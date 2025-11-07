<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Manga;
use App\Notifications\NewChapterNotification;
use App\Support\ChapterPageManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use ZipArchive;

class ChapterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $chapters = Chapter::with('manga')->latest()->paginate(15);

        return view('admin.chapters.index', compact('chapters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $mangas = Manga::orderBy('title')->pluck('title', 'id');

        return view('admin.chapters.create', compact('mangas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'manga_id' => ['required', 'exists:mangas,id'],
            'number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('chapters', 'number')->where(function ($query) use ($request) {
                    return $query->where('manga_id', $request->input('manga_id'));
                }),
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'pages' => ['nullable', 'array'],
            'pages.*' => ['nullable', 'url'],
            'page_files' => ['nullable', 'array'],
            'page_files.*' => ['nullable', 'file', 'image'],
        ]);

        if (isset($validated['pages'])) {
            $validated['pages'] = array_values(array_filter($validated['pages']));
            if (empty($validated['pages'])) {
                $validated['pages'] = null;
            }
        }

        $pages = $validated['pages'] ?? null;
        unset($validated['pages']);

        $chapter = Chapter::create($validated + ['pages' => $pages]);

        if ($request->hasFile('page_files')) {
            $uploadedPages = ChapterPageManager::storeUploadedFiles($chapter, $request->file('page_files'));

            if (! empty($uploadedPages)) {
                $chapter->update(['pages' => array_values($uploadedPages)]);
            }
        }

        $chapter->load('manga.subscribers');

        $subscribers = $chapter->manga->subscribers;

        if ($subscribers->isNotEmpty()) {
            Notification::send($subscribers, new NewChapterNotification($chapter));
        }

        return redirect()->route('admin.chapters.index')
            ->with('status', 'Bölüm başarıyla oluşturuldu.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chapter $chapter): View
    {
        $mangas = Manga::orderBy('title')->pluck('title', 'id');

        return view('admin.chapters.edit', compact('chapter', 'mangas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chapter $chapter): RedirectResponse
    {
        $validated = $request->validate([
            'manga_id' => ['required', 'exists:mangas,id'],
            'number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('chapters', 'number')
                    ->ignore($chapter->id)
                    ->where(function ($query) use ($request) {
                        return $query->where('manga_id', $request->input('manga_id'));
                    }),
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'pages' => ['nullable', 'array'],
            'pages.*' => ['nullable', 'url'],
            'page_files' => ['nullable', 'array'],
            'page_files.*' => ['nullable', 'file', 'image'],
        ]);

        if (isset($validated['pages'])) {
            $validated['pages'] = array_values(array_filter($validated['pages']));
            if (empty($validated['pages'])) {
                $validated['pages'] = null;
            }
        }

        $pages = $validated['pages'] ?? null;
        $hasPagesInput = array_key_exists('pages', $validated);
        unset($validated['pages']);

        $chapter->fill($validated);

        if ($hasPagesInput) {
            $chapter->pages = $pages;
        }

        $chapter->save();

        if ($request->hasFile('page_files')) {
            $uploadedPages = ChapterPageManager::storeUploadedFiles($chapter, $request->file('page_files'));

            if (! empty($uploadedPages)) {
                $chapter->update(['pages' => array_values($uploadedPages)]);
            }
        }

        return redirect()->route('admin.chapters.index')
            ->with('status', 'Bölüm başarıyla güncellendi.');
    }

    public function uploadForm(): View
    {
        $mangas = Manga::orderBy('title')->pluck('title', 'id');

        return view('admin.chapters.bulk-upload', compact('mangas'));
    }

    public function bulkStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'manga_id' => ['required', 'exists:mangas,id'],
            'archive' => ['required', 'file', 'mimes:zip'],
        ]);

        $manga = Manga::findOrFail($validated['manga_id']);
        $archive = $request->file('archive');

        $tempPath = storage_path('app/tmp/'.Str::uuid()->toString());
        FileFacade::makeDirectory($tempPath, 0755, true);

        $zip = new ZipArchive();

        if ($zip->open($archive->getRealPath()) !== true) {
            FileFacade::deleteDirectory($tempPath);

            return back()->withErrors([
                'archive' => 'Arşiv dosyası açılırken bir hata oluştu.',
            ])->withInput();
        }

        $zip->extractTo($tempPath);
        $zip->close();

        $directories = collect(FileFacade::directories($tempPath))
            ->reject(function (string $directory) {
                $name = basename($directory);

                return Str::startsWith($name, '.') || $name === '__MACOSX';
            })
            ->values();

        if ($directories->isEmpty()) {
            FileFacade::deleteDirectory($tempPath);

            return back()->withErrors([
                'archive' => 'Arşiv içerisinde bölüm klasörleri bulunamadı. Her bölüm için ayrı klasörler oluşturun.',
            ])->withInput();
        }

        $directories = $directories->sort(function ($a, $b) {
            return strnatcasecmp(basename($a), basename($b));
        })->values();

        $created = 0;
        $skipped = [];

        foreach ($directories as $directory) {
            $chapterNumber = $this->extractChapterNumber(basename($directory));

            if (! $chapterNumber) {
                $skipped[] = basename($directory).' (bölüm numarası bulunamadı)';

                continue;
            }

            if (Chapter::where('manga_id', $manga->id)->where('number', $chapterNumber)->exists()) {
                $skipped[] = basename($directory).' (zaten mevcut)';

                continue;
            }

            $imageFiles = $this->gatherImageFiles($directory);

            if (empty($imageFiles)) {
                $skipped[] = basename($directory).' (uygun görsel bulunamadı)';

                continue;
            }

            $chapter = Chapter::create([
                'manga_id' => $manga->id,
                'number' => $chapterNumber,
                'title' => $this->extractChapterTitle(basename($directory)),
                'pages' => null,
            ]);

            $storedPages = ChapterPageManager::storeLocalFiles($chapter, $imageFiles);

            if (! empty($storedPages)) {
                $chapter->update(['pages' => array_values($storedPages)]);
                $created++;
            } else {
                $chapter->delete();
                $skipped[] = basename($directory).' (görseller kaydedilemedi)';
            }
        }

        FileFacade::deleteDirectory($tempPath);

        $message = $created.' bölüm yüklendi.';

        if (! empty($skipped)) {
            $message .= ' Atlama nedeni: '.implode(', ', $skipped).'.';
        }

        return redirect()->route('admin.chapters.index')->with('status', $message);
    }

    /**
     * Extract the chapter number from a directory name.
     */
    protected function extractChapterNumber(string $directoryName): ?int
    {
        if (preg_match('/(\d+)/', $directoryName, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * Derive a human readable title from the directory name.
     */
    protected function extractChapterTitle(string $directoryName): ?string
    {
        $clean = trim(preg_replace('/[\d_\-]+/', ' ', $directoryName));
        $clean = preg_replace('/\s+/', ' ', $clean ?? '');

        if (empty($clean)) {
            return null;
        }

        return Str::title($clean);
    }

    /**
     * Gather all image files under the provided directory.
     *
     * @return array<int, string>
     */
    protected function gatherImageFiles(string $directory): array
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'avif'];

        return collect(FileFacade::files($directory))
            ->filter(function ($file) use ($allowedExtensions) {
                $extension = strtolower($file->getExtension());

                return in_array($extension, $allowedExtensions, true);
            })
            ->map(function ($file) {
                return $file->getRealPath();
            })
            ->values()
            ->all();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chapter $chapter): RedirectResponse
    {
        $chapter->delete();

        return redirect()->route('admin.chapters.index')
            ->with('status', 'Bölüm başarıyla silindi.');
    }
}

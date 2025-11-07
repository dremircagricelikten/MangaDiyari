<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\OptimizeMangaCoverImage;
use App\Models\Manga;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MangaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $mangas = Manga::latest()->paginate(10);

        return view('admin.mangas.index', compact('mangas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.mangas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:mangas,slug'],
            'summary' => ['nullable', 'string'],
            'genres' => ['nullable', 'string'],
            'status' => ['required', 'string', 'max:50'],
            'author' => ['nullable', 'string', 'max:255'],
            'artist' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $disk = config('media.disk', config('filesystems.default'));

        if ($request->hasFile('cover_image')) {
            $validated['cover_image_path'] = $request->file('cover_image')->store('covers', $disk);

            if (config('media.optimize')) {
                OptimizeMangaCoverImage::dispatch($validated['cover_image_path'], $disk)->onQueue('media');
            }
        }

        unset($validated['cover_image']);

        if (array_key_exists('genres', $validated)) {
            $validated['genres'] = $this->prepareGenres($validated['genres']);
        }

        Manga::create($validated);

        return redirect()->route('admin.mangas.index')
            ->with('status', 'Manga başarıyla oluşturuldu.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Manga $manga): View
    {
        return view('admin.mangas.edit', compact('manga'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Manga $manga): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:mangas,slug,' . $manga->id],
            'summary' => ['nullable', 'string'],
            'genres' => ['nullable', 'string'],
            'status' => ['required', 'string', 'max:50'],
            'author' => ['nullable', 'string', 'max:255'],
            'artist' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $disk = config('media.disk', config('filesystems.default'));

        if ($request->hasFile('cover_image')) {
            if ($manga->cover_image_path) {
                Storage::disk($disk)->delete($manga->cover_image_path);
            }

            $validated['cover_image_path'] = $request->file('cover_image')->store('covers', $disk);

            if (config('media.optimize')) {
                OptimizeMangaCoverImage::dispatch($validated['cover_image_path'], $disk)->onQueue('media');
            }
        }

        unset($validated['cover_image']);

        if (array_key_exists('genres', $validated)) {
            $validated['genres'] = $this->prepareGenres($validated['genres']);
        }

        $manga->update($validated);

        return redirect()->route('admin.mangas.index')
            ->with('status', 'Manga başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Manga $manga): RedirectResponse
    {
        $disk = config('media.disk', config('filesystems.default'));

        if ($manga->cover_image_path) {
            Storage::disk($disk)->delete($manga->cover_image_path);
        }

        $manga->delete();

        return redirect()->route('admin.mangas.index')
            ->with('status', 'Manga başarıyla silindi.');
    }
    /**
     * @return array<int, string>
     */
    private function prepareGenres(?string $genres): array
    {
        if (blank($genres)) {
            return [];
        }

        return collect(explode(',', $genres))
            ->map(fn (string $genre) => trim($genre))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}

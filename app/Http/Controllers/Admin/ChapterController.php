<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Manga;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

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
        ]);

        if (isset($validated['pages'])) {
            $validated['pages'] = array_values(array_filter($validated['pages']));
            if (empty($validated['pages'])) {
                $validated['pages'] = null;
            }
        }

        Chapter::create($validated);

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
        ]);

        if (isset($validated['pages'])) {
            $validated['pages'] = array_values(array_filter($validated['pages']));
            if (empty($validated['pages'])) {
                $validated['pages'] = null;
            }
        }

        $chapter->update($validated);

        return redirect()->route('admin.chapters.index')
            ->with('status', 'Bölüm başarıyla güncellendi.');
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

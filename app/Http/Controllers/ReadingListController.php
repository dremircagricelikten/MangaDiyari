<?php

namespace App\Http\Controllers;

use App\Models\Manga;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReadingListController extends Controller
{
    public function store(Request $request, Manga $manga): RedirectResponse
    {
        $data = $request->validate([
            'last_read_chapter_number' => ['nullable', 'integer', 'min:0'],
        ]);

        $attributes = [
            'last_read_chapter_number' => $data['last_read_chapter_number'] ?? 0,
            'last_read_at' => isset($data['last_read_chapter_number']) ? now() : null,
        ];

        $user = $request->user();

        if ($user->readingList()->where('manga_id', $manga->id)->exists()) {
            $user->readingList()->updateExistingPivot($manga->id, $attributes);
        } else {
            $user->readingList()->attach($manga->id, $attributes);
        }

        return back()->with('status', 'Manga okuma listene eklendi.');
    }

    public function update(Request $request, Manga $manga): RedirectResponse
    {
        $data = $request->validate([
            'last_read_chapter_number' => ['required', 'integer', 'min:0'],
        ]);

        $attributes = [
            'last_read_chapter_number' => $data['last_read_chapter_number'],
            'last_read_at' => now(),
        ];

        $user = $request->user();

        if ($user->readingList()->where('manga_id', $manga->id)->doesntExist()) {
            $user->readingList()->attach($manga->id, $attributes);
        } else {
            $user->readingList()->updateExistingPivot($manga->id, $attributes);
        }

        return back()->with('status', 'Okuma ilerlemesi güncellendi.');
    }

    public function destroy(Request $request, Manga $manga): RedirectResponse
    {
        $request->user()->readingList()->detach($manga->id);

        return back()->with('status', 'Manga okuma listesinden çıkarıldı.');
    }
}

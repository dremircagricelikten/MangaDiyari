<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Manga;
use Illuminate\Contracts\View\View;

class ChapterController extends Controller
{
    public function show(Manga $manga, string $number): View
    {
        $chapter = $manga->chapters()
            ->where('number', $number)
            ->with(['comments' => function ($query) {
                $query->latest()->with('user');
            }])
            ->firstOrFail();

        $previousChapter = $manga->chapters()
            ->where('number', '<', $chapter->number)
            ->orderByDesc('number')
            ->first();

        $nextChapter = $manga->chapters()
            ->where('number', '>', $chapter->number)
            ->orderBy('number')
            ->first();

        return view('chapters.show', [
            'manga' => $manga,
            'chapter' => $chapter,
            'previousChapter' => $previousChapter,
            'nextChapter' => $nextChapter,
        ]);
    }
}

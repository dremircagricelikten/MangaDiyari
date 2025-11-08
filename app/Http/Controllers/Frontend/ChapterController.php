<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\ReactionType;
use App\Http\Controllers\Controller;
use App\Models\Manga;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ChapterController extends Controller
{
    public function show(Manga $manga, string $number): View
    {
        $chapter = $manga->chapters()
            ->where('number', $number)
            ->with('manga:id,slug,title')
            ->firstOrFail();

        $userId = Auth::id();

        $reactionCountQueries = [];
        foreach (ReactionType::cases() as $reactionType) {
            $reactionCountQueries["reactions as {$reactionType->countColumn()}"] = fn ($query) => $query->where('type', $reactionType->value);
        }

        $commentsQuery = $chapter->comments()
            ->latest()
            ->with(['user:id,name'])
            ->withCount($reactionCountQueries)
            ->limit(50);

        if ($userId) {
            $commentsQuery->with(['reactions' => fn ($query) => $query->where('user_id', $userId)]);
        }

        $comments = $commentsQuery->get();

        if (! $userId) {
            $comments->each->setRelation('reactions', collect());
        }

        $chapter->setRelation('comments', $comments);

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

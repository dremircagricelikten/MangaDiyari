<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\ReactionType;
use App\Http\Controllers\Controller;
use App\Models\Manga;
use App\Support\Cache\MangaCache;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class MangaController extends Controller
{
    public function show(Manga $manga): View
    {
        $chapters = MangaCache::rememberChapters($manga->getKey(), function () use ($manga) {
            return $manga->chapters()->select(['id', 'manga_id', 'number', 'title', 'pages'])->orderByDesc('number')->get();
        });

        $manga->setRelation('chapters', $chapters);

        $userId = Auth::id();

        $reactionCountQueries = [];
        foreach (ReactionType::cases() as $reactionType) {
            $reactionCountQueries["reactions as {$reactionType->countColumn()}"] = fn ($query) => $query->where('type', $reactionType->value);
        }

        $commentsQuery = $manga->comments()
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

        $manga->setRelation('comments', $comments);

        $isSubscribed = $userId
            ? Auth::user()->subscribedMangas()->where('manga_id', $manga->id)->exists()
            : false;

        return view('mangas.show', [
            'manga' => $manga,
            'isSubscribed' => $isSubscribed,
        ]);
    }
}

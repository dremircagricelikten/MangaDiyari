<?php

namespace App\Http\Controllers\Frontend;

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
            return $manga->chapters()->orderByDesc('number')->get();
        });

        $manga->setRelation('chapters', $chapters);

        return view('mangas.show', [
            'manga' => $manga,
            'isSubscribed' => $isSubscribed,
        ]);
    }
}

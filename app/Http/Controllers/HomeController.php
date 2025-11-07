<?php

namespace App\Http\Controllers;

use App\Models\Manga;
use App\Support\Cache\MangaCache;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $popularMangas = MangaCache::rememberHomePopular(function () {
            return Manga::withCount('chapters')
                ->orderByDesc('chapters_count')
                ->latest()
                ->take(6)
                ->get();
        });

        $latestMangas = MangaCache::rememberHomeLatest(function () {
            return Manga::latest()->take(6)->get();
        });

        return view('welcome', [
            'popularMangas' => $popularMangas,
            'latestMangas' => $latestMangas,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Manga;
use App\Support\Cache\MangaCache;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featuredMangas = MangaCache::rememberHomeFeatured(function () {
            return Manga::query()
                ->whereNotNull('cover_image_path')
                ->withCount('chapters')
                ->latest('updated_at')
                ->take(5)
                ->get();
        });

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
            'featuredMangas' => $featuredMangas,
            'popularMangas' => $popularMangas,
            'latestMangas' => $latestMangas,
        ]);
    }
}

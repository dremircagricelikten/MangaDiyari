<?php

namespace App\Observers;

use App\Models\Manga;
use App\Support\Cache\MangaCache;

class MangaObserver
{
    public function saved(Manga $manga): void
    {
        MangaCache::forgetHomeListings();
        MangaCache::forgetSearchFilters();
        MangaCache::forgetChapters($manga->getKey());
    }

    public function deleted(Manga $manga): void
    {
        MangaCache::forgetHomeListings();
        MangaCache::forgetSearchFilters();
        MangaCache::forgetChapters($manga->getKey());
    }
}

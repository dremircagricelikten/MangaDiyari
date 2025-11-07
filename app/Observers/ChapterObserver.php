<?php

namespace App\Observers;

use App\Models\Chapter;
use App\Support\Cache\MangaCache;

class ChapterObserver
{
    public function saved(Chapter $chapter): void
    {
        $this->clearMangaCaches($chapter);
    }

    public function deleted(Chapter $chapter): void
    {
        $this->clearMangaCaches($chapter);
    }

    protected function clearMangaCaches(Chapter $chapter): void
    {
        if ($chapter->manga_id) {
            MangaCache::forgetChapters($chapter->manga_id);
        }

        MangaCache::forgetHomeListings();
    }
}

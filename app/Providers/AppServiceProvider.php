<?php

namespace App\Providers;

use App\Models\Chapter;
use App\Models\Manga;
use App\Observers\ChapterObserver;
use App\Observers\MangaObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Manga::observe(MangaObserver::class);
        Chapter::observe(ChapterObserver::class);
    }
}

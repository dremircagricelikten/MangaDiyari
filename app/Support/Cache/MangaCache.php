<?php

namespace App\Support\Cache;

use Illuminate\Support\Facades\Cache;

class MangaCache
{
    public const HOME_POPULAR_KEY = 'home:popular_mangas';
    public const HOME_LATEST_KEY = 'home:latest_mangas';
    public const SEARCH_STATUSES_KEY = 'search:available_statuses';
    public const SEARCH_GENRES_KEY = 'search:available_genres';

    public static function rememberHomePopular(callable $callback)
    {
        return Cache::remember(
            self::HOME_POPULAR_KEY,
            now()->addSeconds(config('manga.cache.home_ttl', 600)),
            $callback
        );
    }

    public static function rememberHomeLatest(callable $callback)
    {
        return Cache::remember(
            self::HOME_LATEST_KEY,
            now()->addSeconds(config('manga.cache.home_ttl', 600)),
            $callback
        );
    }

    public static function rememberSearchStatuses(callable $callback)
    {
        return Cache::remember(
            self::SEARCH_STATUSES_KEY,
            now()->addSeconds(config('manga.cache.search_filters_ttl', 3600)),
            $callback
        );
    }

    public static function rememberSearchGenres(callable $callback)
    {
        return Cache::remember(
            self::SEARCH_GENRES_KEY,
            now()->addSeconds(config('manga.cache.search_filters_ttl', 3600)),
            $callback
        );
    }

    public static function chaptersCacheKey(int|string $mangaId): string
    {
        return "mangas:{$mangaId}:chapters";
    }

    public static function rememberChapters(int|string $mangaId, callable $callback)
    {
        return Cache::remember(
            self::chaptersCacheKey($mangaId),
            now()->addSeconds(config('manga.cache.chapters_ttl', 600)),
            $callback
        );
    }

    public static function forgetHomeListings(): void
    {
        Cache::forget(self::HOME_POPULAR_KEY);
        Cache::forget(self::HOME_LATEST_KEY);
    }

    public static function forgetSearchFilters(): void
    {
        Cache::forget(self::SEARCH_STATUSES_KEY);
        Cache::forget(self::SEARCH_GENRES_KEY);
    }

    public static function forgetChapters(int|string $mangaId): void
    {
        Cache::forget(self::chaptersCacheKey($mangaId));
    }
}

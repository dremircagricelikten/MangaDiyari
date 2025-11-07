<?php

return [
    'cache' => [
        'home_ttl' => (int) env('MANGA_CACHE_HOME_TTL', 600),
        'chapters_ttl' => (int) env('MANGA_CACHE_CHAPTERS_TTL', 600),
        'search_filters_ttl' => (int) env('MANGA_CACHE_SEARCH_FILTERS_TTL', 3600),
    ],

    'import' => [
        'batch_size' => (int) env('MANGA_IMPORT_BATCH_SIZE', 100),
    ],
];

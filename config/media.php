<?php

return [
    'disk' => env('MEDIA_DISK', env('FILESYSTEM_DISK', 'public')),

    'cdn_url' => env('MEDIA_CDN_URL'),

    'optimize' => env('MEDIA_OPTIMIZE', true),

    'cover_max_width' => env('MEDIA_COVER_MAX_WIDTH', 1200),

    'cover_quality' => env('MEDIA_COVER_QUALITY', 80),

    'png_compression' => env('MEDIA_PNG_COMPRESSION', 6),
];

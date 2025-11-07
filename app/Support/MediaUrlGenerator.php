<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class MediaUrlGenerator
{
    public static function fromPath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $disk = config('media.disk', config('filesystems.default'));
        $cdnUrl = config('media.cdn_url');

        if ($cdnUrl) {
            return rtrim($cdnUrl, '/').'/'.ltrim($path, '/');
        }

        return Storage::disk($disk)->url($path);
    }
}

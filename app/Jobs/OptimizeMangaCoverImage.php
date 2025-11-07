<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OptimizeMangaCoverImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $queue = 'media';

    public function __construct(private readonly string $path, private readonly ?string $disk = null)
    {
    }

    public function handle(): void
    {
        if (! function_exists('imagecreatefromstring')) {
            return;
        }

        $disk = $this->disk ?? config('media.disk', config('filesystems.default'));
        $storage = Storage::disk($disk);

        if (! $storage->exists($this->path)) {
            return;
        }

        try {
            $contents = $storage->get($this->path);
            $image = @imagecreatefromstring($contents);

            if (! $image) {
                return;
            }

            $width = imagesx($image);
            $height = imagesy($image);
            $maxWidth = (int) config('media.cover_max_width', 1200);

            if ($maxWidth > 0 && $width > $maxWidth) {
                $ratio = $maxWidth / $width;
                $newWidth = $maxWidth;
                $newHeight = (int) round($height * $ratio);
                $scalingMode = defined('IMG_BICUBIC_FIXED') ? IMG_BICUBIC_FIXED : (defined('IMG_BILINEAR_FIXED') ? IMG_BILINEAR_FIXED : IMG_NEAREST_NEIGHBOUR);
                $resized = imagescale($image, $newWidth, $newHeight, $scalingMode);

                if ($resized !== false) {
                    imagedestroy($image);
                    $image = $resized;
                }
            }

            $extension = strtolower(pathinfo($this->path, PATHINFO_EXTENSION));
            $buffer = $this->encodeImage($image, $extension);
            imagedestroy($image);

            if ($buffer !== null) {
                $storage->put($this->path, $buffer);
            }
        } catch (\Throwable $exception) {
            Log::warning('Failed to optimize cover image.', [
                'path' => $this->path,
                'disk' => $disk,
                'exception' => $exception->getMessage(),
            ]);
        }
    }

    private function encodeImage($image, string $extension): ?string
    {
        ob_start();

        try {
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    imagejpeg($image, null, (int) config('media.cover_quality', 80));
                    break;
                case 'png':
                    imagepng($image, null, (int) config('media.png_compression', 6));
                    break;
                case 'webp':
                    if (function_exists('imagewebp')) {
                        imagewebp($image, null, (int) config('media.cover_quality', 80));
                        break;
                    }
                    // Fallback to jpeg if webp is not supported.
                    imagejpeg($image, null, (int) config('media.cover_quality', 80));
                    $extension = 'jpg';
                    break;
                default:
                    imagejpeg($image, null, (int) config('media.cover_quality', 80));
                    break;
            }
        } catch (\Throwable $exception) {
            ob_end_clean();

            return null;
        }

        return ob_get_clean() ?: null;
    }
}

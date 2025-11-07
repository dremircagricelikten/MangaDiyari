<?php

namespace App\Support;

use App\Models\Chapter;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ChapterPageManager
{
    /**
     * Store uploaded page files for the given chapter.
     *
     * @param  array<int, UploadedFile|null>  $files
     * @return array<int, string>
     */
    public static function storeUploadedFiles(Chapter $chapter, array $files): array
    {
        $validFiles = array_filter($files, function ($file) {
            return $file instanceof UploadedFile && $file->isValid();
        });

        if (empty($validFiles)) {
            return [];
        }

        usort($validFiles, function (UploadedFile $a, UploadedFile $b) {
            return strnatcasecmp($a->getClientOriginalName(), $b->getClientOriginalName());
        });

        $disk = self::disk();
        $directory = self::chapterDirectory($chapter);

        Storage::disk($disk)->deleteDirectory($directory);
        Storage::disk($disk)->makeDirectory($directory);

        $paths = [];

        foreach ($validFiles as $index => $file) {
            $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
            $filename = self::buildFileName($index, $extension);
            $paths[] = $file->storeAs($directory, $filename, $disk);
        }

        return $paths;
    }

    /**
     * Store local files that were extracted from an archive.
     *
     * @param  array<int, string>  $filePaths
     * @return array<int, string>
     */
    public static function storeLocalFiles(Chapter $chapter, array $filePaths): array
    {
        if (empty($filePaths)) {
            return [];
        }

        natsort($filePaths);

        $disk = self::disk();
        $directory = self::chapterDirectory($chapter);

        Storage::disk($disk)->deleteDirectory($directory);
        Storage::disk($disk)->makeDirectory($directory);

        $paths = [];

        foreach ($filePaths as $index => $path) {
            if (! is_file($path)) {
                continue;
            }

            $file = new File($path);
            $extension = strtolower($file->getExtension() ?: $file->guessExtension() ?: 'jpg');
            $filename = self::buildFileName($index, $extension);
            $storedPath = Storage::disk($disk)->putFileAs($directory, $file, $filename);

            if ($storedPath) {
                $paths[] = $storedPath;
            }
        }

        return $paths;
    }

    public static function deleteStoredPages(Chapter $chapter): void
    {
        $disk = self::disk();
        $directory = self::chapterDirectory($chapter);

        Storage::disk($disk)->deleteDirectory($directory);
    }

    protected static function disk(): string
    {
        return config('media.disk', config('filesystems.default'));
    }

    protected static function chapterDirectory(Chapter $chapter): string
    {
        return 'chapters/'.(string) $chapter->id;
    }

    protected static function buildFileName(int $index, string $extension): string
    {
        $position = str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);

        return $position.'.'.$extension;
    }
}

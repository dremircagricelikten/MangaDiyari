<?php

namespace App\Console\Commands;

use App\Jobs\ImportMangaBatch;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImportMangaCommand extends Command
{
    protected $signature = 'mangas:import {path : JSON veya CSV dosya yolu} {--chunk= : Kuyruğa gönderilecek kayıt sayısı}';

    protected $description = 'Manga kayıtlarını kuyruğa alarak içe aktarır.';

    public function handle(): int
    {
        $path = $this->argument('path');

        if (! File::exists($path)) {
            $this->error('Belirtilen dosya bulunamadı.');

            return self::FAILURE;
        }

        $chunkSize = (int) ($this->option('chunk') ?: config('manga.import.batch_size', 100));
        $chunkSize = max($chunkSize, 1);

        $records = $this->loadRecords($path);

        if ($records->isEmpty()) {
            $this->warn('İçe aktarılacak kayıt bulunamadı.');

            return self::SUCCESS;
        }

        $records->chunk($chunkSize)->each(function (Collection $chunk) {
            ImportMangaBatch::dispatch($chunk->map(function ($item) {
                return is_array($item) ? $item : (array) $item;
            })->toArray());
        });

        $this->info(sprintf('%d kayıt kuyruklara gönderildi.', $records->count()));

        return self::SUCCESS;
    }

    private function loadRecords(string $path): Collection
    {
        $extension = Str::lower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($extension) {
            'json' => $this->loadJson($path),
            'csv' => $this->loadCsv($path),
            default => collect(),
        };
    }

    private function loadJson(string $path): Collection
    {
        $contents = File::get($path);
        $decoded = json_decode($contents, true);

        return collect(is_array($decoded) ? $decoded : []);
    }

    private function loadCsv(string $path): Collection
    {
        $rows = collect();
        if (($handle = fopen($path, 'r')) === false) {
            return $rows;
        }

        $headers = null;
        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            if ($headers === null) {
                $headers = $data;
                continue;
            }

            if (count($headers) !== count($data)) {
                continue;
            }

            $rows->push(array_combine($headers, $data));
        }

        fclose($handle);

        return $rows->filter(fn ($row) => is_array($row));
    }
}

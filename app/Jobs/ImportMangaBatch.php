<?php

namespace App\Jobs;

use App\Models\Manga;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ImportMangaBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $queue = 'imports';

    /**
     * @param array<int, array<string, mixed>> $payload
     */
    public function __construct(private readonly array $payload)
    {
    }

    public function handle(): void
    {
        foreach ($this->payload as $row) {
            $attributes = $this->normalizeAttributes($row);

            if (! isset($attributes['slug'])) {
                continue;
            }

            try {
                Manga::updateOrCreate(
                    ['slug' => $attributes['slug']],
                    Arr::except($attributes, ['slug'])
                );
            } catch (\Throwable $exception) {
                Log::error('Failed to import manga record.', [
                    'slug' => $attributes['slug'] ?? null,
                    'exception' => $exception->getMessage(),
                ]);
            }
        }
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function normalizeAttributes(array $row): array
    {
        $attributes = Arr::only($row, [
            'title',
            'slug',
            'summary',
            'genres',
            'status',
            'author',
            'artist',
            'published_at',
            'cover_image_path',
        ]);

        if (isset($attributes['genres'])) {
            if (is_string($attributes['genres'])) {
                $attributes['genres'] = collect(explode(',', $attributes['genres']))
                    ->map(fn ($genre) => trim((string) $genre))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();
            } elseif (is_array($attributes['genres'])) {
                $attributes['genres'] = collect($attributes['genres'])
                    ->map(fn ($genre) => trim((string) $genre))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();
            }
        }

        if (isset($attributes['published_at'])) {
            $date = null;

            if ($attributes['published_at'] instanceof Carbon) {
                $date = $attributes['published_at'];
            } elseif (is_string($attributes['published_at']) && $attributes['published_at'] !== '') {
                try {
                    $date = Carbon::parse($attributes['published_at']);
                } catch (\Throwable $exception) {
                    Log::warning('Unable to parse published_at value during import.', [
                        'value' => $attributes['published_at'],
                        'exception' => $exception->getMessage(),
                    ]);
                }
            }

            $attributes['published_at'] = $date;
        }

        return $attributes;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

class Chapter extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'manga_id',
        'number',
        'title',
        'pages',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'pages' => 'array',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Chapter $chapter) {
            $disk = config('media.disk', config('filesystems.default'));
            $directory = 'chapters/'.(string) $chapter->id;

            Storage::disk($disk)->deleteDirectory($directory);
        });
    }

    /**
     * Get the manga that owns the chapter.
     */
    public function manga(): BelongsTo
    {
        return $this->belongsTo(Manga::class);
    }

    /**
     * Get the comments that belong to the chapter.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}

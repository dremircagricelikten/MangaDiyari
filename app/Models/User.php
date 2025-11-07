<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Mangas that the user has marked as favorite.
     */
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Manga::class, 'favorites')->withTimestamps();
    }

    /**
     * Mangas that belong to the user's reading list.
     */
    public function readingList(): BelongsToMany
    {
        return $this->belongsToMany(Manga::class, 'reading_list')
            ->withPivot(['last_read_chapter_number', 'last_read_at'])
            ->withTimestamps();
    }
}

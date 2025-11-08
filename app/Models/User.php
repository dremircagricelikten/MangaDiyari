<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'is_admin',
        'role',
        'theme_preference',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
        'theme_preference' => 'string',
    ];

    protected static function booted(): void
    {
        static::saving(function (User $user) {
            if ($user->role instanceof UserRole) {
                $user->attributes['is_admin'] = $user->role === UserRole::ADMIN;
            }
        });
    }

    public function getIsAdminAttribute($value): bool
    {
        if ($this->role instanceof UserRole) {
            return $this->role === UserRole::ADMIN;
        }

        return (bool) $value;
    }

    public function hasRole(UserRole|string $role): bool
    {
        $roleValue = $role instanceof UserRole ? $role : UserRole::from($role);

        return $this->role === $roleValue;
    }

    public function hasAnyRole(UserRole|string ...$roles): bool
    {
        return collect($roles)
            ->map(fn ($role) => $role instanceof UserRole ? $role : UserRole::from($role))
            ->contains(fn (UserRole $role) => $this->role === $role);
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(CommentReaction::class);
    }

    /**
     * Get the comments written by the user.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Manga::class, 'favorites')->withTimestamps();
    }

    public function readingList(): BelongsToMany
    {
        return $this->belongsToMany(Manga::class, 'reading_list')
            ->withPivot(['last_read_chapter_number', 'last_read_at'])
            ->withTimestamps();
    }

    /**
     * Get the mangas the user subscribed to.
     */
    public function subscribedMangas(): BelongsToMany
    {
        return $this->belongsToMany(Manga::class, 'manga_subscriptions')->withTimestamps();
    }
}

<?php

namespace App\Enums;

enum ReactionType: string
{
    case LIKE = 'like';
    case LOVE = 'love';
    case WOW = 'wow';

    public function label(): string
    {
        return match ($this) {
            self::LIKE => 'Beğen',
            self::LOVE => 'Bayıldım',
            self::WOW => 'Şaşırdım',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::LIKE => 'hand-thumbs-up',
            self::LOVE => 'heart-fill',
            self::WOW => 'emoji-smile',
        };
    }

    public function countColumn(): string
    {
        return sprintf('%s_count', $this->value);
    }
}

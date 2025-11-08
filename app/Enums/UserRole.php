<?php

namespace App\Enums;

enum UserRole: string
{
    case USER = 'user';
    case MODERATOR = 'moderator';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::USER => 'Kullanıcı',
            self::MODERATOR => 'Moderatör',
            self::ADMIN => 'Yönetici',
        };
    }
}

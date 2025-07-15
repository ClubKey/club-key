<?php

declare(strict_types=1);

namespace App\Enums;

enum AppRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case USER = 'user';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Administrator',
            self::USER => 'User',
        };
    }

    public function isSuperAdmin(): bool
    {
        return in_array($this, [self::SUPER_ADMIN]);
    }

    public function canManageClub(): bool
    {
        return in_array($this, [self::SUPER_ADMIN]);
    }
}

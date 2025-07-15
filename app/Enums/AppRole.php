<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\EnumTrait;

enum AppRole: string
{
    use EnumTrait;

    case SUPER_ADMIN = 'super_admin';
    case CLUB_ADMIN = 'club_admin';
    case USER = 'user';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Administrator',
            self::CLUB_ADMIN => 'Club Administrator',
            self::USER => 'User',
        };
    }

    public function isSuperAdmin(): bool
    {
        return in_array($this, [self::SUPER_ADMIN]);
    }

    public function canManageAClub(): bool
    {
        return in_array($this, [self::SUPER_ADMIN, self::CLUB_ADMIN]);
    }
}

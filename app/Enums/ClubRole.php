<?php

declare(strict_types=1);

namespace App\Enums;

enum ClubRole: string
{
    case ADMIN = 'admin';
    case MEMBER = 'member';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::MEMBER => 'Member',
        };
    }

    public function isAdmin(): bool
    {
        return in_array($this, [self::ADMIN]);
    }

    public function canManageClub(): bool
    {
        return in_array($this, [self::ADMIN]);
    }
}

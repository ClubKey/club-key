<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\EnumTrait;

enum Topic: string
{
    use EnumTrait;

    case PROMOTIONS = 'promotions';
    case NOTIFICATIONS = 'notifications';

    public function label(): string
    {
        return match ($this) {
            self::PROMOTIONS => 'Promotions',
            self::NOTIFICATIONS => 'Notifications',
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\EnumTrait;

enum SubscriptionStatus: string
{
    use EnumTrait;

    case SUBSCRIBED = 'subscribed';
    case UNSUBSCRIBED = 'unsubscribed';
    case PENDING = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::SUBSCRIBED => 'Subscribed',
            self::UNSUBSCRIBED => 'Unsubscribed',
            self::PENDING => 'Pending',
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\EnumTrait;

enum SubscriptionEventType: string
{
    use EnumTrait;

    case SUBSCRIBED = 'subscribed';
    case UNSUBSCRIBED = 'unsubscribed';
    case RESUBSCRIBED = 'resubscribed';
    case BOUNCED = 'bounced';
    case COMPLAINT = 'complaint';

    public function label(): string
    {
        return match ($this) {
            self::SUBSCRIBED => 'Subscribed',
            self::UNSUBSCRIBED => 'Unsubscribed',
            self::RESUBSCRIBED => 'Resubscribed',
            self::BOUNCED => 'Bounced',
            self::COMPLAINT => 'Complaint',
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\EnumTrait;

enum ContactType: string
{
    use EnumTrait;

    case SMS = 'sms';
    case EMAIL = 'email';
    case PUSH = 'push';
    case WHATSAPP = 'whatsapp';

    public function label(): string
    {
        return match ($this) {
            self::SMS => 'SMS',
            self::EMAIL => 'Email',
            self::PUSH => 'Push Notification',
            self::WHATSAPP => 'WhatsApp',
        };
    }
}

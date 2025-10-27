<?php

declare(strict_types=1);

namespace App\Enums;

enum SmsDirection: string
{
    case OUTBOUND = 'outbound';
    case INBOUND = 'inbound';
}

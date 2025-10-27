<?php

declare(strict_types=1);

namespace App\Enums;

enum SmsMessageStatus: string
{
    case QUEUED = 'queued';
    case SENDING = 'sending';
    case SENT = 'sent';
    case DELIVERED = 'delivered';
    case FAILED = 'failed';
    case RECEIVED = 'received';
}

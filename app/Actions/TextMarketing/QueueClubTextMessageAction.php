<?php

declare(strict_types=1);

namespace App\Actions\TextMarketing;

use App\Models\Club;
use App\Models\SmsMessage;
use App\Models\TextSubscription;
use App\Services\TextMarketing\TextMarketingService;

final readonly class QueueClubTextMessageAction
{
    public function __construct(private TextMarketingService $service) {}

    public function handle(Club $club, TextSubscription $subscription, string $body, array $metadata = []): SmsMessage
    {
        return $this->service->queueMessage($club, $subscription, $body, $metadata);
    }
}

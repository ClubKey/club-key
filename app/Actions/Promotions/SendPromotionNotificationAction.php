<?php

declare(strict_types=1);

namespace App\Actions\Promotions;

use App\Enums\Topic;
use App\Jobs\SendSubscriptionPromotionNotification;
use App\Models\CommunicationSubscription;
use App\Models\Promotion;
use Exception;

final readonly class SendPromotionNotificationAction
{
    /**
     * Execute the action.
     */
    public function handle(Promotion $promotion): void
    {
        $index = 0;

        CommunicationSubscription::query()
            ->where('club_id', $promotion->club_id)
            ->subscribed()
            ->forTopic(Topic::PROMOTIONS)
            ->chunkById(100, function ($subscriptions) use ($promotion, &$index): void {
                foreach ($subscriptions as $subscription) {
                    try {
                        SendSubscriptionPromotionNotification::dispatch($subscription, $promotion)->delay($index);
                        $index++;
                    } catch (Exception $exception) {
                        report($exception);
                    }
                }
            });
    }
}

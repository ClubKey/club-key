<?php

declare(strict_types=1);

namespace App\Actions\CommunicationSubscriptions;

use App\Enums\SubscriptionEventType;
use App\Enums\SubscriptionStatus;
use App\Models\CommunicationSubscription;
use App\Models\CommunicationSubscriptionEvent;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class ResubscribeAction
{
    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(
        CommunicationSubscription $subscription,
        ?string $source = null
    ): CommunicationSubscription {
        return DB::transaction(function () use ($subscription, $source): CommunicationSubscription {
            $now = now();

            $subscription->update([
                'status' => SubscriptionStatus::SUBSCRIBED,
                'subscribed_at' => $now,
                'unsubscribed_at' => null,
                'unsubscribe_reason' => null,
            ]);

            CommunicationSubscriptionEvent::create([
                'subscription_id' => $subscription->id,
                'event_type' => SubscriptionEventType::RESUBSCRIBED,
                'occurred_at' => $now,
                'source' => $source,
            ]);

            return $subscription->fresh();
        });
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\CommunicationSubscriptions;

use App\Enums\SubscriptionEventType;
use App\Models\CommunicationSubscription;
use App\Models\CommunicationSubscriptionEvent;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UnsubscribeAction
{
    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(
        CommunicationSubscription $subscription,
        ?string $reason = null,
        ?string $source = null,
        ?string $providerMessageId = null,
        ?array $providerPayload = null,
        ?CarbonImmutable $at = null
    ): CommunicationSubscription {
        return DB::transaction(function () use ($subscription, $reason, $source, $providerMessageId, $providerPayload, $at): CommunicationSubscription {
            $unsubscribeAt = $at ?? now();

            $subscription->update([
                'status' => \App\Enums\SubscriptionStatus::UNSUBSCRIBED,
                'unsubscribed_at' => $unsubscribeAt,
                'unsubscribe_reason' => $reason,
                'provider_message_id' => $providerMessageId ?? $subscription->provider_message_id,
            ]);

            CommunicationSubscriptionEvent::create([
                'subscription_id' => $subscription->id,
                'event_type' => SubscriptionEventType::UNSUBSCRIBED,
                'occurred_at' => $unsubscribeAt,
                'source' => $source,
                'provider_payload' => $providerPayload,
                'note' => $reason,
            ]);

            return $subscription->fresh();
        });
    }
}

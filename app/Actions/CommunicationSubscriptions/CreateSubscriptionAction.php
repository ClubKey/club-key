<?php

declare(strict_types=1);

namespace App\Actions\CommunicationSubscriptions;

use App\Enums\ContactType;
use App\Enums\SubscriptionEventType;
use App\Enums\SubscriptionStatus;
use App\Enums\Topic;
use App\Models\Club;
use App\Models\CommunicationSubscription;
use App\Models\CommunicationSubscriptionEvent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateSubscriptionAction
{
    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(
        Club $club,
        ContactType $contactType,
        string $contactValue,
        Topic $topic,
        ?User $user = null,
        ?string $source = null,
        ?array $consentProof = null
    ): CommunicationSubscription {
        return DB::transaction(function () use ($club, $contactType, $contactValue, $topic, $user, $source, $consentProof): CommunicationSubscription {
            $normalizedContactValue = CommunicationSubscription::normalizeContactValue($contactType, $contactValue);

            $subscription = CommunicationSubscription::updateOrCreate(
                [
                    'contact_type' => $contactType,
                    'contact_value' => $normalizedContactValue,
                    'topic' => $topic,
                    'club_id' => $club->id,
                ],
                [
                    'user_id' => $user?->id,
                    'status' => SubscriptionStatus::SUBSCRIBED,
                    'subscribed_at' => now(),
                    'unsubscribed_at' => null,
                    'unsubscribe_reason' => null,
                    'source' => $source,
                    'consent_proof' => $consentProof,
                ]
            );
            // Create event if this is a new subscription or was previously unsubscribed
            if ($subscription->wasRecentlyCreated || $subscription->getOriginal('status') === SubscriptionStatus::UNSUBSCRIBED->value) {
                CommunicationSubscriptionEvent::create([
                    'subscription_id' => $subscription->id,
                    'event_type' => SubscriptionEventType::SUBSCRIBED,
                    'occurred_at' => now(),
                    'source' => $source,
                ]);
            }

            return $subscription->fresh();
        });
    }
}

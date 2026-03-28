<?php

declare(strict_types=1);

use App\Actions\CommunicationSubscriptions\UnsubscribeAction;
use App\Enums\ContactType;
use App\Enums\SubscriptionStatus;
use App\Enums\Topic;
use App\Models\Club;
use App\Models\CommunicationSubscription;
use App\Models\User;

pest()->group('communication-subscriptions', 'unsubscribe');

test('can unsubscribe from email notifications', function () {
    $club = Club::factory()->create();
    $user = User::factory()->create(['email' => 'test@example.com']);

    $subscription = CommunicationSubscription::create([
        'user_id' => $user->id,
        'club_id' => $club->id,
        'contact_type' => ContactType::EMAIL,
        'contact_value' => 'test@example.com',
        'topic' => Topic::PROMOTIONS,
        'status' => SubscriptionStatus::SUBSCRIBED,
        'subscribed_at' => now(),
    ]);

    $action = new UnsubscribeAction();

    $updated = $action->handle(
        subscription: $subscription,
        reason: 'User requested',
        source: 'email_link'
    );

    expect($updated->status)->toBe(SubscriptionStatus::UNSUBSCRIBED)
        ->and($updated->unsubscribed_at)->not->toBeNull()
        ->and($updated->unsubscribe_reason)->toBe('User requested');

    $this->assertDatabaseHas('communication_subscription_events', [
        'subscription_id' => $subscription->id,
        'event_type' => 'unsubscribed',
        'source' => 'email_link',
    ]);
});

test('can unsubscribe via webhook with provider message ID', function () {
    $club = Club::factory()->create();

    $subscription = CommunicationSubscription::create([
        'club_id' => $club->id,
        'contact_type' => ContactType::SMS,
        'contact_value' => '+12025550198',
        'topic' => Topic::PROMOTIONS,
        'status' => SubscriptionStatus::SUBSCRIBED,
        'subscribed_at' => now(),
    ]);

    $action = new UnsubscribeAction();

    $updated = $action->handle(
        subscription: $subscription,
        reason: 'STOP keyword',
        source: 'vonage_webhook',
        providerMessageId: 'msg-12345',
        providerPayload: ['messageId' => 'msg-12345', 'text' => 'STOP']
    );

    expect($updated->provider_message_id)->toBe('msg-12345');

    $this->assertDatabaseHas('communication_subscription_events', [
        'subscription_id' => $subscription->id,
        'event_type' => 'unsubscribed',
        'source' => 'vonage_webhook',
        'provider_payload' => json_encode(['messageId' => 'msg-12345', 'text' => 'STOP']),
    ]);
});

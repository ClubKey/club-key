<?php

declare(strict_types=1);

use App\Enums\ContactType;
use App\Enums\SubscriptionStatus;
use App\Enums\Topic;
use App\Models\Club;
use App\Models\CommunicationSubscription;

pest()->group('communication-subscriptions', 'vonage-webhook');

test('handles STOP keyword via SMS webhook', function () {
    $club = Club::factory()->create();

    $subscription = CommunicationSubscription::create([
        'club_id' => $club->id,
        'contact_type' => ContactType::SMS,
        'contact_value' => '+12025550198',
        'topic' => Topic::PROMOTIONS,
        'status' => SubscriptionStatus::SUBSCRIBED,
        'subscribed_at' => now(),
    ]);

    $response = $this->postJson(route('webhooks.vonage'), [
        'msisdn' => '+12025550198',
        'text' => 'STOP',
        'messageId' => 'msg-12345',
    ]);

    $response->assertOk()
        ->assertJson(['status' => 'ok']);

    $subscription->refresh();

    expect($subscription->status)->toBe(SubscriptionStatus::UNSUBSCRIBED)
        ->and($subscription->provider_message_id)->toBe('msg-12345');

    $this->assertDatabaseHas('communication_subscription_events', [
        'subscription_id' => $subscription->id,
        'event_type' => 'unsubscribed',
        'source' => 'vonage_webhook',
    ]);
});

test('handles multiple STOP keywords', function () {
    $club = Club::factory()->create();

    $keywords = ['STOP', 'STOPALL', 'UNSUBSCRIBE', 'QUIT', 'END', 'CANCEL'];

    foreach ($keywords as $keyword) {
        $subscription = CommunicationSubscription::create([
            'club_id' => $club->id,
            'contact_type' => ContactType::SMS,
            'contact_value' => '+12025550198',
            'topic' => Topic::PROMOTIONS,
            'status' => SubscriptionStatus::SUBSCRIBED,
            'subscribed_at' => now(),
        ]);

        $response = $this->postJson(route('webhooks.vonage'), [
            'msisdn' => '+12025550198',
            'text' => $keyword,
        ]);

        $response->assertOk();

        $subscription->refresh();
        expect($subscription->status)->toBe(SubscriptionStatus::UNSUBSCRIBED);

        $subscription->delete(); // Clean up for next iteration
    }
});

test('ignores non-STOP messages', function () {
    $club = Club::factory()->create();

    $subscription = CommunicationSubscription::create([
        'club_id' => $club->id,
        'contact_type' => ContactType::SMS,
        'contact_value' => '+12025550198',
        'topic' => Topic::PROMOTIONS,
        'status' => SubscriptionStatus::SUBSCRIBED,
        'subscribed_at' => now(),
    ]);

    $response = $this->postJson(route('webhooks.vonage'), [
        'msisdn' => '+12025550198',
        'text' => 'Hello, how are you?',
    ]);

    $response->assertOk();

    $subscription->refresh();
    expect($subscription->status)->toBe(SubscriptionStatus::SUBSCRIBED);
});

<?php

declare(strict_types=1);

use App\Actions\CommunicationSubscriptions\CreateSubscriptionAction;
use App\Enums\ContactType;
use App\Enums\SubscriptionStatus;
use App\Enums\Topic;
use App\Models\Club;
use App\Models\CommunicationSubscription;
use App\Models\User;

pest()->group('communication-subscriptions', 'create-subscription');

test('can create an email subscription', function () {
    $club = Club::factory()->create();
    $user = User::factory()->create(['email' => 'test@example.com']);

    $action = new CreateSubscriptionAction();

    $subscription = $action->handle(
        club: $club,
        contactType: ContactType::EMAIL,
        contactValue: 'test@example.com',
        topic: Topic::PROMOTIONS,
        user: $user,
        source: 'test'
    );

    expect($subscription)->toBeInstanceOf(CommunicationSubscription::class)
        ->and($subscription->club_id)->toBe($club->id)
        ->and($subscription->contact_type)->toBe(ContactType::EMAIL)
        ->and($subscription->contact_value)->toBe('test@example.com')
        ->and($subscription->topic)->toBe(Topic::PROMOTIONS)
        ->and($subscription->status)->toBe(SubscriptionStatus::SUBSCRIBED)
        ->and($subscription->user_id)->toBe($user->id);

    $this->assertDatabaseHas('communication_subscription_events', [
        'subscription_id' => $subscription->id,
        'event_type' => 'subscribed',
    ]);
});

test('can create an SMS subscription', function () {
    $club = Club::factory()->create();
    $user = User::factory()->create(['phone' => '2025550198']);

    $action = new CreateSubscriptionAction();

    $subscription = $action->handle(
        club: $club,
        contactType: ContactType::SMS,
        contactValue: '2025550198',
        topic: Topic::NOTIFICATIONS,
        user: $user
    );

    expect($subscription->contact_type)->toBe(ContactType::SMS)
        ->and($subscription->contact_value)->toContain('+1')
        ->and($subscription->topic)->toBe(Topic::NOTIFICATIONS);
});

test('normalizes email to lowercase', function () {
    $club = Club::factory()->create();

    $action = new CreateSubscriptionAction();

    $subscription = $action->handle(
        club: $club,
        contactType: ContactType::EMAIL,
        contactValue: 'Test@Example.COM',
        topic: Topic::PROMOTIONS
    );

    expect($subscription->contact_value)->toBe('test@example.com');
});

test('normalizes phone number to E.164 format', function () {
    $club = Club::factory()->create();

    $action = new CreateSubscriptionAction();

    $subscription = $action->handle(
        club: $club,
        contactType: ContactType::SMS,
        contactValue: '(202) 555-0198',
        topic: Topic::PROMOTIONS
    );

    expect($subscription->contact_value)->toBe('+12025550198');
});

test('prevents duplicate subscriptions', function () {
    $club = Club::factory()->create();

    $action = new CreateSubscriptionAction();

    $subscription1 = $action->handle(
        club: $club,
        contactType: ContactType::EMAIL,
        contactValue: 'test@example.com',
        topic: Topic::PROMOTIONS
    );

    $subscription2 = $action->handle(
        club: $club,
        contactType: ContactType::EMAIL,
        contactValue: 'test@example.com',
        topic: Topic::PROMOTIONS
    );

    expect($subscription1->id)->toBe($subscription2->id)
        ->and(CommunicationSubscription::count())->toBe(1);
});

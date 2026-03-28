<?php

declare(strict_types=1);

use App\Enums\ContactType;
use App\Enums\SubscriptionStatus;
use App\Enums\Topic;
use App\Models\Club;
use App\Models\CommunicationSubscription;
use App\Models\Promotion;
use App\Models\User;
use App\Notifications\PromotionPublished;
use Illuminate\Support\Facades\Notification;

pest()->group('communication-subscriptions', 'notification-filtering');

test('notification is sent when user is subscribed', function () {
    Notification::fake();

    $club = Club::factory()->create();
    $user = User::factory()->create([
        'email' => 'subscribed@example.com',
        'phone' => '2025550198',
    ]);

    $club->members()->attach($user->id);

    // Create subscribed subscription
    CommunicationSubscription::create([
        'user_id' => $user->id,
        'club_id' => $club->id,
        'contact_type' => ContactType::EMAIL,
        'contact_value' => 'subscribed@example.com',
        'topic' => Topic::PROMOTIONS,
        'status' => SubscriptionStatus::SUBSCRIBED,
        'subscribed_at' => now(),
    ]);

    $promotion = Promotion::factory()->create(['club_id' => $club->id]);

    $user->notify(new PromotionPublished($promotion));

    Notification::assertSentTo($user, PromotionPublished::class);
});

test('notification is not sent when user is unsubscribed', function () {
    Notification::fake();

    $club = Club::factory()->create();
    $user = User::factory()->create([
        'email' => 'unsubscribed@example.com',
    ]);

    $club->members()->attach($user->id);

    // Create unsubscribed subscription
    CommunicationSubscription::create([
        'user_id' => $user->id,
        'club_id' => $club->id,
        'contact_type' => ContactType::EMAIL,
        'contact_value' => 'unsubscribed@example.com',
        'topic' => Topic::PROMOTIONS,
        'status' => SubscriptionStatus::UNSUBSCRIBED,
        'unsubscribed_at' => now(),
    ]);

    $promotion = Promotion::factory()->create(['club_id' => $club->id]);

    $user->notify(new PromotionPublished($promotion));

    Notification::assertNothingSent();
});

test('notification is not sent when club member has no subscription record', function () {
    Notification::fake();

    $club = Club::factory()->create();
    $user = User::factory()->create([
        'email' => 'nomatch@example.com',
    ]);

    $club->members()->attach($user->id);

    // No subscription record — should NOT send
    $promotion = Promotion::factory()->create(['club_id' => $club->id]);

    $user->notify(new PromotionPublished($promotion));

    Notification::assertNothingSent();
});

test('notification is sent to unlinked subscriber via on-demand routing', function () {
    Notification::fake();

    $club = Club::factory()->create();
    $subscription = CommunicationSubscription::factory()->create([
        'club_id' => $club->id,
        'contact_type' => ContactType::EMAIL,
        'contact_value' => 'unlinked@example.com',
        'topic' => Topic::PROMOTIONS,
        'status' => SubscriptionStatus::SUBSCRIBED,
        'user_id' => null,
    ]);

    $promotion = Promotion::factory()->create(['club_id' => $club->id]);

    \Illuminate\Support\Facades\Notification::route('mail', $subscription->contact_value)
        ->notify(new PromotionPublished($promotion, $subscription));

    Notification::assertSentOnDemand(PromotionPublished::class);
});

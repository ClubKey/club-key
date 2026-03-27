<?php

declare(strict_types=1);

use App\Actions\Promotions\SendPromotionNotificationAction;
use App\Enums\ContactType;
use App\Enums\SubscriptionStatus;
use App\Enums\Topic;
use App\Jobs\SendSubscriptionPromotionNotification;
use App\Models\Club;
use App\Models\CommunicationSubscription;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

pest()->group('promotions', 'send-promotion-notification-action');

it('dispatches a job for each subscribed email subscription', function () {
    Queue::fake();

    $club = Club::factory()->create();
    $promotion = Promotion::factory()->create(['club_id' => $club->id]);
    CommunicationSubscription::factory()->create([
        'club_id' => $club->id,
        'contact_type' => ContactType::EMAIL,
        'topic' => Topic::PROMOTIONS,
        'status' => SubscriptionStatus::SUBSCRIBED,
    ]);

    app(SendPromotionNotificationAction::class)->handle($promotion);

    Queue::assertPushed(SendSubscriptionPromotionNotification::class, 1);
});

it('dispatches a job for each subscribed sms subscription', function () {
    Queue::fake();

    $club = Club::factory()->create();
    $promotion = Promotion::factory()->create(['club_id' => $club->id]);
    CommunicationSubscription::factory()->create([
        'club_id' => $club->id,
        'contact_type' => ContactType::SMS,
        'contact_value' => '+12025550198',
        'topic' => Topic::PROMOTIONS,
        'status' => SubscriptionStatus::SUBSCRIBED,
    ]);

    app(SendPromotionNotificationAction::class)->handle($promotion);

    Queue::assertPushed(SendSubscriptionPromotionNotification::class, 1);
});

it('dispatches jobs for unlinked subscribers with no user account', function () {
    Queue::fake();

    $club = Club::factory()->create();
    $promotion = Promotion::factory()->create(['club_id' => $club->id]);
    CommunicationSubscription::factory()->create([
        'club_id' => $club->id,
        'contact_type' => ContactType::EMAIL,
        'topic' => Topic::PROMOTIONS,
        'status' => SubscriptionStatus::SUBSCRIBED,
        'user_id' => null,
    ]);

    app(SendPromotionNotificationAction::class)->handle($promotion);

    Queue::assertPushed(SendSubscriptionPromotionNotification::class, 1);
});

it('does not dispatch a job for unsubscribed subscriptions', function () {
    Queue::fake();

    $club = Club::factory()->create();
    $promotion = Promotion::factory()->create(['club_id' => $club->id]);
    CommunicationSubscription::factory()->create([
        'club_id' => $club->id,
        'contact_type' => ContactType::EMAIL,
        'topic' => Topic::PROMOTIONS,
        'status' => SubscriptionStatus::UNSUBSCRIBED,
    ]);

    app(SendPromotionNotificationAction::class)->handle($promotion);

    Queue::assertNothingPushed();
});

it('does not dispatch jobs for subscriptions with a different topic', function () {
    Queue::fake();

    $club = Club::factory()->create();
    $promotion = Promotion::factory()->create(['club_id' => $club->id]);
    CommunicationSubscription::factory()->create([
        'club_id' => $club->id,
        'contact_type' => ContactType::EMAIL,
        'topic' => Topic::NOTIFICATIONS,
        'status' => SubscriptionStatus::SUBSCRIBED,
    ]);

    app(SendPromotionNotificationAction::class)->handle($promotion);

    Queue::assertNothingPushed();
});

it('does not dispatch a job for club members with no subscription record', function () {
    Queue::fake();

    $club = Club::factory()->create();
    $user = User::factory()->create();
    $club->members()->attach($user->id);
    $promotion = Promotion::factory()->create(['club_id' => $club->id]);

    // No subscription record for this member

    app(SendPromotionNotificationAction::class)->handle($promotion);

    Queue::assertNothingPushed();
});

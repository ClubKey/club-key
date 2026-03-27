<?php

declare(strict_types=1);

use App\Enums\ContactType;
use App\Enums\SubscriptionStatus;
use App\Enums\Topic;
use App\Jobs\SendSubscriptionPromotionNotification;
use App\Models\Club;
use App\Models\CommunicationSubscription;
use App\Models\Promotion;
use App\Models\User;
use App\Notifications\PromotionPublished;
use Illuminate\Support\Facades\Notification;

pest()->group('promotions', 'send-subscription-promotion-notification');

it('sends a notification to a linked user', function () {
    Notification::fake();

    $club = Club::factory()->create();
    $user = User::factory()->create(['email' => 'linked@example.com']);
    $subscription = CommunicationSubscription::factory()->create([
        'club_id' => $club->id,
        'contact_type' => ContactType::EMAIL,
        'contact_value' => 'linked@example.com',
        'topic' => Topic::PROMOTIONS,
        'status' => SubscriptionStatus::SUBSCRIBED,
        'user_id' => $user->id,
    ]);
    $promotion = Promotion::factory()->create(['club_id' => $club->id]);

    (new SendSubscriptionPromotionNotification($subscription, $promotion))->handle();

    Notification::assertSentTo($user, PromotionPublished::class);
});

it('sends an on-demand email notification to an unlinked email subscriber', function () {
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

    (new SendSubscriptionPromotionNotification($subscription, $promotion))->handle();

    Notification::assertSentOnDemand(PromotionPublished::class);
});

it('sends an on-demand sms notification to an unlinked sms subscriber', function () {
    Notification::fake();
    config(['vonage.api_key' => 'test', 'vonage.api_secret' => 'test', 'vonage.sms_from' => 'test']);

    $club = Club::factory()->create();
    $subscription = CommunicationSubscription::factory()->create([
        'club_id' => $club->id,
        'contact_type' => ContactType::SMS,
        'contact_value' => '+12025550198',
        'topic' => Topic::PROMOTIONS,
        'status' => SubscriptionStatus::SUBSCRIBED,
        'user_id' => null,
    ]);
    $promotion = Promotion::factory()->create(['club_id' => $club->id]);

    (new SendSubscriptionPromotionNotification($subscription, $promotion))->handle();

    Notification::assertSentOnDemand(PromotionPublished::class);
});

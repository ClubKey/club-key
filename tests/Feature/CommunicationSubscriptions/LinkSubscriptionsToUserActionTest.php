<?php

declare(strict_types=1);

use App\Actions\CommunicationSubscriptions\LinkSubscriptionsToUserAction;
use App\Enums\ContactType;
use App\Enums\SubscriptionStatus;
use App\Enums\Topic;
use App\Models\Club;
use App\Models\CommunicationSubscription;
use App\Models\User;

pest()->group('communication-subscriptions', 'link-subscriptions-to-user');

it('links unlinked email subscriptions to a user by email match', function () {
    $club = Club::factory()->create();
    $user = User::factory()->create(['email' => 'test@example.com']);
    $subscription = CommunicationSubscription::factory()->create([
        'club_id' => $club->id,
        'contact_type' => ContactType::EMAIL,
        'contact_value' => 'test@example.com',
        'topic' => Topic::PROMOTIONS,
        'status' => SubscriptionStatus::SUBSCRIBED,
        'user_id' => null,
    ]);

    $linked = (new LinkSubscriptionsToUserAction())->handle($user);

    expect($linked)->toBe(1);
    expect($subscription->fresh()->user_id)->toBe($user->id);
});

it('links unlinked sms subscriptions to a user by phone match', function () {
    $club = Club::factory()->create();
    $user = User::factory()->create(['phone' => '+12025550198']);
    $subscription = CommunicationSubscription::factory()->create([
        'club_id' => $club->id,
        'contact_type' => ContactType::SMS,
        'contact_value' => '+12025550198',
        'topic' => Topic::PROMOTIONS,
        'status' => SubscriptionStatus::SUBSCRIBED,
        'user_id' => null,
    ]);

    $linked = (new LinkSubscriptionsToUserAction())->handle($user);

    expect($linked)->toBe(1);
    expect($subscription->fresh()->user_id)->toBe($user->id);
});

it('links subscriptions across multiple clubs when no club is scoped', function () {
    $clubA = Club::factory()->create();
    $clubB = Club::factory()->create();
    $user = User::factory()->create(['email' => 'multi@example.com']);

    $subA = CommunicationSubscription::factory()->create([
        'club_id' => $clubA->id,
        'contact_type' => ContactType::EMAIL,
        'contact_value' => 'multi@example.com',
        'topic' => Topic::PROMOTIONS,
        'user_id' => null,
    ]);
    $subB = CommunicationSubscription::factory()->create([
        'club_id' => $clubB->id,
        'contact_type' => ContactType::EMAIL,
        'contact_value' => 'multi@example.com',
        'topic' => Topic::NOTIFICATIONS,
        'user_id' => null,
    ]);

    $linked = (new LinkSubscriptionsToUserAction())->handle($user);

    expect($linked)->toBe(2);
    expect($subA->fresh()->user_id)->toBe($user->id);
    expect($subB->fresh()->user_id)->toBe($user->id);
});

it('skips subscriptions already linked to a user', function () {
    $club = Club::factory()->create();
    $existingUser = User::factory()->create();
    $newUser = User::factory()->create(['email' => 'taken@example.com']);

    $subscription = CommunicationSubscription::factory()->create([
        'club_id' => $club->id,
        'contact_type' => ContactType::EMAIL,
        'contact_value' => 'taken@example.com',
        'topic' => Topic::PROMOTIONS,
        'user_id' => $existingUser->id,
    ]);

    (new LinkSubscriptionsToUserAction())->handle($newUser);

    expect($subscription->fresh()->user_id)->toBe($existingUser->id);
});

it('only links subscriptions for the scoped club', function () {
    $clubA = Club::factory()->create();
    $clubB = Club::factory()->create();
    $user = User::factory()->create(['email' => 'scoped@example.com']);

    $subA = CommunicationSubscription::factory()->create([
        'club_id' => $clubA->id,
        'contact_type' => ContactType::EMAIL,
        'contact_value' => 'scoped@example.com',
        'topic' => Topic::PROMOTIONS,
        'user_id' => null,
    ]);
    $subB = CommunicationSubscription::factory()->create([
        'club_id' => $clubB->id,
        'contact_type' => ContactType::EMAIL,
        'contact_value' => 'scoped@example.com',
        'topic' => Topic::PROMOTIONS,
        'user_id' => null,
    ]);

    $linked = (new LinkSubscriptionsToUserAction())->handle($user, $clubA);

    expect($linked)->toBe(1);
    expect($subA->fresh()->user_id)->toBe($user->id);
    expect($subB->fresh()->user_id)->toBeNull();
});

it('returns zero and does nothing when no matching subscriptions exist', function () {
    $user = User::factory()->create(['email' => 'nomatch@example.com']);

    $linked = (new LinkSubscriptionsToUserAction())->handle($user);

    expect($linked)->toBe(0);
});

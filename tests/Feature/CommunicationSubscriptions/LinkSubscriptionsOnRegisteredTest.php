<?php

declare(strict_types=1);

use App\Enums\ContactType;
use App\Enums\Topic;
use App\Models\Club;
use App\Models\CommunicationSubscription;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

pest()->group('communication-subscriptions', 'link-subscriptions-on-registered');

it('links an unlinked email subscription when a matching user registers', function () {
    $club = Club::factory()->create();
    $subscription = CommunicationSubscription::factory()->create([
        'club_id' => $club->id,
        'contact_type' => ContactType::EMAIL,
        'contact_value' => 'newuser@example.com',
        'topic' => Topic::PROMOTIONS,
        'user_id' => null,
    ]);

    $user = User::factory()->create(['email' => 'newuser@example.com']);
    event(new Registered($user));

    expect($subscription->fresh()->user_id)->toBe($user->id);
});

it('links an unlinked sms subscription when a matching user registers', function () {
    $club = Club::factory()->create();
    $subscription = CommunicationSubscription::factory()->create([
        'club_id' => $club->id,
        'contact_type' => ContactType::SMS,
        'contact_value' => '+12025550198',
        'topic' => Topic::PROMOTIONS,
        'user_id' => null,
    ]);

    $user = User::factory()->create(['phone' => '+12025550198']);
    event(new Registered($user));

    expect($subscription->fresh()->user_id)->toBe($user->id);
});

it('links subscriptions across multiple clubs on registration', function () {
    $clubA = Club::factory()->create();
    $clubB = Club::factory()->create();

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

    $user = User::factory()->create(['email' => 'multi@example.com']);
    event(new Registered($user));

    expect($subA->fresh()->user_id)->toBe($user->id);
    expect($subB->fresh()->user_id)->toBe($user->id);
});

it('does not link subscriptions that belong to a different contact value', function () {
    $club = Club::factory()->create();
    $subscription = CommunicationSubscription::factory()->create([
        'club_id' => $club->id,
        'contact_type' => ContactType::EMAIL,
        'contact_value' => 'other@example.com',
        'topic' => Topic::PROMOTIONS,
        'user_id' => null,
    ]);

    $user = User::factory()->create(['email' => 'newuser@example.com']);
    event(new Registered($user));

    expect($subscription->fresh()->user_id)->toBeNull();
});

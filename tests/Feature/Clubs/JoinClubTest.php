<?php

declare(strict_types=1);

use App\Actions\Clubs\JoinClubAction;
use App\Enums\ContactType;
use App\Enums\Topic;
use App\Models\Club;
use App\Models\CommunicationSubscription;
use App\Models\User;

pest()->group('clubs', 'join-club');

it('links unlinked subscriptions for the club when a new member joins', function () {
    $club = Club::factory()->create();
    $user = User::factory()->create(['email' => 'joiner@example.com']);

    $subscription = CommunicationSubscription::factory()->create([
        'club_id' => $club->id,
        'contact_type' => ContactType::EMAIL,
        'contact_value' => 'joiner@example.com',
        'topic' => Topic::PROMOTIONS,
        'user_id' => null,
    ]);

    app(JoinClubAction::class)->handle($user, $club);

    expect($subscription->fresh()->user_id)->toBe($user->id);
});

it('links unlinked subscriptions when an already-registered user joins a club', function () {
    $club = Club::factory()->create();
    $user = User::factory()->create(['email' => 'existing@example.com']);

    $subscription = CommunicationSubscription::factory()->create([
        'club_id' => $club->id,
        'contact_type' => ContactType::EMAIL,
        'contact_value' => 'existing@example.com',
        'topic' => Topic::PROMOTIONS,
        'user_id' => null,
    ]);

    app(JoinClubAction::class)->handle($user, $club);

    expect($subscription->fresh()->user_id)->toBe($user->id);
});

it('does not link subscriptions from other clubs when a user joins', function () {
    $clubA = Club::factory()->create();
    $clubB = Club::factory()->create();
    $user = User::factory()->create(['email' => 'scoped@example.com']);

    $subOther = CommunicationSubscription::factory()->create([
        'club_id' => $clubB->id,
        'contact_type' => ContactType::EMAIL,
        'contact_value' => 'scoped@example.com',
        'topic' => Topic::PROMOTIONS,
        'user_id' => null,
    ]);

    app(JoinClubAction::class)->handle($user, $clubA);

    expect($subOther->fresh()->user_id)->toBeNull();
});

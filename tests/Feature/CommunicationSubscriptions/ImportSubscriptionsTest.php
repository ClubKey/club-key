<?php

declare(strict_types=1);

use App\Actions\CommunicationSubscriptions\ImportSubscriptionsAction;
use App\Models\Club;
use App\Models\CommunicationSubscription;
use App\Models\User;

pest()->group('communication-subscriptions', 'import-subscriptions');

test('can import subscriptions from array', function () {
    $club = Club::factory()->create();
    $user = User::factory()->create(['email' => 'user@example.com']);

    $subscriptions = [
        [
            'contact_type' => 'email',
            'contact_value' => 'test1@example.com',
            'topic' => 'promotions',
        ],
        [
            'contact_type' => 'email',
            'contact_value' => 'test2@example.com',
            'topic' => 'notifications',
            'user_id' => $user->id,
        ],
        [
            'contact_type' => 'sms',
            'contact_value' => '2025550198',
            'topic' => 'promotions',
        ],
    ];

    $action = new ImportSubscriptionsAction(new \App\Actions\CommunicationSubscriptions\CreateSubscriptionAction());

    $result = $action->handle($club, $subscriptions, 'test_import');

    expect($result->success)->toBe(3)
        ->and($result->errors)->toBeEmpty()
        ->and(CommunicationSubscription::where('club_id', $club->id)->count())->toBe(3);
});

test('handles import errors gracefully', function () {
    $club = Club::factory()->create();

    $subscriptions = [
        [
            'contact_type' => 'email',
            'contact_value' => 'valid@example.com',
            'topic' => 'promotions',
        ],
        [
            'contact_type' => 'invalid_type',
            'contact_value' => 'test@example.com',
            'topic' => 'promotions',
        ],
    ];

    $action = new ImportSubscriptionsAction(new \App\Actions\CommunicationSubscriptions\CreateSubscriptionAction());

    $result = $action->handle($club, $subscriptions, 'test_import');

    expect($result->success)->toBe(1)
        ->and($result->errors)->toHaveCount(1)
        ->and($result->errors[0]->row)->toBe(2);
});

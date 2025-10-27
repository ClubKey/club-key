<?php

declare(strict_types=1);

use App\Actions\TextMarketing\HandleVonageInboundMessageAction;
use App\Models\Club;
use App\Models\ClubSmsSetting;
use App\Models\TextSubscription;

it('unsubscribes members when inbound messages contain opt-out keywords', function (): void {
    $club = Club::factory()->create();
    $settings = ClubSmsSetting::factory()->create([
        'club_id' => $club->id,
        'vonage_number' => '+15555550000',
    ]);

    $subscription = TextSubscription::factory()->create([
        'club_id' => $club->id,
        'phone_number' => '+15555551234',
        'is_subscribed' => true,
    ]);

    /** @var HandleVonageInboundMessageAction $action */
    $action = app(HandleVonageInboundMessageAction::class);

    $action->handle([
        'to' => $settings->vonage_number,
        'msisdn' => $subscription->phone_number,
        'text' => 'STOP',
    ]);

    expect($subscription->fresh()->is_subscribed)->toBeFalse();
});

it('creates subscriptions for new inbound numbers', function (): void {
    $club = Club::factory()->create();
    $settings = ClubSmsSetting::factory()->create([
        'club_id' => $club->id,
        'vonage_number' => '+15555550001',
    ]);

    /** @var HandleVonageInboundMessageAction $action */
    $action = app(HandleVonageInboundMessageAction::class);

    $action->handle([
        'to' => $settings->vonage_number,
        'msisdn' => '+15555554321',
        'text' => 'START',
    ]);

    $subscription = TextSubscription::query()
        ->where('club_id', $club->id)
        ->where('phone_number', '+15555554321')
        ->first();

    expect($subscription)->not->toBeNull()
        ->and($subscription?->is_subscribed)->toBeTrue();
});

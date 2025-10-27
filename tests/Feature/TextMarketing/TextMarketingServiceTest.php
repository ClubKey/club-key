<?php

declare(strict_types=1);

use App\Jobs\SendSmsMessageJob;
use App\Models\Club;
use App\Models\ClubSmsSetting;
use App\Models\TextSubscription;
use App\Services\TextMarketing\TextMarketingService;
use Illuminate\Support\Facades\Queue;

test('text marketing service queues outbound messages with opt-out footer', function (): void {
    Queue::fake();

    /** @var TextMarketingService $service */
    $service = app(TextMarketingService::class);

    $club = Club::factory()->create();
    ClubSmsSetting::factory()->create([
        'club_id' => $club->id,
        'vonage_number' => '+15555550123',
    ]);

    $subscription = TextSubscription::factory()->create([
        'club_id' => $club->id,
        'phone_number' => '+15555550987',
        'is_subscribed' => true,
    ]);

    $message = $service->queueMessage($club, $subscription, 'Half price coffee this week!');

    expect($message->body)
        ->toContain('Reply STOP to unsubscribe.')
        ->and($message->status->value)->toBe('queued');

    Queue::assertPushed(SendSmsMessageJob::class);
});

test('text marketing service prevents sending to unsubscribed subscribers', function (): void {
    /** @var TextMarketingService $service */
    $service = app(TextMarketingService::class);

    $club = Club::factory()->create();
    ClubSmsSetting::factory()->create([
        'club_id' => $club->id,
        'vonage_number' => '+15555550123',
    ]);

    $subscription = TextSubscription::factory()->unsubscribed()->create([
        'club_id' => $club->id,
        'phone_number' => '+15555550987',
    ]);

    $service->queueMessage($club, $subscription, 'New menu drop');
})->throws(\DomainException::class);

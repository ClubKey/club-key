<?php

declare(strict_types=1);

use App\Jobs\SendMemberPromotionNotification;
use App\Models\Promotion;
use App\Models\User;

test('a promotion notification can be resent if it is published', function () {
    Queue::fake();
    config(['queue.default' => 'database']);

    $user = User::factory()->withClub()->create();

    $promotion = Promotion::factory()->create([
        'club_id' => $user->current_club_id,
        'published_at' => now(),
    ]);

    $this->actingAs($user)->post('/promotions/'.$promotion->id.'/resend');

    Queue::assertPushed(SendMemberPromotionNotification::class);
});

test('a promotion notification cannot be resent if it is not published', function () {
    Queue::fake();
    config(['queue.default' => 'database']);
    $user = User::factory()->withClub()->create();

    $promotion = Promotion::factory()->create([
        'club_id' => $user->current_club_id,
        'published_at' => null,
    ]);

    $response = $this->actingAs($user)->post('/promotions/'.$promotion->id.'/resend');

    Queue::assertNotPushed(SendMemberPromotionNotification::class);

    $response->assertSessionHas('error');
});

test('a user not belonging to a club cannot resend promotion email', function () {
    Queue::fake();
    config(['queue.default' => 'database']);
    $user = User::factory()->withClub()->create();
    $otherUser = User::factory()->withClub()->create();

    $promotion = Promotion::factory()->create([
        'club_id' => $user->current_club_id,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($otherUser)->post('/promotions/'.$promotion->id.'/resend');

    Queue::assertNotPushed(SendMemberPromotionNotification::class);

    expect($response->status())->toBe(403);
});

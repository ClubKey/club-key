<?php

declare(strict_types=1);

use App\Jobs\PromotionCreated;
use App\Jobs\PublishDuePromotionsJob;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;

it('allows a club admin to view a promotion', function (): void {
    $user = User::factory()->withClub()->create();
    $promotion = Promotion::factory()->create([
        'club_id' => $user->currentClub->id,
    ]);

    $this->actingAs($user);

    $response = $this->get(route('promotions.show', $promotion));

    $response->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('promotions/Show')
        ->where('promotion.id', $promotion->id)
        ->where('promotion.name', $promotion->name)
    );
});

it('forbids viewing a promotion from another club', function (): void {
    $user = User::factory()->withClub()->create();
    $promotion = Promotion::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('promotions.show', $promotion));

    $response->assertForbidden();
});

it('allows a club admin to update a promotion', function (): void {
    $user = User::factory()->withClub()->create();
    $promotion = Promotion::factory()->create([
        'club_id' => $user->currentClub->id,
        'is_active' => true,
    ]);

    $this->actingAs($user);

    $timezone = 'America/New_York';
    $publishAt = Carbon::create(2024, 1, 10, 12, 0, 0, $timezone);
    $startAt = Carbon::create(2024, 1, 11, 9, 0, 0, $timezone);
    $endAt = Carbon::create(2024, 1, 12, 18, 0, 0, $timezone);

    $response = $this->put(route('promotions.update', $promotion), [
        'name' => 'Updated Promotion',
        'description' => 'Updated description for the promotion.',
        'publish_now' => false,
        'publish_at' => $publishAt->format('Y-m-d\TH:i'),
        'start_at' => $startAt->format('Y-m-d\TH:i'),
        'end_at' => $endAt->format('Y-m-d\TH:i'),
        'is_active' => false,
        'link' => 'https://example.com/promotion',
        'timezone' => $timezone,
    ]);

    $response->assertRedirect(route('promotions.show', $promotion));

    $this->assertDatabaseHas('promotions', [
        'id' => $promotion->id,
        'name' => 'Updated Promotion',
        'description' => 'Updated description for the promotion.',
        'publish_at' => $publishAt->copy()->setTimezone('UTC')->toDateTimeString(),
        'start_at' => $startAt->copy()->setTimezone('UTC')->toDateTimeString(),
        'end_at' => $endAt->copy()->setTimezone('UTC')->toDateTimeString(),
        'is_active' => false,
        'link' => 'https://example.com/promotion',
    ]);
});

it('handles checkbox payloads when updating scheduling details', function (): void {
    Carbon::setTestNow(Carbon::create(2024, 1, 1, 9));

    try {
        $user = User::factory()->withClub()->create();
        $promotion = Promotion::factory()->create([
            'club_id' => $user->currentClub->id,
            'is_active' => true,
            'publish_at' => Carbon::now()->subDay(),
        ]);

        $this->actingAs($user);

        $timezone = 'America/Chicago';
        $startAt = Carbon::create(2024, 1, 2, 9, 0, 0, $timezone)->format('Y-m-d\TH:i');

        $response = $this->put(route('promotions.update', $promotion), [
            'name' => 'Checkbox Submission',
            'description' => 'Ensures checkbox strings are normalized.',
            'publish_now' => '1',
            'publish_at' => null,
            'start_at' => $startAt,
            'end_at' => null,
            'is_active' => '0',
            'link' => '',
            'timezone' => $timezone,
        ]);

        $response->assertRedirect(route('promotions.show', $promotion));

        $promotion->refresh();

        expect($promotion->name)->toBe('Checkbox Submission');
        expect($promotion->description)->toBe('Ensures checkbox strings are normalized.');
        expect($promotion->publish_at)->toEqual(Carbon::now()->toImmutable());
        expect($promotion->start_at)->toEqual(
            Carbon::parse($startAt, $timezone)->setTimezone('UTC')->toImmutable()
        );
        expect($promotion->end_at)->toBeNull();
        expect($promotion->is_active)->toBeFalse();
        expect($promotion->link)->toBeNull();
    } finally {
        Carbon::setTestNow();
    }
});

it('forbids updating a promotion that belongs to another club', function (): void {
    $user = User::factory()->withClub()->create();
    $promotion = Promotion::factory()->create();

    $this->actingAs($user);

    $response = $this->put(route('promotions.update', $promotion), [
        'name' => 'Invalid Update',
        'description' => 'Should not be allowed.',
        'timezone' => 'UTC',
    ]);

    $response->assertForbidden();
});

it('allows a club admin to delete a promotion', function (): void {
    $user = User::factory()->withClub()->create();
    $promotion = Promotion::factory()->create([
        'club_id' => $user->currentClub->id,
    ]);

    $this->actingAs($user);

    $response = $this->delete(route('promotions.destroy', $promotion));

    $response->assertRedirect(route('promotions.index'));

    $this->assertDatabaseMissing('promotions', [
        'id' => $promotion->id,
    ]);
});

it('forbids deleting a promotion that belongs to another club', function (): void {
    $user = User::factory()->withClub()->create();
    $promotion = Promotion::factory()->create();

    $this->actingAs($user);

    $response = $this->delete(route('promotions.destroy', $promotion));

    $response->assertForbidden();

    $this->assertDatabaseHas('promotions', [
        'id' => $promotion->id,
    ]);
});

it('publishes active promotions that are ready for publication', function (): void {
    Carbon::setTestNow(Carbon::create(2024, 1, 1, 12));

    try {
        $now = Carbon::now();

        $readyPromotion = Promotion::factory()->create([
            'publish_at' => $now->copy()->subMinutes(5),
            'published_at' => null,
            'is_active' => true,
        ]);

        $inactivePromotion = Promotion::factory()->create([
            'publish_at' => $now->copy()->subMinutes(10),
            'published_at' => null,
            'is_active' => false,
        ]);

        $futurePromotion = Promotion::factory()->create([
            'publish_at' => $now->copy()->addMinutes(10),
            'published_at' => null,
            'is_active' => true,
        ]);

        $alreadyPublishedAt = $now->copy()->subMinutes(1)->toImmutable();
        $alreadyPublishedPromotion = Promotion::factory()->create([
            'publish_at' => $now->copy()->subMinutes(20),
            'published_at' => $alreadyPublishedAt,
            'is_active' => true,
        ]);

        $expectedPublishedAt = $now->toImmutable();

        PublishDuePromotionsJob::dispatchSync();

        expect($readyPromotion->fresh()->published_at)->toEqual($expectedPublishedAt)
            ->and($inactivePromotion->fresh()->published_at)->toBeNull()
            ->and($futurePromotion->fresh()->published_at)->toBeNull()
            ->and($alreadyPublishedPromotion->fresh()->published_at)->toEqual($alreadyPublishedAt);
    } finally {
        Carbon::setTestNow();
    }
});

it('does not publish promotions that are already published', function (): void {
    // without seconds
    $publishAtDate = now()->subMinutes()->toDateTimeString();
    $promotion = Promotion::factory()->create([
        'publish_at' => now()->subMinutes(1),
        'published_at' => $publishAtDate,
    ]);

    PublishDuePromotionsJob::dispatchSync();

    expect($promotion->fresh()->published_at)->toEqual($publishAtDate)
        ->and($promotion->published_at)->not->toBeNull();
});

it('does not publish promotions that are scheduled for the future', function (): void {
    $promotion = Promotion::factory()->create([
        'publish_at' => now()->addMinutes(1),
    ]);

    PublishDuePromotionsJob::dispatchSync();

    expect($promotion->fresh()->published_at)->toBeNull();
});

test('when a promotion is created with the publish_now checkbox checked, it is published immediately', function (): void {
    $user = User::factory()->withClub()->create();
    $club = $user->currentClub;

    $response = $this->actingAs($user)->post(route('promotions.store'), [
        'name' => 'Publish Now Promotion',
        'description' => 'This promotion should be published immediately.',
        'publish_now' => '1',
        'timezone' => 'UTC',
    ]);

    $response->assertRedirect('/promotions/');

    $this->assertDatabaseHas('promotions', [
        'name' => 'Publish Now Promotion',
        'description' => 'This promotion should be published immediately.',
    ]);

    expect($club->promotions()->count())->toBe(1)
        ->and($club->promotions()->first()->published_at)->not->toBeNull();
});

test('when a promotion club has thousands of members, the response is still fast as the promotion is published in a queue event', function (): void {
    Queue::fake();
    config(['queue.default' => 'database']);

    $user = User::factory()->withClub()->create();
    $club = $user->currentClub;

    // Create 1000 members for the club
    User::factory()->count(1000)->create()->each(function ($member) use ($club) {
        $club->members()->attach($member);
    });

    $startTime = microtime(true);

    $response = $this->actingAs($user)->post(route('promotions.store'), [
        'name' => 'Mass Member Promotion',
        'description' => 'Testing promotion creation with many members',
        'publish_now' => '1',
        'timezone' => 'UTC',
    ]);

    $endTime = microtime(true);
    $executionTime = ($endTime - $startTime);

    $response->assertRedirect('/promotions/');

    // Assert the response was fast (under 1 second)
    expect($executionTime)->toBeLessThan(1.0);

    // Assert the job was queued
    Queue::assertPushed(PromotionCreated::class);
});

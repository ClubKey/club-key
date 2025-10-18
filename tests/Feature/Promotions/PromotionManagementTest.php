<?php

declare(strict_types=1);

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

    $publishAt = Carbon::now()->addDay()->seconds(0);
    $startAt = Carbon::now()->addDay()->seconds(0);
    $endAt = Carbon::now()->addDays(2)->seconds(0);

    $response = $this->put(route('promotions.update', $promotion), [
        'name' => 'Updated Promotion',
        'description' => 'Updated description for the promotion.',
        'publish_now' => false,
        'publish_at' => $publishAt->toDateTimeString(),
        'start_at' => $startAt->toDateTimeString(),
        'end_at' => $endAt->toDateTimeString(),
        'is_active' => false,
        'link' => 'https://example.com/promotion',
    ]);

    $response->assertRedirect(route('promotions.show', $promotion));

    $this->assertDatabaseHas('promotions', [
        'id' => $promotion->id,
        'name' => 'Updated Promotion',
        'description' => 'Updated description for the promotion.',
        'publish_at' => $publishAt->toDateTimeString(),
        'start_at' => $startAt->toDateTimeString(),
        'end_at' => $endAt->toDateTimeString(),
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

        $startAt = Carbon::now()->addDay()->format('Y-m-d\TH:i');

        $response = $this->put(route('promotions.update', $promotion), [
            'name' => 'Checkbox Submission',
            'description' => 'Ensures checkbox strings are normalized.',
            'publish_now' => '1',
            'publish_at' => null,
            'start_at' => $startAt,
            'end_at' => null,
            'is_active' => '0',
            'link' => '',
        ]);

        $response->assertRedirect(route('promotions.show', $promotion));

        $promotion->refresh();

        expect($promotion->name)->toBe('Checkbox Submission');
        expect($promotion->description)->toBe('Ensures checkbox strings are normalized.');
        expect($promotion->publish_at)->toEqual(Carbon::now()->toImmutable());
        expect($promotion->start_at)->toEqual(Carbon::parse($startAt)->toImmutable());
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

        expect($readyPromotion->fresh()->published_at)->toEqual($expectedPublishedAt);
        expect($inactivePromotion->fresh()->published_at)->toBeNull();
        expect($futurePromotion->fresh()->published_at)->toBeNull();
        expect($alreadyPublishedPromotion->fresh()->published_at)->toEqual($alreadyPublishedAt);
    } finally {
        Carbon::setTestNow();
    }
});

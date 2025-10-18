<?php

declare(strict_types=1);

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

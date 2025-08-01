<?php

declare(strict_types=1);

use App\Enums\ClubRole;
use App\Models\Club;
use App\Models\ClubLocation;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create(['role' => 'club_admin']);
    $this->club = Club::factory()->create();
    $this->club->members()->attach($this->user->id, ['role' => ClubRole::ADMIN->value]);
    $this->user->update(['current_club_id' => $this->club->id]);
    $this->location = ClubLocation::factory()->create(['club_id' => $this->club->id]);
});

test('admin users can delete locations', function () {
    $response = $this->actingAs($this->user)
        ->delete("/locations/{$this->location->id}");

    $response->assertRedirect('/locations');
    $response->assertSessionHas('success');

    expect(DB::table('club_locations')->where('id', $this->location->id)->exists())->toBeFalse();
});

test('non admin users cannot delete locations', function () {
    $user = User::factory()->create(['role' => 'user']);
    $club = Club::factory()->create();
    $club->members()->attach($user->id, ['role' => ClubRole::MEMBER->value]);
    $location = ClubLocation::factory()->create(['club_id' => $club->id]);

    $response = $this->actingAs($user)
        ->delete("/locations/{$location->id}");

    $response->assertForbidden();

    expect(DB::table('club_locations')->where('id', $location->id)->exists())->toBeTrue();
});

test('users cannot delete locations from other clubs', function () {
    $otherClub = Club::factory()->create();
    $location = ClubLocation::factory()->create(['club_id' => $otherClub->id]);

    $response = $this->actingAs($this->user)
        ->delete("/locations/{$location->id}");

    $response->assertForbidden();

    expect(DB::table('club_locations')->where('id', $location->id)->exists())->toBeTrue();
});

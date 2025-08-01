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
});

test('users can view locations index', function () {
    $locations = ClubLocation::factory()->count(3)->create(['club_id' => $this->club->id]);

    $response = $this->actingAs($this->user)
        ->get('/locations');

    $response->assertInertia(fn ($page) => $page
        ->component('locations/Index')
        ->has('club')
        ->has('locations', 3)
    );
});

test('locations are ordered by name', function () {
    // Create locations in random order
    ClubLocation::factory()->create(['club_id' => $this->club->id, 'name' => 'Zebra Location']);
    ClubLocation::factory()->create(['club_id' => $this->club->id, 'name' => 'Alpha Location']);
    ClubLocation::factory()->create(['club_id' => $this->club->id, 'name' => 'Beta Location']);

    $response = $this->actingAs($this->user)
        ->get('/locations');

    $response->assertInertia(fn ($page) => $page
        ->component('locations/Index')
        ->has('locations', 3)
        ->where('locations.0.name', 'Alpha Location')
        ->where('locations.1.name', 'Beta Location')
        ->where('locations.2.name', 'Zebra Location')
    );
});

test('empty locations shows empty state', function () {
    $response = $this->actingAs($this->user)
        ->get('/locations');

    $response->assertInertia(fn ($page) => $page
        ->component('locations/Index')
        ->has('club')
        ->has('locations', 0)
    );
});

test('only club locations are shown', function () {
    $otherClub = Club::factory()->create();

    // Create locations for both clubs
    ClubLocation::factory()->create(['club_id' => $this->club->id, 'name' => 'Club Location']);
    ClubLocation::factory()->create(['club_id' => $otherClub->id, 'name' => 'Other Club Location']);

    $response = $this->actingAs($this->user)
        ->get('/locations');

    $response->assertInertia(fn ($page) => $page
        ->component('locations/Index')
        ->has('locations', 1)
        ->where('locations.0.name', 'Club Location')
    );
});

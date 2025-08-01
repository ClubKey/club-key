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

test('users can view edit location page', function () {
    $response = $this->actingAs($this->user)
        ->get("/locations/{$this->location->id}/edit");

    $response->assertInertia(fn ($page) => $page
        ->component('locations/Edit')
        ->has('club')
        ->has('location')
    );
});

test('users can update location', function () {
    $updatedData = [
        'name' => 'Updated Downtown Branch',
        'street_address' => '456 Oak Ave',
        'city' => 'Los Angeles',
        'state' => 'CA',
        'zip' => '90210',
        'phone' => '(555) 987-6543',
    ];

    $response = $this->actingAs($this->user)
        ->put("/locations/{$this->location->id}", $updatedData);

    $response->assertRedirect('/locations');
    $response->assertSessionHas('success');

    expect(DB::table('club_locations')->where([
        'id' => $this->location->id,
        'club_id' => $this->club->id,
        'name' => 'Updated Downtown Branch',
        'street_address' => '456 Oak Ave',
        'city' => 'Los Angeles',
        'state' => 'CA',
        'zip' => '90210',
        'phone' => '(555) 987-6543',
    ])->exists())->toBeTrue();
});

test('location update validates required fields', function () {
    $response = $this->actingAs($this->user)
        ->put("/locations/{$this->location->id}", []);

    $response->assertSessionHasErrors(['name', 'street_address', 'city', 'state', 'zip']);
});

test('non admin users cannot update locations', function () {
    $user = User::factory()->create(['role' => 'user']);
    $club = Club::factory()->create();
    $club->members()->attach($user->id, ['role' => ClubRole::MEMBER->value]);
    $location = ClubLocation::factory()->create(['club_id' => $club->id]);

    $updatedData = [
        'name' => 'Updated Downtown Branch',
        'street_address' => '456 Oak Ave',
        'city' => 'Los Angeles',
        'state' => 'CA',
        'zip' => '90210',
    ];

    $response = $this->actingAs($user)
        ->put("/locations/{$location->id}", $updatedData);

    $response->assertForbidden();
});

test('users cannot update locations from other clubs', function () {
    $otherClub = Club::factory()->create();
    $location = ClubLocation::factory()->create(['club_id' => $otherClub->id]);

    $updatedData = [
        'name' => 'Updated Downtown Branch',
        'street_address' => '456 Oak Ave',
        'city' => 'Los Angeles',
        'state' => 'CA',
        'zip' => '90210',
    ];

    $response = $this->actingAs($this->user)
        ->put("/locations/{$location->id}", $updatedData);

    $response->assertForbidden();
});

<?php

declare(strict_types=1);

use App\Enums\ClubRole;
use App\Models\Club;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create(['role' => 'club_admin']);
    $this->club = Club::factory()->create();
    $this->club->members()->attach($this->user->id, ['role' => ClubRole::ADMIN->value]);
    $this->user->update(['current_club_id' => $this->club->id]);
});

test('users can view create location page', function () {
    $response = $this->actingAs($this->user)
        ->get('/locations/create');

    $response->assertInertia(fn ($page) => $page
        ->component('locations/Create')
        ->has('club')
    );
});

test('users can create location', function () {
    $locationData = [
        'name' => 'Downtown Branch',
        'street_address' => '123 Main St',
        'city' => 'New York',
        'state' => 'NY',
        'zip' => '10001',
        'phone' => '(555) 123-4567',
    ];

    $response = $this->actingAs($this->user)
        ->post('/locations', $locationData);

    $response->assertRedirect('/locations');
    $response->assertSessionHas('success');

    expect(DB::table('club_locations')->where([
        'club_id' => $this->club->id,
        'name' => 'Downtown Branch',
        'street_address' => '123 Main St',
        'city' => 'New York',
        'state' => 'NY',
        'zip' => '10001',
        'phone' => '(555) 123-4567',
    ])->exists())->toBeTrue();
});

test('location creation validates required fields', function () {
    $response = $this->actingAs($this->user)
        ->post('/locations', []);

    $response->assertSessionHasErrors(['name', 'street_address', 'city', 'state', 'zip']);
});

test('non admin users cannot create locations', function () {
    $user = User::factory()->create(['role' => 'user']);
    $club = Club::factory()->create();
    $club->members()->attach($user->id, ['role' => ClubRole::MEMBER->value]);

    $locationData = [
        'name' => 'Downtown Branch',
        'street_address' => '123 Main St',
        'city' => 'New York',
        'state' => 'NY',
        'zip' => '10001',
    ];

    $response = $this->actingAs($user)
        ->post('/locations', $locationData);

    $response->assertForbidden();
});

test('phone is optional', function () {
    $locationData = [
        'name' => 'Downtown Branch',
        'street_address' => '123 Main St',
        'city' => 'New York',
        'state' => 'NY',
        'zip' => '10001',
    ];

    $response = $this->actingAs($this->user)
        ->post('/locations', $locationData);

    $response->assertRedirect('/locations');
    $response->assertSessionHas('success');

    expect(DB::table('club_locations')->where([
        'club_id' => $this->club->id,
        'name' => 'Downtown Branch',
        'phone' => null,
    ])->exists())->toBeTrue();
});

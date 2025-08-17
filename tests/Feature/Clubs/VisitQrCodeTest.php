<?php

declare(strict_types=1);

use App\Models\Club;
use App\Models\User;

pest()->group('visit', 'qr-code');

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

test('when a club is created, a qr code is generated', function () {

    $this->actingAs($this->user)->post(route('clubs.store'), [
        'name' => 'Test Club',
        'slug' => 'test-club',
        'description' => 'A club for testing purposes',
        'address' => '123 Test St',
        'city' => 'Test City',
        'state' => 'TS',
        'zip' => '12345',
        'phone' => '123-456-7890',
        'email' => 'test@test.com',
    ]);

    $club = Club::where('slug', 'test-club')->first();

    expect($club->visit_qr_code_path)->toBeString();

    $this->assertTrue(Storage::disk('public')->exists($club->visit_qr_code_path));
});

test('when a club location is created, a qr code is generated', function () {
    $club = Club::factory()->create();
    $club->members()->attach($this->user->id, ['role' => 'admin']);
    $this->user->update(['current_club_id' => $club->id]);

    $this->actingAs($this->user);

    $this->post(route('locations.store'), [
        'name' => 'Test Location',
        'street_address' => '456 Test Ave',
        'city' => 'Test City',
        'state' => 'TS',
        'zip' => '12345',
        'phone' => '987-654-3210',
    ]);

    $location = $club->locations()->where('name', 'Test Location')->first();

    expect($location->visit_qr_code_path)->toBeString();

    $this->assertTrue(Storage::disk('public')->exists($location->visit_qr_code_path));
});

test('when a club slug is updated, the qr code is regenerated', function () {

    $this->actingAs($this->user);

    $this->post(route('clubs.store'), [
        'name' => 'Test Club',
        'slug' => 'test-club',
        'description' => 'A club for testing purposes',
        'address' => '123 Test St',
        'city' => 'Test City',
        'state' => 'TS',
        'zip' => '12345',
        'phone' => '123-456-7890',
        'email' => 'test@test.com',
    ]);

    $club = Club::where('slug', 'test-club')->first();

    $previousQrCodePath = $club->visit_qr_code_path;

    $this->patch(route('clubs.update', ['club' => $club]), [
        'name' => 'Updated Club Name',
        'slug' => 'updated-club',
        'description' => 'Updated description',
        'address' => '789 Updated St',
        'city' => 'Updated City',
        'state' => 'US',
        'zip' => '54321',
        'phone' => '321-654-0987',
        'email' => 'test@test.com',
    ]);

    $club->refresh();

    expect($club->visit_qr_code_path)->not->toBe($previousQrCodePath)
        ->and(Storage::disk('public')->exists($club->visit_qr_code_path))->toBeTrue();
});

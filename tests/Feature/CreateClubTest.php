<?php

declare(strict_types=1);

use App\Enums\AppRole;
use App\Models\Club;
use App\Models\User;
use Illuminate\Http\UploadedFile;

pest()->group('clubs', 'create-club');

test('a super admin can create a club', function () {
    $user = User::factory()->create([
        'role' => AppRole::SUPER_ADMIN,
    ]);

    $response = $this->actingAs($user)->post('/clubs', [
        'name' => 'Test Club',
        'description' => 'Test Description',
        'logo' => UploadedFile::fake()->image('test.jpg'),
        'slug' => 'test-club',
    ]);

    $this->assertDatabaseHas('clubs', [
        'name' => 'Test Club',
        'description' => 'Test Description',
        'slug' => 'test-club',
    ]);

    $club = Club::where('name', 'Test Club')->first();

    $response->assertRedirect(route('clubs.show', $club));
});

test('a club admin can create a club', function () {
    $user = User::factory()->create([
        'role' => AppRole::CLUB_ADMIN,
    ]);

    $response = $this->actingAs($user)->post('/clubs', [
        'name' => 'Test Club',
        'description' => 'Test Description',
        'logo' => UploadedFile::fake()->image('test.jpg'),
        'slug' => 'test-club',
    ]);

    $this->assertDatabaseHas('clubs', [
        'name' => 'Test Club',
        'description' => 'Test Description',
        'slug' => 'test-club',
    ]);

    $club = Club::where('name', 'Test Club')->first();

    $response->assertRedirect(route('clubs.show', $club));
});

test('a regular user cannot create a club', function () {
    $user = User::factory()->create([
        'role' => AppRole::USER,
    ]);

    $response = $this->actingAs($user)->post('/clubs', [
        'name' => 'Test Club',
        'description' => 'Test Description',
        'logo' => UploadedFile::fake()->image('test.jpg'),
        'slug' => 'test-club',
    ]);

    $response->assertForbidden();
});

test('someone who is not logged in cannot create a club', function () {
    $response = $this->post('/clubs', [
        'name' => 'Test Club',
        'description' => 'Test Description',
        'logo' => UploadedFile::fake()->image('test.jpg'),
        'slug' => 'test-club',
    ]);

    $response->assertRedirect(route('login'));
});

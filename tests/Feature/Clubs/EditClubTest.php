<?php

declare(strict_types=1);

use App\Enums\ClubRole;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Inertia\Testing\AssertableInertia;

test('a user can visit the edit club page if they are an admin of the club', function () {
    $user = User::factory()->withClub()->create();

    $this->actingAs($user)
        ->get(route('clubs.edit', $user->currentClub->slug))
        ->assertOk()
        ->assertInertia(function (AssertableInertia $inertia) {
            $inertia->component('clubs/Edit')
                ->has('club', function (AssertableInertia $page) {
                    $page->where('id', fn ($value) => is_int($value))
                        ->where('name', fn ($value) => is_string($value))
                        ->where('slug', fn ($value) => is_string($value))
                        ->etc();
                });
        });
});

test('a user cannot visit the edit club page if they are not an admin of the club', function () {
    $user = User::factory()->withClub()->create();
    $nonAdminUser = User::factory()->create();
    $nonAdminUser->clubs()->attach($user->currentClub, ['role' => ClubRole::MEMBER->value]);

    $this->actingAs($nonAdminUser)
        ->get(route('clubs.edit', ['club' => $user->currentClub->slug]))
        ->assertForbidden();
});

test('a user can update the club details if they are an admin of the club', function () {
    $user = User::factory()->withClub()->create();

    $this->actingAs($user)
        ->put(route('clubs.update', $user->currentClub->slug), [
            'name' => 'Updated Club Name',
            'slug' => 'updated-club-name',
            'description' => 'Updated Description',
            'logo' => UploadedFile::fake()->image('updated-logo.jpg'),
        ])
        ->assertRedirect(route('clubs.edit', $user->currentClub->refresh()->slug));

    $user->currentClub->refresh();
    expect($user->currentClub->name)->toBe('Updated Club Name')
        ->and($user->currentClub->description)->toBe('Updated Description');
});

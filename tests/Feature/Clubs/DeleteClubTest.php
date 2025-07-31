<?php

declare(strict_types=1);

use App\Models\Club;
use App\Models\User;

test('a user can delete their own club', function () {
    $user = User::factory()->withClub()->create([
        'password' => 'password',
    ]);
    $this->actingAs($user);

    $club = $user->currentClub;

    $response = $this->delete(route('clubs.destroy', $club), [
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard'));

    $this->assertDatabaseMissing('clubs', [
        'id' => $club->id,
        'name' => $club->name,
    ]);

    $this->assertDatabaseMissing('club_user', [
        'club_id' => $club->id,
        'user_id' => $user->id,
    ]);
});

test('a user cannot delete a club they do not own', function () {
    $user = User::factory()->create([
        'password' => 'password',
    ]);
    $this->actingAs($user);

    $club = Club::factory()->create();

    $response = $this->delete(route('clubs.destroy', $club), [
        'password' => 'password',
    ]);

    $response->assertForbidden();

    $this->assertDatabaseHas('clubs', [
        'id' => $club->id,
        'name' => $club->name,
    ]);
});

test('a user must provide their password to delete a club', function () {
    $user = User::factory()->withClub()->create([
        'password' => 'password',
    ]);
    $this->actingAs($user);

    $club = $user->currentClub;

    $response = $this->delete(route('clubs.destroy', $club), [
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors(['password']);

    $this->assertDatabaseHas('clubs', [
        'id' => $club->id,
        'name' => $club->name,
    ]);
});

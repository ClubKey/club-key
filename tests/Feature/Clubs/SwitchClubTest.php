<?php

declare(strict_types=1);

use App\Enums\ClubRole;
use App\Models\Club;
use App\Models\User;

test('a member of a club can switch to a club they are a member of', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $anotherClub = Club::factory()->create();
    $user->clubs()->attach($club, ['role' => ClubRole::MEMBER->value]);
    $user->clubs()->attach($anotherClub, ['role' => ClubRole::MEMBER->value]);
    $user->current_club_id = $club->id;
    $user->save();
    $this->actingAs($user);

    $response = $this->post(route('clubs.switch', ['club' => $anotherClub->slug]));
    $response->assertRedirect(route('dashboard'));
    $this->assertEquals($anotherClub->id, $user->fresh()->current_club_id);
    $this->assertTrue($user->fresh()->belongsToClub($anotherClub));
    $this->assertTrue($user->fresh()->belongsToClub($club));
});

test('a member of a club cannot switch to a club they are not a member of', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $anotherClub = Club::factory()->create();
    $user->clubs()->attach($club, ['role' => ClubRole::MEMBER->value]);
    $user->current_club_id = $club->id;
    $user->save();
    $this->actingAs($user);

    $response = $this->post(route('clubs.switch', ['club' => $anotherClub->slug]));
    $response->assertForbidden();
    $this->assertEquals($club->id, $user->fresh()->current_club_id);
});

test('an admin of a club can switch to a club they are a member of', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $anotherClub = Club::factory()->create();
    $user->clubs()->attach($club, ['role' => ClubRole::ADMIN->value]);
    $user->clubs()->attach($anotherClub, ['role' => ClubRole::MEMBER->value]);
    $user->current_club_id = $club->id;
    $user->save();
    $this->actingAs($user);

    $response = $this->post(route('clubs.switch', ['club' => $anotherClub->slug]));
    $response->assertRedirect(route('dashboard'));
    $this->assertEquals($anotherClub->id, $user->fresh()->current_club_id);
});

<?php

declare(strict_types=1);

use App\Enums\AppRole;
use App\Enums\ClubRole;
use App\Models\Club;
use App\Models\ClubUser;
use App\Models\User;

pest()->group('clubs', 'visits', 'members');

test('an admin can log a visit for a member', function () {
    $club = Club::factory()->create();
    $admin = User::factory()->create();
    $member = User::factory()->create();

    // Make admin an admin of the club
    $club->members()->attach($admin, ['role' => ClubRole::ADMIN]);

    // Make member a member of the club
    $club->members()->attach($member, ['role' => ClubRole::MEMBER]);

    // Get the ClubUser model
    $clubUser = ClubUser::where('club_id', $club->id)
        ->where('user_id', $member->id)
        ->first();

    // Admin switches to this club
    $admin->update(['current_club_id' => $club->id]);

    $response = $this->actingAs($admin)->post(route('members.visits.create', $clubUser->id));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('visits', [
        'club_id' => $club->id,
        'user_id' => $member->id,
    ]);
});

test('a super admin can log a visit for a member', function () {
    $club = Club::factory()->create();
    $superAdmin = User::factory()->create([
        'role' => AppRole::SUPER_ADMIN,
    ]);
    $member = User::factory()->create();

    // Make member a member of the club
    $club->members()->attach($member, ['role' => ClubRole::MEMBER]);

    // Get the ClubUser model
    $clubUser = ClubUser::where('club_id', $club->id)
        ->where('user_id', $member->id)
        ->first();

    $response = $this->actingAs($superAdmin)->post(route('members.visits.create', $clubUser->id));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('visits', [
        'club_id' => $club->id,
        'user_id' => $member->id,
    ]);
});

test('a regular member cannot log a visit for another member', function () {
    $club = Club::factory()->create();
    $regularMember = User::factory()->create();
    $targetMember = User::factory()->create();

    // Make regular member a member of the club
    $club->members()->attach($regularMember, ['role' => ClubRole::MEMBER]);
    $regularMember->update(['current_club_id' => $club->id]);

    // Make target member a member of the club
    $club->members()->attach($targetMember, ['role' => ClubRole::MEMBER]);

    // Get the ClubUser model
    $clubUser = ClubUser::where('club_id', $club->id)
        ->where('user_id', $targetMember->id)
        ->first();

    $response = $this->actingAs($regularMember)->post(route('members.visits.create', $clubUser->id));

    $response->assertForbidden();

    $this->assertDatabaseMissing('visits', [
        'club_id' => $club->id,
        'user_id' => $targetMember->id,
    ]);
});

test('a visit logged by admin creates a visit record with correct data', function () {
    $club = Club::factory()->create();
    $admin = User::factory()->create();
    $member = User::factory()->create();

    // Make admin an admin of the club
    $club->members()->attach($admin, ['role' => ClubRole::ADMIN]);
    $admin->update(['current_club_id' => $club->id]);

    // Make member a member of the club
    $club->members()->attach($member, ['role' => ClubRole::MEMBER]);

    // Get the ClubUser model
    $clubUser = ClubUser::where('club_id', $club->id)
        ->where('user_id', $member->id)
        ->first();

    $this->actingAs($admin)->post(route('members.visits.create', $clubUser->id));

    $visit = $member->visits()->where('club_id', $club->id)->first();

    expect($visit)->not->toBeNull();
    expect($visit->user_id)->toBe($member->id);
    expect($visit->club_id)->toBe($club->id);
});

test('a visit can be logged for a member multiple times on the same day', function () {
    $club = Club::factory()->create();
    $admin = User::factory()->create();
    $member = User::factory()->create();

    // Make admin an admin of the club
    $club->members()->attach($admin, ['role' => ClubRole::ADMIN]);
    $admin->update(['current_club_id' => $club->id]);

    // Make member a member of the club
    $club->members()->attach($member, ['role' => ClubRole::MEMBER]);

    // Get the ClubUser model
    $clubUser = ClubUser::where('club_id', $club->id)
        ->where('user_id', $member->id)
        ->first();

    // First visit
    $this->actingAs($admin)->post(route('members.visits.create', $clubUser->id));

    // Wait a bit to avoid the 10-minute duplicate check
    $this->travel(11)->minutes();

    // Second visit
    $this->actingAs($admin)->post(route('members.visits.create', $clubUser->id));

    $visits = $member->visits()->where('club_id', $club->id)->get();

    expect($visits)->toHaveCount(2);
});

test('duplicate visits within 10 minutes are prevented', function () {
    $club = Club::factory()->create();
    $admin = User::factory()->create();
    $member = User::factory()->create();

    // Make admin an admin of the club
    $club->members()->attach($admin, ['role' => ClubRole::ADMIN]);
    $admin->update(['current_club_id' => $club->id]);

    // Make member a member of the club
    $club->members()->attach($member, ['role' => ClubRole::MEMBER]);

    // Get the ClubUser model
    $clubUser = ClubUser::where('club_id', $club->id)
        ->where('user_id', $member->id)
        ->first();

    // First visit
    $this->actingAs($admin)->post(route('members.visits.create', $clubUser->id));

    // Try to log another visit immediately
    $this->actingAs($admin)->post(route('members.visits.create', $clubUser->id));

    $visits = $member->visits()->where('club_id', $club->id)->get();

    // Should only have one visit due to 10-minute duplicate prevention
    expect($visits)->toHaveCount(1);
});

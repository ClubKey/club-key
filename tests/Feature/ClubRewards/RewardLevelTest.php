<?php

declare(strict_types=1);

use App\Models\Club;
use App\Models\RewardLevel;
use App\Models\User;

pest()->group('feature', 'club-rewards', 'reward-levels');

it('allows an admin to view reward levels index', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $club->admins()->attach($user, ['role' => 'admin']);
    $user->update(['current_club_id' => $club->id]);

    $response = $this->actingAs($user)
        ->get(route('reward-levels.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('reward-levels/Index')
        ->has('rewardLevels')
    );
});

it('allows an admin to create a reward level', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $club->admins()->attach($user, ['role' => 'admin']);
    $user->update(['current_club_id' => $club->id]);

    $rewardLevelData = [
        'name' => 'Gold Level',
        'icon_name' => 'trophy',
        'min_visits_per_month' => 10,
        'color' => '#ffd700',
        'description' => 'Premium rewards for frequent visitors',
        'percentage_discount' => 15,
        'order' => 1,
    ];

    $response = $this->actingAs($user)
        ->post(route('reward-levels.store'), $rewardLevelData);

    $response->assertRedirect(route('reward-levels.index'));
    $this->assertDatabaseHas('reward_levels', [
        'club_id' => $club->id,
        'name' => 'Gold Level',
        'min_visits_per_month' => 10,
        'percentage_discount' => 15,
    ]);
});

it('allows an admin to edit a reward level', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $club->admins()->attach($user, ['role' => 'admin']);
    $user->update(['current_club_id' => $club->id]);

    $rewardLevel = RewardLevel::factory()->create([
        'club_id' => $club->id,
    ]);

    $response = $this->actingAs($user)
        ->get(route('reward-levels.edit', $rewardLevel));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('reward-levels/Edit')
        ->has('rewardLevel')
    );
});

it('allows an admin to update a reward level', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $club->admins()->attach($user, ['role' => 'admin']);
    $user->update(['current_club_id' => $club->id]);

    $rewardLevel = RewardLevel::factory()->create([
        'club_id' => $club->id,
        'name' => 'Old Name',
    ]);

    $updateData = [
        'name' => 'Updated Name',
        'min_visits_per_month' => 20,
        'percentage_discount' => 25,
    ];

    $response = $this->actingAs($user)
        ->put(route('reward-levels.update', $rewardLevel), $updateData);

    $response->assertRedirect(route('reward-levels.index'));
    $this->assertDatabaseHas('reward_levels', [
        'id' => $rewardLevel->id,
        'name' => 'Updated Name',
        'min_visits_per_month' => 20,
        'percentage_discount' => 25,
    ]);
});

it('allows an admin to delete a reward level', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $club->admins()->attach($user, ['role' => 'admin']);
    $user->update(['current_club_id' => $club->id]);

    $rewardLevel = RewardLevel::factory()->create([
        'club_id' => $club->id,
    ]);

    $response = $this->actingAs($user)
        ->delete(route('reward-levels.destroy', $rewardLevel));

    $response->assertRedirect(route('reward-levels.index'));
    $this->assertDatabaseMissing('reward_levels', [
        'id' => $rewardLevel->id,
    ]);
});

it('prevents non-admins from accessing reward levels', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $club->members()->attach($user, ['role' => 'member']);
    $user->update(['current_club_id' => $club->id]);

    $response = $this->actingAs($user)
        ->get(route('reward-levels.index'));

    $response->assertStatus(403);
});

it('prevents a user without a current club from accessing reward levels', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $club->admins()->attach($user, ['role' => 'admin']);
    // Note: no current_club_id set

    $response = $this->actingAs($user)
        ->get(route('reward-levels.index'));

    $response->assertStatus(403);
});

it('prevents an admin from accessing reward levels from a different club', function () {
    $user = User::factory()->create();
    $club1 = Club::factory()->create();
    $club2 = Club::factory()->create();

    $club1->admins()->attach($user, ['role' => 'admin']);
    $user->update(['current_club_id' => $club1->id]);

    $rewardLevel = RewardLevel::factory()->create([
        'club_id' => $club2->id,
    ]);

    $response = $this->actingAs($user)
        ->get(route('reward-levels.edit', $rewardLevel));

    $response->assertStatus(403);
});

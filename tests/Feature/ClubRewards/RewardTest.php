<?php

declare(strict_types=1);

use App\Models\Club;
use App\Models\Reward;
use App\Models\RewardLevel;
use App\Models\User;

pest()->group('feature', 'club-rewards', 'rewards');

it('allows an admin to create a reward', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $club->admins()->attach($user, ['role' => 'admin']);
    $user->update(['current_club_id' => $club->id]);

    $rewardLevel = RewardLevel::factory()->create([
        'club_id' => $club->id,
    ]);

    $rewardData = [
        'description' => 'Free coffee on your birthday',
        'order' => 1,
    ];

    $response = $this->actingAs($user)
        ->post(route('reward-levels.rewards.store', $rewardLevel), $rewardData);

    $response->assertRedirect(route('reward-levels.edit', $rewardLevel));
    $this->assertDatabaseHas('rewards', [
        'reward_level_id' => $rewardLevel->id,
        'description' => 'Free coffee on your birthday',
        'order' => 1,
    ]);
});

it('allows an admin to edit a reward', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $club->admins()->attach($user, ['role' => 'admin']);
    $user->update(['current_club_id' => $club->id]);

    $rewardLevel = RewardLevel::factory()->create([
        'club_id' => $club->id,
    ]);

    $reward = Reward::factory()->create([
        'reward_level_id' => $rewardLevel->id,
    ]);

    $response = $this->actingAs($user)
        ->get(route('reward-levels.rewards.edit', [$rewardLevel, $reward]));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('rewards/Edit')
        ->has('reward')
    );
});

it('allows an admin to update a reward', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $club->admins()->attach($user, ['role' => 'admin']);
    $user->update(['current_club_id' => $club->id]);

    $rewardLevel = RewardLevel::factory()->create([
        'club_id' => $club->id,
    ]);

    $reward = Reward::factory()->create([
        'reward_level_id' => $rewardLevel->id,
        'description' => 'Old reward text',
    ]);

    $updateData = [
        'description' => 'Updated reward text',
        'order' => 2,
    ];

    $response = $this->actingAs($user)
        ->put(route('reward-levels.rewards.update', [$rewardLevel, $reward]), $updateData);

    $response->assertRedirect(route('reward-levels.edit', $rewardLevel));
    $this->assertDatabaseHas('rewards', [
        'id' => $reward->id,
        'description' => 'Updated reward text',
        'order' => 2,
    ]);
});

it('allows an admin to delete a reward', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $club->admins()->attach($user, ['role' => 'admin']);
    $user->update(['current_club_id' => $club->id]);

    $rewardLevel = RewardLevel::factory()->create([
        'club_id' => $club->id,
    ]);

    $reward = Reward::factory()->create([
        'reward_level_id' => $rewardLevel->id,
    ]);

    $response = $this->actingAs($user)
        ->delete(route('reward-levels.rewards.destroy', [$rewardLevel, $reward]));

    $response->assertRedirect(route('reward-levels.edit', $rewardLevel));
    $this->assertDatabaseMissing('rewards', [
        'id' => $reward->id,
    ]);
});

it('prevents non-admins from creating a reward', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $club->members()->attach($user, ['role' => 'member']);
    $user->update(['current_club_id' => $club->id]);

    $rewardLevel = RewardLevel::factory()->create([
        'club_id' => $club->id,
    ]);

    $rewardData = [
        'description' => 'Free coffee on your birthday',
        'order' => 1,
    ];

    $response = $this->actingAs($user)
        ->post(route('reward-levels.rewards.store', $rewardLevel), $rewardData);

    $response->assertStatus(403);
});

it('prevents an admin from creating a reward for a different club', function () {
    $user = User::factory()->create();
    $club1 = Club::factory()->create();
    $club2 = Club::factory()->create();

    $club1->admins()->attach($user, ['role' => 'admin']);
    $user->update(['current_club_id' => $club1->id]);

    $rewardLevel = RewardLevel::factory()->create([
        'club_id' => $club2->id,
    ]);

    $rewardData = [
        'description' => 'Free coffee on your birthday',
        'order' => 1,
    ];

    $response = $this->actingAs($user)
        ->post(route('reward-levels.rewards.store', $rewardLevel), $rewardData);

    $response->assertStatus(404);
});

it('prevents an admin from editing a reward from a different club', function () {
    $user = User::factory()->create();
    $club1 = Club::factory()->create();
    $club2 = Club::factory()->create();

    $club1->admins()->attach($user, ['role' => 'admin']);
    $user->update(['current_club_id' => $club1->id]);

    $rewardLevel = RewardLevel::factory()->create([
        'club_id' => $club2->id,
    ]);

    $reward = Reward::factory()->create([
        'reward_level_id' => $rewardLevel->id,
    ]);

    $response = $this->actingAs($user)
        ->get(route('reward-levels.rewards.edit', [$rewardLevel, $reward]));

    $response->assertStatus(403);
});

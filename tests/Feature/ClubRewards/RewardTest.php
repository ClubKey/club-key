<?php

declare(strict_types=1);

namespace Tests\Feature\ClubRewards;

use App\Models\Club;
use App\Models\Reward;
use App\Models\RewardLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RewardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_reward(): void
    {
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
    }

    public function test_admin_can_edit_reward(): void
    {
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
            ->component('club-rewards/Rewards/Edit')
            ->has('reward')
        );
    }

    public function test_admin_can_update_reward(): void
    {
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
    }

    public function test_admin_can_delete_reward(): void
    {
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
    }

    public function test_non_admin_cannot_create_reward(): void
    {
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
    }

    public function test_admin_cannot_create_reward_for_different_club(): void
    {
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
    }

    public function test_admin_cannot_edit_reward_from_different_club(): void
    {
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
    }
}

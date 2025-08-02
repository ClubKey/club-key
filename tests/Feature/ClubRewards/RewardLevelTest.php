<?php

declare(strict_types=1);

namespace Tests\Feature\ClubRewards;

use App\Models\Club;
use App\Models\RewardLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RewardLevelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_reward_levels_index(): void
    {
        $user = User::factory()->create();
        $club = Club::factory()->create();
        $club->admins()->attach($user, ['role' => 'admin']);
        $user->update(['current_club_id' => $club->id]);

        $response = $this->actingAs($user)
            ->get(route('reward-levels.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('club-rewards/RewardLevels/Index')
            ->has('rewardLevels')
        );
    }

    public function test_admin_can_create_reward_level(): void
    {
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
    }

    public function test_admin_can_edit_reward_level(): void
    {
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
            ->component('club-rewards/RewardLevels/Edit')
            ->has('rewardLevel')
        );
    }

    public function test_admin_can_update_reward_level(): void
    {
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
    }

    public function test_admin_can_delete_reward_level(): void
    {
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
    }

    public function test_non_admin_cannot_access_reward_levels(): void
    {
        $user = User::factory()->create();
        $club = Club::factory()->create();
        $club->members()->attach($user, ['role' => 'member']);
        $user->update(['current_club_id' => $club->id]);

        $response = $this->actingAs($user)
            ->get(route('reward-levels.index'));

        $response->assertStatus(403);
    }

    public function test_user_without_current_club_cannot_access_reward_levels(): void
    {
        $user = User::factory()->create();
        $club = Club::factory()->create();
        $club->admins()->attach($user, ['role' => 'admin']);
        // Note: user has no current_club_id set

        $response = $this->actingAs($user)
            ->get(route('reward-levels.index'));

        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_reward_level_from_different_club(): void
    {
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
    }
}

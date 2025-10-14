<?php

declare(strict_types=1);

namespace App\Actions\ClubRewards;

use App\Actions\Clubs\UpdateUserRewardLevelAction;
use App\Models\ClubUser;
use App\Models\RewardLevel;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteRewardLevelAction
{
    public function __construct(
        private UpdateUserRewardLevelAction $updateUserRewardLevelAction,
        private DeleteRewardAction $deleteRewardAction,
    ) {}

    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(RewardLevel $rewardLevel): void
    {
        DB::transaction(function () use ($rewardLevel): void {
            // Delete associated rewards first
            $rewardLevel->rewards()->each(function ($reward) {
                $this->deleteRewardAction->handle($reward);
            });

            $clubUsers = ClubUser::where('reward_level_id', $rewardLevel->id)
                ->orWhere('next_reward_level_id', $rewardLevel->id)
                ->get();

            // Then delete the reward level
            $rewardLevel->delete();

            $clubUsers->fresh()->each(function ($user) {
                $this->updateUserRewardLevelAction->handle($user);
            });
        });
    }
}

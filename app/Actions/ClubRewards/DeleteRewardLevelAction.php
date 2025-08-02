<?php

declare(strict_types=1);

namespace App\Actions\ClubRewards;

use App\Models\RewardLevel;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteRewardLevelAction
{
    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(RewardLevel $rewardLevel): void
    {
        DB::transaction(function () use ($rewardLevel): void {
            // Delete associated rewards first
            $rewardLevel->rewards()->delete();

            // Then delete the reward level
            $rewardLevel->delete();
        });
    }
}

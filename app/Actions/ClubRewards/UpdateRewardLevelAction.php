<?php

declare(strict_types=1);

namespace App\Actions\ClubRewards;

use App\DTOs\RewardLevelDto;
use App\Models\RewardLevel;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateRewardLevelAction
{
    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(RewardLevelDto $dto, RewardLevel $rewardLevel): void
    {
        DB::transaction(function () use ($dto, $rewardLevel): void {
            $rewardLevel->update($dto->toArray());
        });
    }
}

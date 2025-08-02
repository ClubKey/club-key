<?php

declare(strict_types=1);

namespace App\Actions\ClubRewards;

use App\DTOs\RewardLevelDto;
use App\Models\RewardLevel;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateRewardLevelAction
{
    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(RewardLevelDto $dto): RewardLevel
    {
        return DB::transaction(function () use ($dto): RewardLevel {
            return RewardLevel::create($dto->toArray());
        });
    }
}

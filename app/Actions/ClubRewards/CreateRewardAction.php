<?php

declare(strict_types=1);

namespace App\Actions\ClubRewards;

use App\DTOs\RewardDto;
use App\Models\Reward;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateRewardAction
{
    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(RewardDto $dto): Reward
    {
        return DB::transaction(function () use ($dto): Reward {
            return Reward::create($dto->toArray());
        });
    }
}

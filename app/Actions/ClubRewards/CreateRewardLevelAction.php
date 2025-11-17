<?php

declare(strict_types=1);

namespace App\Actions\ClubRewards;

use App\Actions\Clubs\UpdateUserRewardLevelAction;
use App\DTOs\RewardLevelDto;
use App\Models\RewardLevel;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateRewardLevelAction
{
    public function __construct(
        private UpdateUserRewardLevelAction $updateUserRewardLevelAction,
    ) {}

    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(RewardLevelDto $dto): RewardLevel
    {
        return DB::transaction(function () use ($dto): RewardLevel {
            $rewardLevel = RewardLevel::create($dto->toArray());

            $users = $rewardLevel->club->clubUsers;

            foreach ($users as $user) {
                $this->updateUserRewardLevelAction->handle($user);
            }

            return $rewardLevel;
        });
    }
}

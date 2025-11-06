<?php

declare(strict_types=1);

namespace App\Actions\ClubRewards;

use App\Actions\Clubs\UpdateUserRewardLevelAction;
use App\DTOs\RewardLevelDto;
use App\Models\RewardLevel;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateRewardLevelAction
{
    public function __construct(
        private UpdateUserRewardLevelAction $updateUserRewardLevelAction,
    ) {}

    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(RewardLevelDto $dto, RewardLevel $rewardLevel): void
    {
        DB::transaction(function () use ($dto, $rewardLevel): void {
            $rewardLevel->update($dto->toArray());

            if ($rewardLevel->wasChanged('min_visits_per_month')) {
                $users = $rewardLevel->club->clubUsers;

                foreach ($users as $user) {
                    $this->updateUserRewardLevelAction->handle($user);
                }
            }
        });
    }
}

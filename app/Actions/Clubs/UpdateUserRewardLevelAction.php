<?php

declare(strict_types=1);

namespace App\Actions\Clubs;

use App\Models\ClubUser;
use Illuminate\Support\Facades\DB;

final readonly class UpdateUserRewardLevelAction
{
    /**
     * Execute the action.
     */
    public function handle(ClubUser $clubUser): void
    {
        DB::transaction(function () use ($clubUser): void {
            $user = $clubUser->user;
            $club = $clubUser->club;
            $visitsInLastMonth = $user->visitsForClub($club)->where('created_at', '>=', now()->subMonth())->count();

            $rewardLevel = $club->rewardLevels()->where('min_visits_per_month', '<=', $visitsInLastMonth)
                ->orderByDesc('min_visits_per_month')
                ->first();

            $clubUser->reward_level_id = $rewardLevel?->id;

            $clubUser->save();
        });
    }
}

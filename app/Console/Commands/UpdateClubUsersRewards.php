<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Clubs\UpdateUserRewardLevelAction;
use App\Models\ClubUser;
use Illuminate\Console\Command;
use Throwable;

final class UpdateClubUsersRewards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-club-users-rewards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the reward levels for all club users based on their recent activity';

    /**
     * Execute the console command.
     *
     * @throws Throwable
     */
    public function handle(): void
    {
        $clubUsers = ClubUser::with(['user', 'club'])->get();

        $updateRewardLevelAction = new UpdateUserRewardLevelAction();
        foreach ($clubUsers as $clubUser) {
            $updateRewardLevelAction->handle($clubUser);
            $this->info("Updated rewards for ClubUser ID: {$clubUser->id}");
        }
    }
}

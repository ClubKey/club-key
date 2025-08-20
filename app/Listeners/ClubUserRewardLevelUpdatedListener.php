<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ClubUserRewardLevelUpdated;
use App\Notifications\YourRewardLevelHasChanged;

final class ClubUserRewardLevelUpdatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ClubUserRewardLevelUpdated $event): void
    {
        $event->clubUser->user->notify(new YourRewardLevelHasChanged($event->clubUser, $event->previousRewardLevel));
    }
}

<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UserJoinedClub;
use App\Notifications\WelcomeNewClubUser;

final class UserJoinedClubListener
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
    public function handle(UserJoinedClub $event): void
    {

        $event->clubUser->user->notify(new WelcomeNewClubUser($event->clubUser));
    }
}

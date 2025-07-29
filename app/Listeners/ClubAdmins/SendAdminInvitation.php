<?php

declare(strict_types=1);

namespace App\Listeners\ClubAdmins;

use App\Events\ClubAdmins\AdminInvitationCreated;
use App\Mail\InviteAdmin;
use Illuminate\Support\Facades\Mail;

final class SendAdminInvitation
{
    /**
     * Create the event listener.
     */
    public function __construct(
    ) {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AdminInvitationCreated $event): void
    {
        Mail::to($event->adminInvitation->email)->send(new InviteAdmin($event->adminInvitation));
    }
}

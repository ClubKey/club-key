<?php

declare(strict_types=1);

namespace App\Listeners\Clubs;

use App\Events\Clubs\AdminNotificationResent;
use App\Mail\InviteAdmin;
use Illuminate\Support\Facades\Mail;

final class SendResendInvitation
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
    public function handle(AdminNotificationResent $event): void
    {
        Mail::to($event->adminInvitation->email)->send(new InviteAdmin($event->adminInvitation));
    }
}

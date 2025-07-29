<?php

declare(strict_types=1);

namespace App\Actions\AdminInvitations;

use App\Events\Clubs\AdminNotificationResent;
use App\Models\AdminInvitation;
use Illuminate\Support\Facades\DB;

final readonly class ResendAdminInvitationAction
{
    /**
     * Execute the action.
     */
    public function handle(AdminInvitation $adminInvitation): void
    {
        DB::transaction(function () use ($adminInvitation): void {
            $adminInvitation->update([
                'expires_at' => now()->addDays(30),
            ]);

            AdminNotificationResent::dispatch($adminInvitation);
        });
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\AdminInvitations;

use App\Models\AdminInvitation;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class RevokeAdminInvitationAction
{
    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(AdminInvitation $adminInvitation): void
    {
        DB::transaction(function () use ($adminInvitation): void {
            $adminInvitation->delete();
        });
    }
}

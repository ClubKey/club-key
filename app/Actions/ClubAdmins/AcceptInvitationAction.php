<?php

declare(strict_types=1);

namespace App\Actions\ClubAdmins;

use App\Actions\Clubs\SwitchCurrentClubAction;
use App\Models\AdminInvitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class AcceptInvitationAction
{
    public function __construct(
        private SwitchCurrentClubAction $switchCurrentClubAction
    ) {}

    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(AdminInvitation $adminInvitation, User $user): void
    {
        DB::transaction(function () use ($adminInvitation, $user): void {
            $adminInvitation->club->members()->attach($user->id, [
                'role' => $adminInvitation->role->value,
            ]);

            $this->switchCurrentClubAction->handle($user, $adminInvitation->club);

            // Delete the invitation after successful acceptance
            $adminInvitation->delete();
        });
    }
}

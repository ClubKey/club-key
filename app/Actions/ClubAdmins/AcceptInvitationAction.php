<?php

declare(strict_types=1);

namespace App\Actions\ClubAdmins;

use App\Actions\Clubs\SwitchCurrentClubAction;
use App\Enums\ClubRole;
use App\Events\AdminAddedToClub;
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

            // Dispatch event if the role is ADMIN
            if ($adminInvitation->role === ClubRole::ADMIN) {
                AdminAddedToClub::dispatch($adminInvitation->club, $user);
            }

            // Delete the invitation after successful acceptance
            $adminInvitation->delete();
        });
    }
}

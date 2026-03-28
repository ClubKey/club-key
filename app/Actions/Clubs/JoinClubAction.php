<?php

declare(strict_types=1);

namespace App\Actions\Clubs;

use App\Actions\CommunicationSubscriptions\LinkSubscriptionsToUserAction;
use App\Enums\ClubRole;
use App\Events\UserJoinedClub;
use App\Models\Club;
use App\Models\ClubUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class JoinClubAction
{
    public function __construct(
        private LinkSubscriptionsToUserAction $linkSubscriptionsToUserAction,
    ) {}

    /**
     * Returns true if the user is a new club user.
     *
     * @throws Throwable
     */
    public function handle(User $user, Club $club): bool
    {
        return DB::transaction(function () use ($user, $club): bool {
            if (! $club->members->contains($user)) {
                $club->members()->attach($user, [
                    'role' => ClubRole::MEMBER,
                ]);

                $clubUser = ClubUser::where([
                    'club_id' => $club->id,
                    'user_id' => $user->id,
                ])->first();

                $this->linkSubscriptionsToUserAction->handle($user, $club);

                event(new UserJoinedClub($clubUser));

                return true;
            }

            return false;
        });
    }
}

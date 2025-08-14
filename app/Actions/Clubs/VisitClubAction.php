<?php

declare(strict_types=1);

namespace App\Actions\Clubs;

use App\Models\Club;
use App\Models\ClubLocation;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class VisitClubAction
{
    public function __construct(private JoinClubAction $joinClubAction) {}

    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(User $user, Club $club, ?ClubLocation $clubLocation = null): void
    {
        DB::transaction(function () use ($user, $club, $clubLocation): void {
            if (! $user->belongsToClub($club)) {
                $this->joinClubAction->handle($user, $club);
            }

            $visitsInLastTwoHours = $user->visits()
                ->whereClubId($club->id)
                ->whereClubLocationId($clubLocation?->id)
                ->where('created_at', '>', now()->subHours(2));

            if ($visitsInLastTwoHours->doesntExist()) {
                Visit::create([
                    'user_id' => $user->id,
                    'club_id' => $club->id,
                    'club_location_id' => $clubLocation?->id,
                ]);

            }
        });
    }
}

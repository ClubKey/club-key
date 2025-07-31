<?php

declare(strict_types=1);

namespace App\Actions\Clubs;

use App\Models\Club;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteClubAction
{
    public function __construct(private SwitchCurrentClubAction $switchCurrentClub) {}

    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(Club $club, User $user): void
    {
        DB::transaction(function () use ($club, $user): void {
            $club->members()->detach();
            $club->delete();

            /** @var Club $clubToSwitchTo */
            $clubToSwitchTo = $user->clubs()
                ->first();

            if ($clubToSwitchTo) {
                $this->switchCurrentClub->handle($user, $clubToSwitchTo);
            }
        });
    }
}

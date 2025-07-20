<?php

declare(strict_types=1);

namespace App\Actions\Clubs;

use App\Models\Club;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class SwitchCurrentClubAction
{
    /**
     * Execute the action.
     */
    public function handle(User $user, Club $toClub): void
    {
        DB::transaction(function () use ($user, $toClub): void {
            $user->currentClub()->associate($toClub);
            $user->save();
        });
    }
}

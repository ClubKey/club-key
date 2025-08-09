<?php

declare(strict_types=1);

namespace App\Actions\Clubs;

use App\Enums\ClubRole;
use App\Models\Club;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class JoinClubAction
{
    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(User $user, Club $club): void
    {
        DB::transaction(function () use ($user, $club): void {
            if (! $club->members->contains($user)) {
                $club->members()->attach($user, [
                    'role' => ClubRole::MEMBER,
                ]);
            }
        });
    }
}

<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\ClubRole;
use App\Models\ClubLocation;
use App\Models\User;

final class ClubLocationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ClubLocation $clubLocation): bool
    {
        return $user->belongsToClub($clubLocation->club);
    }

    public function create(User $user): bool
    {
        return $user->currentClub && $user->hasRoleInClub($user->currentClub, ClubRole::ADMIN);
    }

    public function update(User $user, ClubLocation $clubLocation): bool
    {
        return $user->hasRoleInClub($clubLocation->club, ClubRole::ADMIN);
    }

    public function delete(User $user, ClubLocation $clubLocation): bool
    {
        return $user->hasRoleInClub($clubLocation->club, ClubRole::ADMIN);
    }
}

<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\ClubRole;
use App\Models\Club;
use App\Models\User;

final class ClubPolicy
{
    public function create(User $user): bool
    {
        return $user->role->canManageAClub();
    }

    public function update(User $user, Club $club): bool
    {
        return $user->hasRoleInClub($club, ClubRole::ADMIN) || $user->isSuperAdmin();
    }

    public function view(?User $user, Club $club): bool
    {
        // Allow all users to view the club, including guests.
        return true;
    }

    public function inviteAdmins(User $user, Club $club): bool
    {
        return $user->hasRoleInClub($club, ClubRole::ADMIN);
    }

    public function removeAdmins(User $user, Club $club): bool
    {
        return $user->ownsClub($club);
    }

    public function delete(User $user, Club $club): bool
    {
        return $user->ownsClub($club) || $user->isSuperAdmin();
    }

    public function switch(User $user, Club $club): bool
    {
        return $user->belongsToClub($club) || $user->isSuperAdmin();
    }
}

<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\ClubRole;
use App\Models\Club;
use App\Models\ClubUser;
use App\Models\User;

final class ClubUserPolicy
{
    public function viewAny(User $user, ?Club $club = null): bool
    {
        if ($club === null) {
            return false;
        }

        return $user->hasRoleInClub($club, ClubRole::ADMIN) || $user->isSuperAdmin();
    }

    public function view(User $user, ClubUser $clubUser): bool
    {
        return $user->hasRoleInClub($clubUser->club, ClubRole::ADMIN) || $user->isSuperAdmin();
    }
}

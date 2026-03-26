<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\AppRole;
use App\Enums\ClubRole;
use App\Models\CommunicationSubscription;
use App\Models\User;

final class CommunicationSubscriptionPolicy
{
    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user): bool
    {
        return $user->currentClub &&
            ($user->hasRoleInClub($user->currentClub, ClubRole::ADMIN) || $user->role === AppRole::SUPER_ADMIN);
    }

    public function view(User $user, CommunicationSubscription $communicationSubscription): bool
    {
        return $user->currentClub && ($user->hasRoleInClub($communicationSubscription->club, ClubRole::ADMIN) || $user->role === AppRole::SUPER_ADMIN);
    }
}

<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\ClubRole;
use App\Models\Promotion;
use App\Models\User;

final class PromotionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRoleInClub($user->currentClub, ClubRole::ADMIN);
    }

    public function view(User $user, Promotion $promotion): bool
    {
        return $user->hasRoleInClub($promotion->club, ClubRole::ADMIN);
    }

    public function create(User $user): bool
    {
        return $user->hasRoleInClub($user->currentClub, ClubRole::ADMIN);
    }

    public function update(User $user, Promotion $promotion): bool
    {
        return $user->hasRoleInClub($promotion->club, ClubRole::ADMIN);
    }

    public function delete(User $user, Promotion $promotion): bool
    {
        return $user->hasRoleInClub($promotion->club, ClubRole::ADMIN);
    }
}

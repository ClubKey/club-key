<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Reward;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class RewardPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->currentClub && $user->currentClub->admins->contains($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Reward $reward): bool
    {
        return $user->currentClub &&
               $reward->rewardLevel->club_id === $user->currentClub->id &&
               $user->currentClub->admins->contains($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->currentClub && $user->currentClub->admins->contains($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Reward $reward): bool
    {
        return $user->currentClub &&
               $reward->rewardLevel->club_id === $user->currentClub->id &&
               $user->currentClub->admins->contains($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Reward $reward): bool
    {
        return $user->currentClub &&
               $reward->rewardLevel->club_id === $user->currentClub->id &&
               $user->currentClub->admins->contains($user);
    }
}

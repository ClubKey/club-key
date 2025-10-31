<?php

declare(strict_types=1);

namespace App\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Contracts\User;

final readonly class CreateOrUpdateSocialUserAction
{
    /**
     * Execute the action.
     * @param User $user
     */
    public function handle(User $user): void
    {
        //@todo add SocialUser Model and table and then implement
    }
}

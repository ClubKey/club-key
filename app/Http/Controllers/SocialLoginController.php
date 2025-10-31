<?php

namespace App\Http\Controllers;

use App\Actions\CreateOrUpdateSocialUserAction;
use Illuminate\Http\Request;
use Laravel\Socialite\Socialite;

class SocialLoginController extends Controller
{
    protected array $socialProviders = [
        'google'
    ];

    public function redirect(string $provider)
    {
        if (!in_array($provider, $this->socialProviders)) {
            abort(404);
        }
        return Socialite::driver($provider)->redirect();
    }


    public function callback(string $provider, CreateOrUpdateSocialUserAction $createOrUpdateSocialUserAction)
    {
        if (!in_array($provider, $this->socialProviders)) {
            abort(404);
        }
        $user = Socialite::driver($provider)->user();
        $createOrUpdateSocialUserAction->handle($user);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Clubs\HandleInvitationAfterAuthAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

final class RegisteredUserController extends Controller
{
    public function __construct(
        private HandleInvitationAfterAuthAction $handleInvitationAfterAuthAction
    ) {}

    /**
     * Show the registration page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/Register', [
            'inviteToken' => $request->cookie('invite_token'),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $inviteToken = $request->cookie('invite_token');

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($user));

            Auth::login($user);

            return $this->handleInvitationAfterAuthAction->handle($user, $inviteToken);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw the validation exception - the cookie will persist automatically
            throw $e;
        }
    }
}

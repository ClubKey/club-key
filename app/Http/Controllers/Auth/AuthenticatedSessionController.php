<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\AdminInvitations\HandleInvitationAfterAuthAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

final class AuthenticatedSessionController extends Controller
{
    public function __construct(
        private HandleInvitationAfterAuthAction $handleInvitationAfterAuthAction
    ) {}

    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
            'inviteToken' => $request->cookie('invite_token'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Get the invite token from cookie before regenerating the session
        $inviteToken = $request->cookie('invite_token');

        $request->session()->regenerate();

        return $this->handleInvitationAfterAuthAction->handle(Auth::user(), $inviteToken);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

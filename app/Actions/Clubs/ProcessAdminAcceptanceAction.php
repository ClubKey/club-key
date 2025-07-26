<?php

declare(strict_types=1);

namespace App\Actions\Clubs;

use App\Models\AdminInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Throwable;

use function __;
use function redirect;
use function to_route;

final readonly class ProcessAdminAcceptanceAction
{
    public function __construct(
        private AcceptInvitationAction $acceptInvitationAction
    ) {}

    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(AdminInvitation $adminInvitation): RedirectResponse
    {

        if (Auth::guest()) {
            return to_route('register')
                ->withCookie('invite_token', $adminInvitation->token, 60 * 24 * 7); // 7 days
        }

        // if the user is already logged in and the invitation is valid, redirect to the admin dashboard
        if ($adminInvitation->isValidForUser(Auth::user())) {
            $this->acceptInvitationAction->handle($adminInvitation, Auth::user());

            return redirect()->route('dashboard')
                ->with('success', __('You have been added as an admin of :club', ['club' => $adminInvitation->club->name]))
                ->withCookie('invite_token', '', -1); // Clear the cookie
        }

        if ($adminInvitation->expires_at->isPast()) {
            return redirect()->route('dashboard')
                ->with('error', __('This invitation has expired. Please contact the club admin for a new invitation.'))
                ->withCookie('invite_token', '', -1); // Clear the cookie
        }

        return to_route('dashboard')
            ->with('error', __("You're already an admin of this club or the invitation is invalid. Please contact the club admin for assistance."))
            ->withCookie('invite_token', '', -1); // Clear the cookie
    }
}

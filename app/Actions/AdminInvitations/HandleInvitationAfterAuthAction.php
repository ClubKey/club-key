<?php

declare(strict_types=1);

namespace App\Actions\AdminInvitations;

use App\Models\AdminInvitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

use function __;
use function redirect;

final readonly class HandleInvitationAfterAuthAction
{
    public function __construct(
        private AcceptInvitationAction $acceptInvitationAction
    ) {}

    /**
     * Handle invitation acceptance after user authentication.
     *
     * @throws Throwable
     */
    public function handle(User $user, ?string $inviteToken): RedirectResponse
    {
        if (! $inviteToken) {
            return redirect()->route('dashboard');
        }

        $adminInvitation = AdminInvitation::byToken($inviteToken)->first();

        if (! $adminInvitation) {
            return redirect()->route('dashboard')
                ->with('error', __('Invalid invitation token. Please contact the club admin for a new invitation.'))
                ->withCookie('invite_token', '', -1); // Clear the cookie
        }

        if ($adminInvitation->expires_at->isPast()) {
            return redirect()->route('dashboard')
                ->with('error', __('This invitation has expired. Please contact the club admin for a new invitation.'))
                ->withCookie('invite_token', '', -1); // Clear the cookie
        }

        if (! $adminInvitation->isValidForUser($user)) {
            return redirect()->route('dashboard')
                ->with('error', __("You're already an admin of this club or the invitation is invalid. Please contact the club admin for assistance."))
                ->withCookie('invite_token', '', -1); // Clear the cookie
        }

        DB::transaction(function () use ($adminInvitation, $user): void {
            $this->acceptInvitationAction->handle($adminInvitation, $user);
        });

        return redirect()->route('dashboard')
            ->with('success', __('You have been added as an admin of :club', ['club' => $adminInvitation->club->name]))
            ->withCookie('invite_token', '', -1); // Clear the cookie
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Clubs;

use App\Actions\ClubAdmins\AcceptInvitationAction;
use App\Actions\ClubAdmins\CreateAdminInvitationAction;
use App\Actions\ClubAdmins\ResendAdminInvitationAction;
use App\Actions\ClubAdmins\RevokeAdminInvitationAction;
use App\DTOs\AdminInvitatiionDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClubAdmins\AdminInvitationRequest;
use App\Models\AdminInvitation;
use App\Models\Club;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Throwable;

use function __;
use function to_route;

final class AdminInvitationController extends Controller
{
    /**
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function store(
        AdminInvitationRequest $request,
        Club $club,
        CreateAdminInvitationAction $adminInvitationAction
    ): RedirectResponse {
        Gate::authorize('inviteAdmins', $club);

        $adminInvitationAction->handle(AdminInvitatiionDto::fromArray($request->validated()), $club);

        return back()
            ->with('success', __('Admin invitation created successfully. An email has been sent to the invited user.'));
    }

    public function accept(
        Club $club,
        AdminInvitation $adminInvitation,
        AcceptInvitationAction $acceptInvitationAction,
    ): RedirectResponse {
        if ($adminInvitation->isValidForUser(Auth::user())) {
            $acceptInvitationAction->handle($adminInvitation, Auth::user());

            return to_route('dashboard')
                ->with('success', __('You have been added as an admin of :club', ['club' => $adminInvitation->club->name]));
        }

        if ($adminInvitation->expires_at->isPast()) {
            return to_route('dashboard')
                ->with('error', __('This invitation has expired. Please contact the club admin for a new invitation.'));
        }

        return to_route('dashboard')
            ->with('error', __("You're already an admin of this club or the invitation is invalid. Please contact the club admin for assistance."));
    }

    public function resend(Club $club, AdminInvitation $adminInvitation, ResendAdminInvitationAction $resendAdminInvitationAction): RedirectResponse
    {
        Gate::authorize('update', $club);

        $resendAdminInvitationAction->handle($adminInvitation);

        return back()->with('success', __('Admin invitation resent successfully. An email has been sent to the invited user.'));
    }

    /**
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function destroy(Club $club, AdminInvitation $adminInvitation, RevokeAdminInvitationAction $revokeAdminInvitationAction): RedirectResponse
    {
        Gate::authorize('update', $club);

        $revokeAdminInvitationAction->handle($adminInvitation);

        return back()->with('success', __('Admin invitation revoked successfully.'));
    }
}

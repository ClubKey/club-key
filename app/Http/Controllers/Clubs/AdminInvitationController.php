<?php

declare(strict_types=1);

namespace App\Http\Controllers\Clubs;

use App\Actions\Clubs\CreateAdminInvitationAction;
use App\Actions\Clubs\ProcessAdminAcceptanceAction;
use App\DTOs\AdminInvitatiionDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Clubs\AdminInvitationRequest;
use App\Models\AdminInvitation;
use App\Models\Club;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Throwable;

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
        Gate::authorize('update', $club);

        $adminInvitationAction->handle(AdminInvitatiionDto::fromArray($request->validated()), $club);

        return back()
            ->with('success', __('Admin invitation created successfully. An email has been sent to the invited user.'));
    }

    public function accept(
        Club $club,
        AdminInvitation $adminInvitation,
        ProcessAdminAcceptanceAction $processAdminAcceptanceAction,
    ): RedirectResponse {
        return $processAdminAcceptanceAction->handle($adminInvitation);
    }
}

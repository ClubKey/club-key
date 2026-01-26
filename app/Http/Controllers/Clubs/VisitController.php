<?php

declare(strict_types=1);

namespace App\Http\Controllers\Clubs;

use App\Actions\Clubs\VisitClubAction;
use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubLocation;
use App\Models\ClubUser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Throwable;

final class VisitController extends Controller
{
    /**
     * @throws Throwable
     */
    public function store(
        Request $request,
        Club $club,
        ?ClubLocation $clubLocation,
        VisitClubAction $visitClubAction
    ): RedirectResponse {
        $user = $request->user();

        $visitClubAction->handle($user, $club, $clubLocation);

        return redirect(route('clubs.show', ['club' => $club]))
            ->with('success', __('Sweet! Thanks for visiting us at :club.', ['club' => $club->name]));
    }

    /**
     * Create a visit for a member by admin/barista.
     *
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function createForMember(
        Request $request,
        ClubUser $member,
        VisitClubAction $visitClubAction
    ): RedirectResponse {
        Gate::authorize('view', $member);

        $visitClubAction->handle($member->user, $member->club);

        return back()->with('success', __('Visit logged for :name.', ['name' => $member->user->name]));
    }
}

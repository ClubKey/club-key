<?php

declare(strict_types=1);

namespace App\Http\Controllers\Clubs;

use App\Actions\Clubs\VisitClubAction;
use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
}

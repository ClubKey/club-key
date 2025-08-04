<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Clubs\SwitchCurrentClubAction;
use App\Http\Controllers\Controller;
use App\Models\Club;
use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

final class SwitchClubController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws AuthorizationException
     */
    public function __invoke(
        Request $request,
        Club $club,
        SwitchCurrentClubAction $switchCurrentClubAction
    ): RedirectResponse {
        Gate::authorize('switch', $club);

        $switchCurrentClubAction->handle(Auth::user(), $club);

        return to_route('dashboard')
            ->with('success', __('You have switched to the club :club', ['club' => $club->name]));
    }
}

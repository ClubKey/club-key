<?php

declare(strict_types=1);

namespace App\Http\Controllers\Clubs;

use App\Actions\Clubs\JoinClubAction;
use App\Http\Controllers\Controller;
use App\Models\Club;
use Illuminate\Http\Request;
use Throwable;

final class JoinClubController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Throwable
     */
    public function __invoke(Request $request, Club $club, JoinClubAction $joinClubAction)
    {
        $user = $request->user();

        $wasNewMember = $joinClubAction->handle($user, $club);

        if ($wasNewMember) {
            session()->flash('success', "You joined $club->name successfully! Congrats, and look forward to seeing rewards soon.");
        }

        return to_route('clubs.show', ['club' => $club]);
    }
}

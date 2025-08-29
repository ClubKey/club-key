<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ClubUser;
use Illuminate\Http\Request;
use Inertia\Inertia;

final class DashboardController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $clubUser = ClubUser::where('user_id', $user->id)->where('club_id', $user->currentClub?->id)
            ->with('rewardLevel.rewards')
            ->first();

        return Inertia::render('Dashboard', [
            'clubUser' => $clubUser,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ClubUser;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\DashboardService;

final class DashboardController extends Controller
{
    public function show(Request $request, DashboardService $dashboardService)
    {
        $user = $request->user();
        $club = $user->currentClub;
        $clubUser = ClubUser::where('user_id', $user->id)->where('club_id', $club?->id)
            ->with('rewardLevel.rewards')
            ->first();

        $adminOverview = null;
        if ($clubUser && $clubUser->role && $clubUser->role->isAdmin()) {
            $dto = $dashboardService->getAdminOverview($club->id);
            $adminOverview = $dto?->toArray();
        }

        return Inertia::render('Dashboard', [
            'clubUser' => $clubUser,
            'adminOverview' => $adminOverview,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ClubUser;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
        $userOverview = null;
        $promotion = null;

        if ($clubUser && $clubUser->role && $clubUser->role->isAdmin()) {
            $dto = $dashboardService->getAdminOverview($club->id);
            $adminOverview = $dto?->toArray();
        } elseif ($clubUser) {
            $userOverview = $dashboardService->getUserOverview($clubUser)->toArray();
            $promotion = $club?->promotions()->currentlyRunning()->first();
        }

        return Inertia::render('Dashboard', [
            'clubUser' => $clubUser,
            'adminOverview' => $adminOverview,
            'userOverview' => $userOverview,
            'promotion' => $promotion,
        ]);
    }
}

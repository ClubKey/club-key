<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\DashboardDataDto;
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

        $dashboardData = array_map(
            static fn (DashboardDataDto $data) => $data->toArray(),
            $dashboardService->generateDashboardData($clubUser)
        );

        return Inertia::render('Dashboard', [
            'clubUser' => $clubUser,
            'dashboardData' => $dashboardData,
        ]);
    }
}

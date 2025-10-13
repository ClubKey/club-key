<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\DashboardAdminOverviewDto;
use App\DTOs\DashboardUserOverviewDto;
use App\Models\ClubUser;
use App\Models\Visit;
use Illuminate\Support\Facades\DB;

final class DashboardService
{
    public function getAdminOverview(int $clubId): ?DashboardAdminOverviewDto
    {
        // Number of members
        $numberOfMembers = ClubUser::where('club_id', $clubId)->count();

        // Number of members signed up today
        $numberOfMembersToday = ClubUser::where('club_id', $clubId)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        // Number of check-ins this week
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $numberOfVisitsThisWeek = Visit::query()
            ->where('club_id', $clubId)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->count();

        // Most popular location
        $mostPopularLocation = Visit::query()
            ->select('club_location_id', DB::raw('count(*) as total'))
            ->where('club_id', $clubId)
            ->whereNotNull('club_location_id')
            ->groupBy('club_location_id')
            ->orderByDesc('total')
            ->first();

        $mostPopularLocationName = null;
        if ($mostPopularLocation) {
            $location = DB::table('club_locations')->where('id', $mostPopularLocation->club_location_id)->first();
            $mostPopularLocationName = $location?->name;
        }

        return new DashboardAdminOverviewDto(
            $numberOfMembers,
            $numberOfMembersToday,
            $numberOfVisitsThisWeek,
            $mostPopularLocationName
        );
    }

    public function getUserOverview(ClubUser $clubUser): DashboardUserOverviewDto
    {
        $baseVisitQuery = Visit::query()
            ->where('club_id', $clubUser->club_id)
            ->where('user_id', $clubUser->user_id);

        $visitsPastWeek = (clone $baseVisitQuery)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $visitsThisMonth = (clone $baseVisitQuery)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $lifetimeVisits = (clone $baseVisitQuery)->count();

        $lastVisit = (clone $baseVisitQuery)
            ->latest('created_at')
            ->first();

        $lastVisitedAt = $lastVisit?->created_at?->toIso8601String();

        $visitsPastThirtyDays = (clone $baseVisitQuery)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $nextRewardLevel = $clubUser->nextRewardLevel;
        $visitsUntilNextReward = null;
        $nextRewardLevelName = null;

        if ($nextRewardLevel) {
            $nextRewardLevelName = $nextRewardLevel->name;
            $visitsUntilNextReward = max(
                $nextRewardLevel->min_visits_per_month - $visitsPastThirtyDays,
                0
            );
        }

        return new DashboardUserOverviewDto(
            visitsPastWeek: $visitsPastWeek,
            visitsThisMonth: $visitsThisMonth,
            lifetimeVisits: $lifetimeVisits,
            lastVisitedAt: $lastVisitedAt,
            visitsUntilNextReward: $visitsUntilNextReward,
            nextRewardLevelName: $nextRewardLevelName
        );
    }
}

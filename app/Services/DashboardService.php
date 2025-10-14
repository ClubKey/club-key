<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\DashboardAdminOverviewDto;
use App\DTOs\DashboardUserOverviewDto;
use App\Models\ClubLocation;
use App\Models\ClubUser;
use App\Models\Visit;

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
        $mostPopularLocation = ClubLocation::query()
            ->whereHas('visits', function ($query) use ($clubId) {
                $query->where('club_id', $clubId);
            })
            ->withCount('visits')
            ->orderByDesc('visits_count')
            ->first();

        $mostPopularLocationName = $mostPopularLocation?->name;

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

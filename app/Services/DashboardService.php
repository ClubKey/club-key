<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\DashboardDataDto;
use App\Models\ClubLocation;
use App\Models\ClubUser;
use App\Models\Visit;
use Illuminate\Support\Str;

final class DashboardService
{
    /**
     * @return DashboardDataDto[]
     */
    public function generateDashboardData(?ClubUser $clubUser): array
    {
        if (! $clubUser) {
            return [];
        }

        if ($clubUser->role->isAdmin()) {
            return $this->generateAdminDashboardData($clubUser->club_id);
        }

        return $this->generateMemberDashboardData($clubUser);
    }

    /**
     * @return DashboardDataDto[]
     */
    private function generateAdminDashboardData(int $clubId): array
    {
        $numberOfMembers = ClubUser::where('club_id', $clubId)->count();

        $numberOfMembersToday = ClubUser::where('club_id', $clubId)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $numberOfVisitsThisWeek = Visit::query()
            ->where('club_id', $clubId)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->count();

        $mostPopularLocation = ClubLocation::query()
            ->whereHas('visits', function ($query) use ($clubId) {
                $query->where('club_id', $clubId);
            })
            ->withCount('visits')
            ->orderByDesc('visits_count')
            ->first();

        $dashboardData = [
            new DashboardDataDto(
                value: $numberOfMembersToday,
                heading: sprintf(
                    'New %s today',
                    Str::plural('member', $numberOfMembersToday)
                ),
            ),
            new DashboardDataDto(
                value: $numberOfMembers,
                heading: sprintf(
                    'Total %s',
                    Str::plural('member', $numberOfMembers)
                ),
            ),
            new DashboardDataDto(
                value: $numberOfVisitsThisWeek,
                heading: sprintf(
                    '%s this week',
                    ucfirst(Str::plural('visit', $numberOfVisitsThisWeek))
                ),
            ),
        ];

        if ($mostPopularLocation) {
            $dashboardData[] = new DashboardDataDto(
                value: $mostPopularLocation->name,
                heading: 'Most popular location',
            );
        }

        return $dashboardData;
    }

    /**
     * @return DashboardDataDto[]
     */
    private function generateMemberDashboardData(ClubUser $clubUser): array
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

        $dashboardData = [
            new DashboardDataDto(
                value: $clubUser->rewardLevel?->name ?? 'Member',
                heading: 'Your reward tier',
            ),
            new DashboardDataDto(
                value: $visitsPastWeek,
                heading: sprintf(
                    '%s in the past 7 days',
                    ucfirst(Str::plural('visit', $visitsPastWeek))
                ),
            ),
            new DashboardDataDto(
                value: $visitsThisMonth,
                heading: sprintf(
                    '%s this month',
                    ucfirst(Str::plural('visit', $visitsThisMonth))
                ),
            ),
            new DashboardDataDto(
                value: $lifetimeVisits,
                heading: sprintf(
                    'Total %s',
                    ucfirst(Str::plural('visit', $lifetimeVisits))
                ),
            ),
        ];

        if ($visitsUntilNextReward !== null) {
            $dashboardData[] = new DashboardDataDto(
                value: $visitsUntilNextReward,
                heading: sprintf(
                    '%s until next reward',
                    ucfirst(Str::plural('visit', $visitsUntilNextReward))
                ),
                subheading: $nextRewardLevelName ? "Next up: {$nextRewardLevelName}" : null,
            );
        } else {
            $dashboardData[] = new DashboardDataDto(
                value: '🎉',
                heading: "You're at the top tier!",
            );
        }

        return $dashboardData;
    }
}

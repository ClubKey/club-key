<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\DashboardAdminOverviewDto;
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
}

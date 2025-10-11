<?php
declare(strict_types=1);

namespace App\DTOs;

final class DashboardAdminOverviewDto
{
    public function __construct(
        public int     $numberOfMembers,
        public int     $numberOfMembersToday,
        public int     $numberOfVisitsThisWeek,
        public ?string $mostPopularLocation,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'numberOfMembers' => $this->numberOfMembers,
            'numberOfMembersToday' => $this->numberOfMembersToday,
            'numberOfVisitsThisWeek' => $this->numberOfVisitsThisWeek,
            'mostPopularLocation' => $this->mostPopularLocation,
        ];
    }
}

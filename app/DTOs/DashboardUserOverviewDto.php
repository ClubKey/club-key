<?php

declare(strict_types=1);

namespace App\DTOs;

final class DashboardUserOverviewDto
{
    public function __construct(
        public int $visitsPastWeek,
        public int $visitsThisMonth,
        public int $lifetimeVisits,
        public ?string $lastVisitedAt,
        public ?int $visitsUntilNextReward,
        public ?string $nextRewardLevelName,
    ) {
    }

    public function toArray(): array
    {
        return [
            'visitsPastWeek' => $this->visitsPastWeek,
            'visitsThisMonth' => $this->visitsThisMonth,
            'lifetimeVisits' => $this->lifetimeVisits,
            'lastVisitedAt' => $this->lastVisitedAt,
            'visitsUntilNextReward' => $this->visitsUntilNextReward,
            'nextRewardLevelName' => $this->nextRewardLevelName,
        ];
    }
}

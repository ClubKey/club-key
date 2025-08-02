<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Contracts\DTO;

final readonly class RewardLevelDto implements DTO
{
    public function __construct(
        public string $name,
        public ?string $iconName = null,
        public int $minVisitsPerMonth = 0,
        public ?string $color = null,
        public ?string $description = null,
        public int $percentageDiscount = 0,
        public int $order = 0,
        public int $clubId = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            iconName: $data['icon_name'] ?? null,
            minVisitsPerMonth: (int) ($data['min_visits_per_month'] ?? 0),
            color: $data['color'] ?? null,
            description: $data['description'] ?? null,
            percentageDiscount: (int) ($data['percentage_discount'] ?? 0),
            order: (int) ($data['order'] ?? 0),
            clubId: (int) ($data['club_id'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'icon_name' => $this->iconName,
            'min_visits_per_month' => $this->minVisitsPerMonth,
            'color' => $this->color,
            'description' => $this->description,
            'percentage_discount' => $this->percentageDiscount,
            'order' => $this->order,
            'club_id' => $this->clubId,
        ];
    }
}

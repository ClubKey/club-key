<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Contracts\DTO;

final readonly class RewardDto implements DTO
{
    public function __construct(
        public string $description,
        public int $order = 0,
        public int $rewardLevelId = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            description: $data['description'],
            order: (int) ($data['order'] ?? 0),
            rewardLevelId: (int) ($data['reward_level_id'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'order' => $this->order,
            'reward_level_id' => $this->rewardLevelId,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Contracts\DTO;

final readonly class PromotionDto implements DTO
{
    public function __construct(
        public string $name,
        public string $description,
        public bool $publishNow = false,
        public ?string $publishAt = null,
        public ?string $publishedAt = null,
        public ?string $startAt = null,
        public ?string $endAt = null,
        public ?bool $isActive = null,
        public ?string $link = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'],
            publishNow: $data['publish_now'] ?? false,
            publishAt: $data['publish_at'] ?? null,
            publishedAt: $data['published_at'] ?? null,
            startAt: $data['start_at'] ?? null,
            endAt: $data['end_at'] ?? null,
            isActive: $data['is_active'] ?? null,
            link: $data['link'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'publish_now' => $this->publishNow,
            'publish_at' => $this->publishAt,
            'published_at' => $this->publishedAt,
            'start_at' => $this->startAt,
            'end_at' => $this->endAt,
            'is_active' => $this->isActive,
            'link' => $this->link,
        ];
    }
}

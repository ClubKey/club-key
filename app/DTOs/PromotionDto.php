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
        $isActive = null;
        if (array_key_exists('is_active', $data)) {
            $value = $data['is_active'];
            $filtered = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            $isActive = $filtered !== null ? $filtered : (bool) $value;
        }

        $publishNow = $data['publish_now'] ?? false;
        if (! is_bool($publishNow)) {
            $normalizedPublishNow = filter_var($publishNow, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            $publishNow = $normalizedPublishNow !== null ? $normalizedPublishNow : (bool) $publishNow;
        }

        $publishAt = $data['publish_at'] ?? null;
        $publishedAt = $data['published_at'] ?? null;
        $startAt = $data['start_at'] ?? null;
        $endAt = $data['end_at'] ?? null;
        $link = $data['link'] ?? null;

        $normalizeNullableString = static function (mixed $value): ?string {
            if (is_string($value)) {
                return $value === '' ? null : $value;
            }

            if ($value instanceof \Stringable) {
                return (string) $value;
            }

            return null;
        };

        return new self(
            name: $data['name'],
            description: $data['description'],
            publishNow: $publishNow,
            publishAt: $normalizeNullableString($publishAt),
            publishedAt: $normalizeNullableString($publishedAt),
            startAt: $normalizeNullableString($startAt),
            endAt: $normalizeNullableString($endAt),
            isActive: $isActive,
            link: $normalizeNullableString($link),
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

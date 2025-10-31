<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Contracts\DTO;
use Carbon\CarbonImmutable;
use Stringable;
use Throwable;

final readonly class PromotionDto implements DTO
{
    public function __construct(
        public string $name,
        public string $description,
        public bool $publishNow = false,
        public ?CarbonImmutable $publishAt = null,
        public ?CarbonImmutable $publishedAt = null,
        public ?CarbonImmutable $startAt = null,
        public ?CarbonImmutable $endAt = null,
        public ?bool $isActive = null,
        public ?string $link = null,
        public string $timezone = 'UTC',
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

        $defaultTimezone = 'UTC';

        if (function_exists('config')) {
            try {
                $defaultTimezone = (string) config('app.timezone', 'UTC');
            } catch (Throwable) {
                $defaultTimezone = 'UTC';
            }
        }

        $timezone = $data['timezone'] ?? $defaultTimezone;

        $normalizeNullableString = static function (mixed $value): ?string {
            if (is_string($value)) {
                return $value === '' ? null : $value;
            }

            if ($value instanceof Stringable) {
                return (string) $value;
            }

            return null;
        };

        $timezone = $normalizeNullableString($timezone) ?? $defaultTimezone;

        $normalizeDate = static function (?string $value) use ($timezone): ?CarbonImmutable {
            if ($value === null || $value === '') {
                return null;
            }

            return CarbonImmutable::parse($value, $timezone)->setTimezone('UTC');
        };

        return new self(
            name: $data['name'],
            description: $data['description'],
            publishNow: $publishNow,
            publishAt: $normalizeDate($normalizeNullableString($publishAt)),
            publishedAt: $normalizeDate($normalizeNullableString($publishedAt)),
            startAt: $normalizeDate($normalizeNullableString($startAt)),
            endAt: $normalizeDate($normalizeNullableString($endAt)),
            isActive: $isActive,
            link: $normalizeNullableString($link),
            timezone: $timezone,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'publish_now' => $this->publishNow,
            'publish_at' => $this->publishAt?->toIso8601String(),
            'published_at' => $this->publishedAt?->toIso8601String(),
            'start_at' => $this->startAt?->toIso8601String(),
            'end_at' => $this->endAt?->toIso8601String(),
            'is_active' => $this->isActive,
            'link' => $this->link,
            'timezone' => $this->timezone,
        ];
    }
}

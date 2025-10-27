<?php

declare(strict_types=1);

namespace App\Support;

use InvalidArgumentException;

final class TextMarketingPlan
{
    public function __construct(
        public readonly string $identifier,
        public readonly string $name,
        public readonly int $monthlyLimit,
        public readonly int $basePriceCents,
        public readonly int $overageRateCents,
    ) {}

    public static function fromConfig(string $identifier): self
    {
        $plan = config("text-marketing.plans.{$identifier}");

        if (! is_array($plan)) {
            throw new InvalidArgumentException("Unknown text marketing plan [{$identifier}].");
        }

        return new self(
            identifier: $identifier,
            name: $plan['name'],
            monthlyLimit: (int) $plan['monthly_limit'],
            basePriceCents: (int) $plan['base_price_cents'],
            overageRateCents: (int) $plan['overage_rate_cents'],
        );
    }

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier,
            'name' => $this->name,
            'monthly_limit' => $this->monthlyLimit,
            'base_price_cents' => $this->basePriceCents,
            'overage_rate_cents' => $this->overageRateCents,
        ];
    }
}

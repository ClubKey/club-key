<?php

declare(strict_types=1);

namespace App\DTOs;

final class DashboardDataDto
{
    public function __construct(
        public readonly int|string $value,
        public readonly string $heading,
        public readonly ?string $subheading = null,
    ) {}

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'heading' => $this->heading,
            'subheading' => $this->subheading,
        ];
    }
}

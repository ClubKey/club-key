<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class ImportSubscriptionErrorDto
{
    public function __construct(
        public int $row,
        public string $error,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            row: (int) ($data['row'] ?? 0),
            error: (string) ($data['error'] ?? ''),
        );
    }

    /**
     * @return array{row: int, error: string}
     */
    public function toArray(): array
    {
        return [
            'row' => $this->row,
            'error' => $this->error,
        ];
    }
}

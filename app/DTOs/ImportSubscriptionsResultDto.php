<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class ImportSubscriptionsResultDto
{
    /**
     * @param  array<int, ImportSubscriptionErrorDto>  $errors
     */
    public function __construct(
        public int $success,
        public array $errors = [],
    ) {}

    public static function fromArray(array $data): self
    {
        $errors = [];
        foreach ($data['errors'] ?? [] as $error) {
            $errors[] = ImportSubscriptionErrorDto::fromArray($error);
        }

        return new self(
            success: (int) ($data['success'] ?? 0),
            errors: $errors,
        );
    }

    /**
     * @return array{success: int, errors: array<int, array{row: int, error: string}>}
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'errors' => array_map(
                fn (ImportSubscriptionErrorDto $error) => $error->toArray(),
                $this->errors
            ),
        ];
    }
}

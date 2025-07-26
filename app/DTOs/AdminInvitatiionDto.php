<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Contracts\DTO;
use App\Enums\ClubRole;

final readonly class AdminInvitatiionDto implements DTO
{
    public function __construct(
        public string $email,
        public ClubRole $role,
    ) {}

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            role: ClubRole::from($data['role']),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'role' => $this->role->value,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Contracts\DTO;

final readonly class ClubLocationDto implements DTO
{
    public function __construct(
        public int $clubId,
        public string $name,
        public string $streetAddress,
        public string $city,
        public string $state,
        public string $zip,
        public ?string $phone = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            clubId: $data['club_id'],
            name: $data['name'],
            streetAddress: $data['street_address'],
            city: $data['city'],
            state: $data['state'],
            zip: $data['zip'],
            phone: $data['phone'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'club_id' => $this->clubId,
            'name' => $this->name,
            'street_address' => $this->streetAddress,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'phone' => $this->phone,
        ];
    }
}

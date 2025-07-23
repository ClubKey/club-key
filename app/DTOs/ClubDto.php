<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Contracts\DTO;
use Illuminate\Http\UploadedFile;

final readonly class ClubDto implements DTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description = null,
        public UploadedFile|string|null $logo = null,
        public ?string $website = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $streetAddress = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $zip = null,
        public ?string $facebook = null,
        public ?string $instagram = null,
        public ?string $x = null,
        public ?string $youtube = null,
        public ?string $tiktok = null,
        public ?string $linkedin = null,
        public ?string $pinterest = null,
        public ?string $snapchat = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            slug: $data['slug'],
            description: $data['description'] ?? null,
            logo: $data['logo'] ?? null,
            website: $data['website'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            streetAddress: $data['street_address'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            zip: $data['zip'] ?? null,
            facebook: $data['facebook'] ?? null,
            instagram: $data['instagram'] ?? null,
            x: $data['x'] ?? null,
            youtube: $data['youtube'] ?? null,
            tiktok: $data['tiktok'] ?? null,
            linkedin: $data['linkedin'] ?? null,
            pinterest: $data['pinterest'] ?? null,
            snapchat: $data['snapchat'] ?? null
        );
    }

    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'website' => $this->website,
            'email' => $this->email,
            'phone' => $this->phone,
            'street_address' => $this->streetAddress,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'x' => $this->x,
            'youtube' => $this->youtube,
            'tiktok' => $this->tiktok,
            'linkedin' => $this->linkedin,
            'pinterest' => $this->pinterest,
            'snapchat' => $this->snapchat,
        ];

        if ($this->logo) {
            $data['logo_path'] = $this->logo;
        }

        return $data;
    }
}

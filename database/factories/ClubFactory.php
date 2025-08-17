<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ClubRole;
use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Club>
 */
final class ClubFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'description' => $this->faker->paragraph(),
            'slug' => $this->faker->unique()->slug(),
            'website' => $this->faker->url(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'street_address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'zip' => $this->faker->postcode(),
            'facebook' => $this->faker->url(),
            'instagram' => $this->faker->url(),
            'x' => $this->faker->url(),
            'youtube' => $this->faker->url(),
            'tiktok' => $this->faker->url(),
            'linkedin' => $this->faker->url(),
            'pinterest' => $this->faker->url(),
            'snapchat' => $this->faker->url(),
        ];
    }

    public function withUser(User $user, ?ClubRole $role = null): static
    {
        return $this->afterCreating(function (Club $club) use ($user, $role) {
            $club->members()->attach($user->id, [
                'role' => $role ? $role->value : ClubRole::MEMBER->value,
            ]);
            $user->current_club_id = $club->id;
            $user->save();
        });
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ClubRole;
use App\Models\AdminInvitation;
use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<AdminInvitation>
 */
final class AdminInvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'club_id' => Club::factory(),
            'inviter_id' => User::factory(),
            'email' => fake()->unique()->safeEmail(),
            'token' => Str::ulid(),
            'role' => ClubRole::ADMIN,
            'expires_at' => now()->addDays(30),
        ];
    }

    /**
     * Indicate that the invitation is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDays(1),
        ]);
    }

    /**
     * Indicate that the invitation expires soon.
     */
    public function expiringSoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->addHours(1),
        ]);
    }
}

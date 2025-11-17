<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AppRole;
use App\Enums\ClubRole;
use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
final class UserFactory extends Factory
{
    public const DEFAULT_PHONE = '5092344456';

    public const DEFAULT_PASSWORD = 'TestingPassword54!';

    public const UPDATE_PASSWORD = 'TestingPassword55!';

    public const WRONG_PASSWORD = 'TestingPassword567!';

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'phone' => self::DEFAULT_PHONE,
            'password' => self::$password ??= Hash::make(self::DEFAULT_PASSWORD),
            'remember_token' => Str::random(10),
            'current_club_id' => null,
            'role' => AppRole::CLUB_ADMIN,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is a simple user. (Without club admin privileges)
     */
    public function asUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => AppRole::USER,
        ]);
    }

    public function withClub(): static
    {
        return $this->afterCreating(function (User $user) {
            $club = Club::factory()->create([
                'owner_id' => $user->id,
            ]);
            $user->clubs()->attach($club, [
                'role' => ClubRole::ADMIN->value,
            ]);
            $user->current_club_id = $club->id;
            $user->save();
        });
    }
}

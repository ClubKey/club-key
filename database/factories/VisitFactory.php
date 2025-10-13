<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Club;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Visit>
 */
final class VisitFactory extends Factory
{
    protected $model = Visit::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'club_id' => Club::factory(),
            'club_location_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function forClub(Club $club): self
    {
        return $this->state(fn () => [
            'club_id' => $club->id,
        ]);
    }

    public function forUser(User $user): self
    {
        return $this->state(fn () => [
            'user_id' => $user->id,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Club;
use App\Models\SquareConnection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SquareConnection>
 */
final class SquareConnectionFactory extends Factory
{
    protected $model = SquareConnection::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'club_id' => Club::factory(),
            'merchant_id' => $this->faker->uuid(),
            'access_token' => $this->faker->sha256(),
            'refresh_token' => $this->faker->sha256(),
            'access_token_expires_at' => now()->addDays(30),
            'scopes' => config('square.scopes', []),
            'connected_at' => now(),
        ];
    }
}

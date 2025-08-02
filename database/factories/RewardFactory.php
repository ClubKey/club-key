<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Reward;
use App\Models\RewardLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reward>
 */
final class RewardFactory extends Factory
{
    protected $model = Reward::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reward_level_id' => RewardLevel::factory(),
            'description' => fake()->sentence(),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}

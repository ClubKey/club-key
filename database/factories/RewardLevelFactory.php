<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Club;
use App\Models\RewardLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RewardLevel>
 */
final class RewardLevelFactory extends Factory
{
    protected $model = RewardLevel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'club_id' => Club::factory(),
            'name' => fake()->randomElement(['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond']),
            'icon_name' => fake()->optional()->randomElement(['trophy', 'star', 'crown', 'gem', 'medal']),
            'min_visits_per_month' => fake()->numberBetween(1, 30),
            'color' => fake()->optional()->hexColor(),
            'description' => fake()->optional()->sentence(),
            'percentage_discount' => fake()->numberBetween(5, 50),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}

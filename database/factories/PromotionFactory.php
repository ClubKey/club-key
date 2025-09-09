<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Club;
use App\Models\Promotion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

final class PromotionFactory extends Factory
{
    protected $model = Promotion::class;

    public function definition(): array
    {
        return [
            'club_id' => Club::factory(),
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now(),
            'is_active' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}

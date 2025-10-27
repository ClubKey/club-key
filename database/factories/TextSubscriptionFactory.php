<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Club;
use App\Models\TextSubscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<TextSubscription>
 */
final class TextSubscriptionFactory extends Factory
{
    protected $model = TextSubscription::class;

    public function definition(): array
    {
        return [
            'club_id' => Club::factory(),
            'user_id' => User::factory(),
            'phone_number' => $this->faker->e164PhoneNumber(),
            'is_subscribed' => true,
            'opt_in_source' => 'factory',
            'unsubscribed_at' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    public function unsubscribed(): self
    {
        return $this->state(fn () => [
            'is_subscribed' => false,
            'unsubscribed_at' => Carbon::now(),
        ]);
    }
}

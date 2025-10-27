<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Club;
use App\Models\ClubSmsSetting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<ClubSmsSetting>
 */
final class ClubSmsSettingFactory extends Factory
{
    protected $model = ClubSmsSetting::class;

    public function definition(): array
    {
        return [
            'club_id' => Club::factory(),
            'vonage_number' => $this->faker->e164PhoneNumber(),
            'vonage_number_id' => (string) $this->faker->randomNumber(8),
            'current_plan' => 'starter',
            'monthly_limit' => 2000,
            'messages_sent_this_period' => 0,
            'messages_received_this_period' => 0,
            'overage_messages' => 0,
            'base_price_cents' => 5900,
            'overage_rate_cents' => 2,
            'billing_cycle_resets_at' => Carbon::now()->addMonth(),
            'billing_provider_subscription_id' => (string) $this->faker->uuid(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}

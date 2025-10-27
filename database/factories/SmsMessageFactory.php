<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SmsDirection;
use App\Enums\SmsMessageStatus;
use App\Models\Club;
use App\Models\SmsMessage;
use App\Models\TextSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<SmsMessage>
 */
final class SmsMessageFactory extends Factory
{
    protected $model = SmsMessage::class;

    public function definition(): array
    {
        return [
            'club_id' => Club::factory(),
            'text_subscription_id' => TextSubscription::factory(),
            'direction' => SmsDirection::OUTBOUND,
            'status' => SmsMessageStatus::QUEUED,
            'provider_message_id' => null,
            'from_number' => $this->faker->e164PhoneNumber(),
            'to_number' => $this->faker->e164PhoneNumber(),
            'segments' => 1,
            'cost_cents' => null,
            'body' => $this->faker->sentence(),
            'metadata' => [],
            'queued_at' => Carbon::now(),
            'sent_at' => null,
            'delivered_at' => null,
            'failed_at' => null,
            'failure_reason' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    public function inbound(): self
    {
        return $this->state(fn () => [
            'direction' => SmsDirection::INBOUND,
            'status' => SmsMessageStatus::RECEIVED,
            'queued_at' => null,
        ]);
    }
}

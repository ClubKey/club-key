<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $club_id
 * @property string $vonage_number
 * @property string|null $vonage_number_id
 * @property string $current_plan
 * @property int $monthly_limit
 * @property int $messages_sent_this_period
 * @property int $messages_received_this_period
 * @property int $overage_messages
 * @property int $base_price_cents
 * @property int $overage_rate_cents
 * @property CarbonImmutable|null $billing_cycle_resets_at
 * @property string|null $billing_provider_subscription_id
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property-read Club $club
 */
final class ClubSmsSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id',
        'vonage_number',
        'vonage_number_id',
        'current_plan',
        'monthly_limit',
        'messages_sent_this_period',
        'messages_received_this_period',
        'overage_messages',
        'base_price_cents',
        'overage_rate_cents',
        'billing_cycle_resets_at',
        'billing_provider_subscription_id',
    ];

    protected $casts = [
        'monthly_limit' => 'integer',
        'messages_sent_this_period' => 'integer',
        'messages_received_this_period' => 'integer',
        'overage_messages' => 'integer',
        'base_price_cents' => 'integer',
        'overage_rate_cents' => 'integer',
        'billing_cycle_resets_at' => 'immutable_datetime',
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SmsMessage::class, 'club_id', 'club_id');
    }

    public function remainingMessages(): int
    {
        return max($this->monthly_limit - $this->messages_sent_this_period, 0);
    }

    public function incrementSentMessages(int $segments = 1): void
    {
        $newSentCount = $this->messages_sent_this_period + $segments;
        $overage = max($newSentCount - $this->monthly_limit, 0);

        $this->forceFill([
            'messages_sent_this_period' => $newSentCount,
            'overage_messages' => $overage,
        ])->save();
    }

    public function incrementReceivedMessages(int $segments = 1): void
    {
        $this->forceFill([
            'messages_received_this_period' => $this->messages_received_this_period + $segments,
        ])->save();
    }
}

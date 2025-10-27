<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SmsDirection;
use App\Enums\SmsMessageStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $club_id
 * @property int|null $text_subscription_id
 * @property SmsDirection $direction
 * @property SmsMessageStatus $status
 * @property string|null $provider_message_id
 * @property string|null $from_number
 * @property string|null $to_number
 * @property int $segments
 * @property int|null $cost_cents
 * @property string $body
 * @property array|null $metadata
 * @property CarbonImmutable|null $queued_at
 * @property CarbonImmutable|null $sent_at
 * @property CarbonImmutable|null $delivered_at
 * @property CarbonImmutable|null $failed_at
 * @property string|null $failure_reason
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property-read Club $club
 * @property-read TextSubscription|null $subscription
 */
final class SmsMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id',
        'text_subscription_id',
        'direction',
        'status',
        'provider_message_id',
        'from_number',
        'to_number',
        'segments',
        'cost_cents',
        'body',
        'metadata',
        'queued_at',
        'sent_at',
        'delivered_at',
        'failed_at',
        'failure_reason',
    ];

    protected $casts = [
        'direction' => SmsDirection::class,
        'status' => SmsMessageStatus::class,
        'segments' => 'integer',
        'cost_cents' => 'integer',
        'metadata' => 'array',
        'queued_at' => 'immutable_datetime',
        'sent_at' => 'immutable_datetime',
        'delivered_at' => 'immutable_datetime',
        'failed_at' => 'immutable_datetime',
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
    ];

    protected $attributes = [
        'segments' => 1,
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(TextSubscription::class, 'text_subscription_id');
    }

    public function scopeOutbound(Builder $query): void
    {
        $query->where('direction', SmsDirection::OUTBOUND);
    }

    public function scopeInbound(Builder $query): void
    {
        $query->where('direction', SmsDirection::INBOUND);
    }

    public function markFailed(string $reason): void
    {
        $this->forceFill([
            'status' => SmsMessageStatus::FAILED,
            'failed_at' => CarbonImmutable::now(),
            'failure_reason' => $reason,
        ])->save();
    }

    public function markDelivered(?CarbonImmutable $deliveredAt = null): void
    {
        $this->forceFill([
            'status' => SmsMessageStatus::DELIVERED,
            'delivered_at' => $deliveredAt ?? CarbonImmutable::now(),
        ])->save();
    }

    public function markSent(string $providerMessageId, ?CarbonImmutable $sentAt = null): void
    {
        $this->forceFill([
            'status' => SmsMessageStatus::SENT,
            'provider_message_id' => $providerMessageId,
            'sent_at' => $sentAt ?? CarbonImmutable::now(),
        ])->save();
    }
}

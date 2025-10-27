<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SmsDirection;
use App\Enums\SmsMessageStatus;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $club_id
 * @property int|null $user_id
 * @property string $phone_number
 * @property bool $is_subscribed
 * @property string|null $opt_in_source
 * @property CarbonImmutable|null $unsubscribed_at
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property-read Club $club
 * @property-read User|null $user
 * @property-read Collection<int, SmsMessage> $messages
 *
 * @method static Builder<static>|TextSubscription newModelQuery()
 * @method static Builder<static>|TextSubscription newQuery()
 * @method static Builder<static>|TextSubscription query()
 * @method static Builder<static>|TextSubscription subscribed()
 */
final class TextSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id',
        'user_id',
        'phone_number',
        'is_subscribed',
        'unsubscribed_at',
        'opt_in_source',
    ];

    protected $casts = [
        'is_subscribed' => 'boolean',
        'unsubscribed_at' => 'immutable_datetime',
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SmsMessage::class);
    }

    public function scopeSubscribed(Builder $query): void
    {
        $query->where('is_subscribed', true);
    }

    public function markUnsubscribed(?CarbonInterface $timestamp = null): void
    {
        $this->forceFill([
            'is_subscribed' => false,
            'unsubscribed_at' => $timestamp?->toImmutable() ?? CarbonImmutable::now(),
        ])->save();

        $this->messages()
            ->where('direction', SmsDirection::OUTBOUND->value)
            ->where('status', SmsMessageStatus::QUEUED->value)
            ->update([
                'status' => SmsMessageStatus::FAILED->value,
                'failed_at' => CarbonImmutable::now(),
                'failure_reason' => 'recipient unsubscribed',
            ]);
    }

    public function markSubscribed(?string $optInSource = null): void
    {
        $this->forceFill([
            'is_subscribed' => true,
            'opt_in_source' => $optInSource ?? $this->opt_in_source,
            'unsubscribed_at' => null,
        ])->save();
    }
}

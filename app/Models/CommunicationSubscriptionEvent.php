<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SubscriptionEventType;
use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $subscription_id
 * @property SubscriptionEventType $event_type
 * @property CarbonImmutable $occurred_at
 * @property string|null $actor_type
 * @property int|null $actor_id
 * @property string|null $source
 * @property array|null $provider_payload
 * @property string|null $note
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property-read CommunicationSubscription $subscription
 *
 * @method static Builder<static>|CommunicationSubscriptionEvent newModelQuery()
 * @method static Builder<static>|CommunicationSubscriptionEvent newQuery()
 * @method static Builder<static>|CommunicationSubscriptionEvent query()
 *
 * @mixin Eloquent
 */
final class CommunicationSubscriptionEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'event_type',
        'occurred_at',
        'actor_type',
        'actor_id',
        'source',
        'provider_payload',
        'note',
    ];

    protected $casts = [
        'event_type' => SubscriptionEventType::class,
        'occurred_at' => 'immutable_datetime',
        'provider_payload' => 'array',
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(CommunicationSubscription::class, 'subscription_id');
    }
}

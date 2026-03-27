<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ContactType;
use App\Enums\SubscriptionStatus;
use App\Enums\Topic;
use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int|null $user_id
 * @property int $club_id
 * @property ContactType $contact_type
 * @property string $contact_value
 * @property Topic $topic
 * @property SubscriptionStatus $status
 * @property CarbonImmutable|null $subscribed_at
 * @property CarbonImmutable|null $unsubscribed_at
 * @property string|null $unsubscribe_reason
 * @property string|null $source
 * @property string|null $source_message_id
 * @property string|null $provider_message_id
 * @property array|null $consent_proof
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property-read User|null $user
 * @property-read Club $club
 * @property-read HasMany<CommunicationSubscriptionEvent> $events
 * @property-read int|null $events_count
 *
 * @method static Builder<static>|CommunicationSubscription newModelQuery()
 * @method static Builder<static>|CommunicationSubscription newQuery()
 * @method static Builder<static>|CommunicationSubscription query()
 * @method static Builder<static>|CommunicationSubscription subscribed()
 * @method static Builder<static>|CommunicationSubscription unsubscribed()
 * @method static Builder<static>|CommunicationSubscription forContact(ContactType $contactType, string $contactValue)
 * @method static Builder<static>|CommunicationSubscription forTopic(Topic $topic)
 *
 * @mixin Eloquent
 */
final class CommunicationSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'club_id',
        'contact_type',
        'contact_value',
        'topic',
        'status',
        'subscribed_at',
        'unsubscribed_at',
        'unsubscribe_reason',
        'source',
        'source_message_id',
        'provider_message_id',
        'consent_proof',
    ];

    protected $casts = [
        'contact_type' => ContactType::class,
        'topic' => Topic::class,
        'status' => SubscriptionStatus::class,
        'subscribed_at' => 'immutable_datetime',
        'unsubscribed_at' => 'immutable_datetime',
        'consent_proof' => 'array',
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
    ];

    public static function normalizeContactValue(ContactType $contactType, string $contactValue): string
    {
        return match ($contactType) {
            ContactType::SMS => format_phone_number($contactValue),
            ContactType::EMAIL => mb_strtolower(mb_trim($contactValue)),
            ContactType::PUSH, ContactType::WHATSAPP => mb_trim($contactValue),
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(CommunicationSubscriptionEvent::class, 'subscription_id');
    }

    public function userName(): Attribute
    {
        return new Attribute(
            get: fn () => $this->user?->name ?? null,
        );
    }

    public function scopeSubscribed(Builder $query): Builder
    {
        return $query->where('status', SubscriptionStatus::SUBSCRIBED);
    }

    public function scopeUnsubscribed(Builder $query): Builder
    {
        return $query->where('status', SubscriptionStatus::UNSUBSCRIBED);
    }

    public function scopeForContact(Builder $query, ContactType $contactType, string $contactValue): Builder
    {
        return $query->where('contact_type', $contactType)
            ->where('contact_value', self::normalizeContactValue($contactType, $contactValue));
    }

    public function scopeForTopic(Builder $query, Topic $topic): Builder
    {
        return $query->where('topic', $topic);
    }
}

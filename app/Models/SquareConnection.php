<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $club_id
 * @property string $merchant_id
 * @property string $access_token
 * @property string $refresh_token
 * @property CarbonImmutable|null $access_token_expires_at
 * @property list<string>|null $scopes
 * @property CarbonImmutable $connected_at
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property-read Club $club
 *
 * @method static Builder<static>|SquareConnection newModelQuery()
 * @method static Builder<static>|SquareConnection newQuery()
 * @method static Builder<static>|SquareConnection query()
 *
 * @mixin Eloquent
 */
final class SquareConnection extends Model
{
    /** @use HasFactory<\Database\Factories\SquareConnectionFactory> */
    use HasFactory;

    protected $fillable = [
        'club_id',
        'merchant_id',
        'access_token',
        'refresh_token',
        'access_token_expires_at',
        'scopes',
        'connected_at',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function isAccessTokenExpired(): bool
    {
        if ($this->access_token_expires_at === null) {
            return false;
        }

        return $this->access_token_expires_at->isPast();
    }

    public function needsRefresh(?int $withinDays = null): bool
    {
        $withinDays ??= (int) config('square.refresh_within_days', 7);

        if ($this->access_token_expires_at === null) {
            return true;
        }

        return $this->access_token_expires_at->lte(now()->addDays($withinDays));
    }

    public function scopeNeedsRefresh(Builder $query, ?int $withinDays = null): Builder
    {
        $withinDays ??= (int) config('square.refresh_within_days', 7);
        $threshold = now()->addDays($withinDays);

        return $query->where(function (Builder $query) use ($threshold): void {
            $query->whereNull('access_token_expires_at')
                ->orWhere('access_token_expires_at', '<=', $threshold);
        });
    }

    protected function casts(): array
    {
        return [
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
            'access_token_expires_at' => 'immutable_datetime',
            'connected_at' => 'immutable_datetime',
            'scopes' => 'array',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }
}

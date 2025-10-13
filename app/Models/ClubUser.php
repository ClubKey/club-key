<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ClubRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property int $user_id
 * @property int $club_id
 * @property ClubRole $role
 * @property int $reward_level_id
 * @property int $visits_past_30_days
 * @property int $next_reward_level_id
 * @property-read Club $club
 * @property-read User $user
 * @property-read RewardLevel $rewardLevel
 * @property-read RewardLevel $nextRewardLevel
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Visit> $visits
 */
final class ClubUser extends Pivot
{
    protected $with = ['user', 'club', 'rewardLevel', 'nextRewardLevel'];

    protected $fillable = [
        'user_id',
        'club_id',
        'role',
        'reward_level_id',
        'next_reward_level_id',
    ];

    protected $casts = [
        'role' => ClubRole::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function rewardLevel(): BelongsTo
    {
        return $this->belongsTo(RewardLevel::class);
    }

    public function nextRewardLevel(): BelongsTo
    {
        return $this->belongsTo(RewardLevel::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'user_id', 'user_id')
            ->whereColumn('club_id', $this->qualifyColumn('club_id'));
    }

    public function scopeForClub(Builder $query, Club $club): Builder
    {
        return $query->where('club_id', $club->id);
    }

    public function scopeWithMembershipDetails(Builder $query): Builder
    {
        return $query->with(['user', 'club', 'rewardLevel', 'nextRewardLevel']);
    }

    public function scopeWithVisitMetrics(Builder $query): Builder
    {
        return $query->withCount([
            'visits as total_visits',
            'visits as visits_last_week' => static fn (Builder $builder) => $builder
                ->where('created_at', '>=', Carbon::now()->subDays(7)),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ClubRole;
use App\Models\Scopes\OrderDescending;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

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
 */
#[ScopedBy(OrderDescending::class)]
final class ClubUser extends Pivot
{
    protected $with = ['user', 'club', 'rewardLevel', 'nextRewardLevel'];

    protected $fillable = [
        'user_id',
        'club_id',
        'role',
        'reward_level_id',
        'next_reward_level_id',
        'visits_past_30_days',
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

    #[Scope]
    public function search(Builder $query, ?string $search = null): void
    {
        if (! $search) {
            return;
        }

        $query->whereHas('user', function (Builder $query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    #[Scope]
    public function byHighestRewardLevel(Builder $query): void
    {
        $query->orderByDesc(
            RewardLevel::select('min_visits_per_month')
                ->whereColumn('reward_levels.id', 'reward_level_id')
                ->limit(1)
        );
    }

    #[Scope]
    public function byMostRecentVisits(Builder $query): void
    {
        $query->addSelect([
            'last_visit_at' => function ($query) {
                $query->selectRaw('MAX(created_at)')
                    ->from('visits')
                    ->whereColumn('visits.user_id', 'club_user.user_id')
                    ->whereColumn('visits.club_id', 'club_user.club_id');
            },
        ])
            ->orderByDesc('last_visit_at');
    }

    #[Scope]
    public function byMostVisits(Builder $query): void
    {
        $query->orderByDesc('visits_past_30_days');
    }
}

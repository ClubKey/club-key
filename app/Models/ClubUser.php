<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ClubRole;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $user_id
 * @property int $club_id
 * @property ClubRole $role
 * @property int $reward_level_id
 * @property-read Club $club
 * @property-read User $user
 * @property-read RewardLevel $rewardLevel
 */
final class ClubUser extends Pivot
{
    protected $with = ['user', 'club'];

    protected $fillable = [
        'user_id',
        'club_id',
        'role',
        'reward_level_id',
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
}

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
 * @property-read Club $club
 * @property-read User $user
 */
final class ClubUser extends Pivot
{
    protected $fillable = [
        'user_id',
        'club_id',
        'role',
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
}

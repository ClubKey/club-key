<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ClubRole;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $club_id
 * @property string $email
 * @property string $token
 * @property ClubRole $role
 * @property \Carbon\CarbonImmutable $expires_at
 * @property \Carbon\CarbonImmutable $created_at
 * @property \Carbon\CarbonImmutable $updated_at
 * @property-read Club $club
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminInvitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminInvitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminInvitation query()
 *
 * @mixin \Eloquent
 */
final class AdminInvitation extends Model
{
    protected $guarded = [];

    protected $fillable = [
        'club_id',
        'email',
        'token',
        'role',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'immutable_datetime',
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
        'role' => ClubRole::class,
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}

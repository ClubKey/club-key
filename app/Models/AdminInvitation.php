<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ClubRole;
use Carbon\CarbonImmutable;
use Database\Factories\AdminInvitationFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $club_id
 * @property int|null $inviter_id
 * @property string $email
 * @property string $token
 * @property ClubRole $role
 * @property CarbonImmutable $expires_at
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property-read Club $club
 * @property-read User|null $inviter
 *
 * @method static Builder<static>|AdminInvitation newModelQuery()
 * @method static Builder<static>|AdminInvitation newQuery()
 * @method static Builder<static>|AdminInvitation query()
 *
 * @mixin Eloquent
 */
final class AdminInvitation extends Model
{
    use HasFactory;

    /** @use HasFactory<AdminInvitationFactory> */
    protected $hidden = [
        'token',
    ];

    protected $fillable = [
        'club_id',
        'inviter_id',
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

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }

    public function isValidForUser(User $user): bool
    {
        return ! $user->hasRoleInClub($this->club, $this->role) &&
            $this->expires_at?->isFuture();
    }

    public function scopeByToken(Builder $query, string $token): Builder
    {
        return $query->where('token', $token);
    }
}

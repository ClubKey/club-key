<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $logo_path
 * @property string|null $description
 * @property string|null $website
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $street_address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $zip
 * @property string|null $facebook
 * @property string|null $instagram
 * @property string|null $twitter
 * @property string|null $youtube
 * @property string|null $tiktok
 * @property string|null $linkedin
 * @property string|null $pinterest
 * @property string|null $snapchat
 * @property \Carbon\CarbonImmutable $created_at
 * @property \Carbon\CarbonImmutable $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Visit> $visits
 * @property-read int|null $visits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ClubLocation> $locations
 * @property-read int|null $locations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $members
 * @property-read int|null $members_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $admins
 * @property-read \Illuminate\Database\Eloquent\Collection<int, RewardLevel> $rewardLevels
 * @property-read int|null $reward_levels_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Club newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Club newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Club query()
 *
 * @mixin \Eloquent
 */
final class Club extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo_path',
        'description',
        'website',
        'email',
        'phone',
        'street_address',
        'city',
        'state',
        'zip',
        'facebook',
        'instagram',
        'twitter',
        'youtube',
        'tiktok',
        'linkedin',
        'pinterest',
        'snapchat',
    ];

    protected $casts = [
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
    ];

    public function adminInvitations(): HasMany
    {
        return $this->hasMany(AdminInvitation::class);
    }

    public function admins(): BelongsToMany
    {
        return $this->members()->wherePivot('role', Role::ADMIN->value);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'club_user', 'club_id', 'user_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function locations(): HasMany
    {
        return $this->hasMany(ClubLocation::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function rewardLevels(): HasMany
    {
        return $this->hasMany(RewardLevel::class);
    }
}

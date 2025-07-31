<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ClubRole;
use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;

/**
 * @property int $id
 * @property int|null $owner_id
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
 * @property string|null $x
 * @property string|null $youtube
 * @property string|null $tiktok
 * @property string|null $linkedin
 * @property string|null $pinterest
 * @property string|null $snapchat
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property-read User|null $owner
 * @property-read Collection<int, AdminInvitation> $adminInvitations
 * @property-read int|null $admin_invitations_count
 * @property-read Collection<int, Visit> $visits
 * @property-read int|null $visits_count
 * @property-read Collection<int, ClubLocation> $locations
 * @property-read int|null $locations_count
 * @property-read Collection<int, User> $members
 * @property-read int|null $members_count
 * @property-read Collection<int, User> $admins
 * @property-read Collection<int, RewardLevel> $rewardLevels
 * @property-read int|null $reward_levels_count
 *
 * @method static Builder<static>|Club newModelQuery()
 * @method static Builder<static>|Club newQuery()
 * @method static Builder<static>|Club query()
 *
 * @mixin Eloquent
 */
final class Club extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
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
        'x',
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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function adminInvitations(): HasMany
    {
        return $this->hasMany(AdminInvitation::class);
    }

    public function admins(): BelongsToMany
    {
        return $this->members()->wherePivot('role', ClubRole::ADMIN->value);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'club_user', 'club_id', 'user_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
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
        return $this->hasMany(RewardLevel::class)->orderBy('order');
    }

    public function logoPath(): Attribute
    {
        return new Attribute(
            get: fn ($path) => $path ? asset('storage/'.$path) : null,
            set: function ($value) {
                if ($value instanceof UploadedFile) {
                    return $value->store('clubs', 'public');
                }

                if (is_string($value) && mb_trim($value) !== '') {
                    return mb_trim($value);
                }

                return null;
            }
        );
    }
}

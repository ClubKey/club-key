<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\AppRole;
use App\Enums\ClubRole;
use Carbon\CarbonImmutable;
use Database\Factories\UserFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property ?int $current_club_id
 * @property AppRole $role
 * @property CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, Visit> $visits
 * @property-read int|null $visits_count
 * @property-read Collection<int, Club> $clubs
 * @property-read int|null $clubs_count
 * @property-read ?Club $currentClub
 *
 * @method static UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereEmailVerifiedAt($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereName($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
final class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $appends = ['club_role'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'current_club_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'immutable_datetime',
        'password' => 'hashed',
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
        'role' => AppRole::class,
    ];

    public function currentClub(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role->isSuperAdmin();
    }

    public function hasRoleInClub(Club $club, ClubRole $role): bool
    {
        return $this->clubs()->where('club_id', $club->id)->wherePivot('role', $role->value)->exists();
    }

    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class)
            ->using(ClubUser::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function ownsClub(Club $club): bool
    {
        return $this->id === $club->owner_id;
    }

    public function ownedClubs(): HasMany
    {
        return $this->hasMany(Club::class, 'owner_id', 'id');
    }

    public function belongsToClub(Club $club): bool
    {
        return $this->clubs()->where('club_id', $club->id)->exists();
    }

    public function clubRole(): Attribute
    {
        return new Attribute(
            get: fn () => $this->currentClub ? $this->getRoleInClub($this->currentClub) : null,
        );
    }

    public function getRoleInClub(Club $club): ?ClubRole
    {
        $pivot = $this->clubs()->where('club_id', $club->id)->first()?->pivot;

        if (! $pivot) {
            return null;
        }

        return $pivot->role instanceof ClubRole ? $pivot->role : ClubRole::from($pivot->role);
    }

    public function visitsForClub(Club $club): HasMany
    {
        return $this->visits()->whereClubId($club->id);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ClubRole;
use Carbon\CarbonImmutable;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Cashier\Billable;

/**
 * @property int $id
 * @property int|null $owner_id
 * @property string $name
 * @property string|null $visit_qr_code_path
 * @property-read string $route
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
 * @property-read Collection<int, ClubUser> $clubUsers
 * @property-read int|null $club_users_count
 * @property-read Collection<int, Promotion> $promotions
 * @property-read int|null $promotions_count
 *
 * @method static Builder<static>|Club newModelQuery()
 * @method static Builder<static>|Club newQuery()
 * @method static Builder<static>|Club query()
 *
 * @mixin Eloquent
 */
final class Club extends Model
{
    use Billable, HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'visit_qr_code_path',
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
        'stripe_id',
        'pm_type',
        'pm_last_four',
        'trial_ends_at',
    ];

    protected $casts = [
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
        'trial_ends_at' => 'immutable_datetime',
    ];

    public function route(): Attribute
    {
        return new Attribute(
            get: fn () => route('clubs.show', $this->slug),
        );
    }

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
        return $this->belongsToMany(User::class)
            ->using(ClubUser::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function clubUsers(): HasMany
    {
        return $this->hasMany(ClubUser::class);
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

    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class);
    }

    public function logoPath(): Attribute
    {
        return new Attribute(
            get: fn ($path) => $path ? Storage::disk('public')->url($path) : null,
            set: function ($value) {
                // Handle file uploads
                if ($value instanceof UploadedFile) {
                    // Delete previous logo if it exists
                    $previousPath = $this->getOriginal('logo_path');
                    if ($previousPath && Storage::disk('public')->exists($previousPath)) {
                        Storage::disk('public')->delete($previousPath);
                    }

                    // Store the new file
                    $path = $value->store('clubs', 'public');

                    if ($path === false) {
                        throw new Exception('Failed to upload logo file');
                    }

                    return $path;
                }

                // Handle string values (direct path assignment)
                if (is_string($value) && mb_trim($value) !== '') {
                    return mb_trim($value);
                }

                // Handle null/empty values
                if ($value === null || $value === '') {
                    // Delete previous logo if it exists
                    $previousPath = $this->getOriginal('logo_path');
                    if ($previousPath && Storage::disk('public')->exists($previousPath)) {
                        Storage::disk('public')->delete($previousPath);
                    }
                }

                return null;
            }
        );
    }

    public function visitQrCodePath(): Attribute
    {
        return new Attribute(
            get: fn ($path) => $path ? Storage::disk('public')->url($path) : null,
        );
    }

    public function getAdminCount(): int
    {
        return $this->admins()->count();
    }

    public function calculateSubscriptionPrice(): int
    {
        $basePrice = 5000; // $50.00 in cents
        $adminCount = $this->getAdminCount();
        $includedAdmins = 3;
        
        if ($adminCount > $includedAdmins) {
            $extraAdmins = $adminCount - $includedAdmins;
            $basePrice += ($extraAdmins * 1000); // $10.00 per extra admin in cents
        }

        return $basePrice;
    }
}

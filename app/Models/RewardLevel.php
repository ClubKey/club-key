<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $club_id
 * @property string $name
 * @property int $order
 * @property string $icon_name
 * @property int $min_visits_per_month
 * @property string $color
 * @property string $description
 * @property int $percentage_discount
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Club $club
 * @property-read HasMany<Reward> $rewards
 * @property-read BelongsToMany<User> $users
 * @property-read BelongsToMany<User> $usersWithThisAsNextLevel
 *
 * @method static Builder<static>|RewardLevel newModelQuery()
 * @method static Builder<static>|RewardLevel newQuery()
 * @method static Builder<static>|RewardLevel query()
 */
final class RewardLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id',
        'name',
        'order',
        'icon_name',
        'min_visits_per_month',
        'color',
        'description',
        'percentage_discount',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(Reward::class)->orderBy('order');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, ClubUser::class);
    }

    public function usersWithThisAsNextLevel(): BelongsToMany
    {
        return $this->belongsToMany(User::class, ClubUser::class, 'next_reward_level_id');
    }

    protected static function booted(): void
    {
        self::addGlobalScope('ordered', function (Builder $builder) {
            $builder->orderBy('min_visits_per_month')
                ->orderBy('order');
        });
    }
}

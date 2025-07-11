<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Club $club
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RewardLevel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RewardLevel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RewardLevel query()
 */
final class RewardLevel extends Model
{
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
}

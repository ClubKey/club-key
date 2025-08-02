<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $reward_level_id
 * @property string $description
 * @property int $order
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read RewardLevel $rewardLevel
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reward newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reward newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reward query()
 */
final class Reward extends Model
{
    use HasFactory;

    protected $fillable = [
        'reward_level_id',
        'description',
        'order',
    ];

    public function rewardLevel(): BelongsTo
    {
        return $this->belongsTo(RewardLevel::class);
    }
}

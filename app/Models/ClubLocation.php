<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $club_id
 * @property string|null $name
 * @property string|null $phone
 * @property string $street_address
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property \Carbon\CarbonImmutable $created_at
 * @property \Carbon\CarbonImmutable $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClubLocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClubLocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClubLocation query()
 *
 * @mixin \Eloquent
 */
final class ClubLocation extends Model
{
    protected $fillable = [
        'club_id',
        'name',
        'phone',
        'street_address',
        'city',
        'state',
        'zip',
    ];

    protected $casts = [
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }
}

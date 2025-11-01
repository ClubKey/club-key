<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\OrderDescending;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property Carbon $publish_at
 * @property Carbon|null $published_at
 * @property Carbon $start_at
 * @property Carbon|null $end_at
 * @property string $link
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Club $club
 */
#[ScopedBy(OrderDescending::class)]
final class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id',
        'name',
        'description',
        'publish_at',
        'published_at',
        'start_at',
        'end_at',
        'is_active',
        'link',
    ];

    protected $casts = [
        'publish_at' => 'immutable_datetime',
        'published_at' => 'immutable_datetime',
        'start_at' => 'immutable_datetime',
        'end_at' => 'immutable_datetime',
        'is_active' => 'boolean',
    ];

    #[Scope]
    public function currentlyRunning(Builder $query): void
    {
        $now = now();

        $query->where('is_active', true)
            ->where('published_at', '<=', $now)
            ->where(function (Builder $query) use ($now): void {
                $query->whereNull('start_at')->orWhere('start_at', '<=', $now);
            })
            ->where(function (Builder $query) use ($now): void {
                $query->whereNull('end_at')->orWhere('end_at', '>=', $now);
            })
            ->orderByDesc('publish_at')
            ->orderByDesc('created_at');
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }
}

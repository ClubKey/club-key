<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

/**
 * @property int $id
 * @property int $club_id
 * @property string|null $name
 * @property string|null $visit_qr_code_path
 * @property-read string|null $visit_tap_link
 * @property string|null $phone
 * @property string $street_address
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 *
 * @method static Builder<static>|ClubLocation newModelQuery()
 * @method static Builder<static>|ClubLocation newQuery()
 * @method static Builder<static>|ClubLocation query()
 *
 * @mixin Eloquent
 */
final class ClubLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id',
        'name',
        'visit_qr_code_path',
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

    public function visitQrCodePath(): Attribute
    {
        return new Attribute(
            get: fn($path) => $path ? Storage::disk('public')->url($path) : null,
        );
    }

    public function visitTapLink(): Attribute
    {
        return new Attribute(
            get: fn() => URL::signedRoute('clubs.visit', [
                'club' => $this->club->slug,
                'clubLocation' => $this->id,
            ]),
        );
    }
}

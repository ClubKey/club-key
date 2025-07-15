<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ClubRole;
use App\Http\Requests\ClubRequest;
use App\Models\Club;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final readonly class CreateClubAction
{
    /**
     * Execute the action.
     */
    public function handle(ClubRequest $request): Club
    {
        return DB::transaction(function () use ($request): Club {
            $data = $request->validated();
            $logo = $data['logo'];
            if ($logo) {
                $logoPath = $logo->store('clubs', 'public');
                $data['logo_path'] = $logoPath;
            }
            unset($data['logo']);

            $club = Club::create($data);

            $club->members()->attach(Auth::id(), ['role' => ClubRole::ADMIN->value]);

            return $club;
        });
    }
}

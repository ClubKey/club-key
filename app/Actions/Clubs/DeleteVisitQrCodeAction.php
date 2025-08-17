<?php

declare(strict_types=1);

namespace App\Actions\Clubs;

use App\Models\Club;
use App\Models\ClubLocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

final readonly class DeleteVisitQrCodeAction
{
    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(?ClubLocation $clubLocation = null, ?Club $club = null): void
    {
        DB::transaction(function () use ($clubLocation, $club): void {
            if ($clubLocation) {
                if ($clubLocation->visit_qr_code_path && Storage::disk('public')->exists($clubLocation->visit_qr_code_path)) {
                    Storage::disk('public')->delete($clubLocation->visit_qr_code_path);
                }
                $clubLocation->visit_qr_code_path = null;
                $clubLocation->save();
            }

            if ($club) {
                if ($club->visit_qr_code_path && Storage::disk('public')->exists($club->visit_qr_code_path)) {
                    Storage::disk('public')->delete($club->visit_qr_code_path);
                }
                $club->visit_qr_code_path = null;
                $club->save();
            }
        });
    }
}

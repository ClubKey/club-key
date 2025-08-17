<?php

declare(strict_types=1);

namespace App\Actions\Clubs;

use App\Models\ClubLocation;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteClubLocationAction
{
    public function __construct(
        private DeleteVisitQrCodeAction $deleteQrCodeAction,
    ) {}

    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(ClubLocation $clubLocation): void
    {
        DB::transaction(function () use ($clubLocation): void {
            $this->deleteQrCodeAction->handle($clubLocation);

            $clubLocation->delete();
        });
    }
}

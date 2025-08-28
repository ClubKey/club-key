<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Clubs\VisitQrCodeGeneratorAction;
use App\Models\Club;
use Illuminate\Console\Command;

final class GenerateNewQrCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-new-qr-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate fresh new QR Codes for all clubs and club locations.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Generating QR Codes for all clubs...');

        $this->newLine();

        $this->info('Progress:');

        $clubs = Club::with('locations')->get();

        $this->withProgressBar($clubs, function (Club $club) {
            $qrGeneratorAction = new VisitQrCodeGeneratorAction;
            $qrGeneratorAction->handle(club: $club);

            foreach ($club->locations as $location) {
                $qrGeneratorAction->handle(clubLocation: $location);
            }
        });

        $this->newLine(2);

        $this->info('Club QR Codes have been generated.');
    }
}

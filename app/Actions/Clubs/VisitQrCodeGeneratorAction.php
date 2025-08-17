<?php

declare(strict_types=1);

namespace App\Actions\Clubs;

use App\Models\Club;
use App\Models\ClubLocation;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Exception\ValidationException;
use Endroid\QrCode\Writer\PngWriter;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Throwable;
use URL;

final readonly class VisitQrCodeGeneratorAction
{
    /**
     * Only provide either a ClubLocation or a Club, not both.
     * When a Club is provided, the QR code will be generated for the club's visit URL, and saved to the clubs table.
     * When a ClubLocation is provided, the QR code will be generated for the club location
     *
     * Returns true if the QR code was successfully generated and saved, false otherwise.
     */
    public function handle(?ClubLocation $clubLocation = null, ?Club $club = null): bool
    {
        try {
            return DB::transaction(function () use ($clubLocation, $club): bool {
                if ($clubLocation) {
                    if ($clubLocation->visit_qr_code_path) {
                        // Delete the old QR code file if it exists
                        $oldFilePath = $clubLocation->visit_qr_code_path;
                        if (Storage::disk('public')->exists($oldFilePath)) {
                            Storage::disk('public')->delete($oldFilePath);
                        }
                    }
                    $clubLocation->visit_qr_code_path = $this->generateQrCode(clubLocation: $clubLocation);

                    return $clubLocation->save();
                }

                if ($club) {
                    if ($club->visit_qr_code_path) {
                        // Delete the old QR code file if it exists
                        $oldFilePath = $club->visit_qr_code_path;
                        if (Storage::disk('public')->exists($oldFilePath)) {
                            Storage::disk('public')->delete($oldFilePath);
                        }
                    }
                    $club->visit_qr_code_path = $this->generateQrCode(club: $club);

                    return $club->save();
                }
            });
        } catch (Exception|Throwable $exception) {
            Log::error($exception->getMessage());

            return false;
        }
    }

    /**
     * @throws ValidationException
     */
    private function generateQrCode(?ClubLocation $clubLocation = null, ?Club $club = null): string
    {
        if ($clubLocation) {
            $url = URL::signedRoute('clubs.visit', ['club' => $clubLocation->club->slug, 'clubLocation' => $clubLocation->id]);
        } elseif ($club) {
            $url = URL::signedRoute('clubs.visit', ['club' => $club->slug]);
        } else {
            throw new InvalidArgumentException('Either clubLocation or club must be provided.');
        }

        $builder = new Builder(
            writer: new PngWriter(),
            data: $url,
            size: 500,
            margin: 0,
        );

        $result = $builder->build();

        // save the file, to the public storage path
        $filePath = 'qr-codes/'.md5($url).'.png';
        $result = Storage::disk('public')->put($filePath, $result->getString(), [
            'visibility' => 'public',
            'ContentType' => $result->getMimeType(),
        ]);

        if ($result === false) {
            throw new Exception('Failed to save QR code image');
        }

        // return the file path
        return $filePath;
    }
}

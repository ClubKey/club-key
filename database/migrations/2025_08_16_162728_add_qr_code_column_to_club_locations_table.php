<?php

declare(strict_types=1);

use App\Actions\Clubs\VisitQrCodeGeneratorAction;
use App\Models\Club;
use App\Models\ClubLocation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('club_locations', function (Blueprint $table) {
            $table->string('visit_qr_code_path')->nullable()->after('name');
        });

        $qrCodeGeneratorAction = new VisitQrCodeGeneratorAction();

        $clubLocations = ClubLocation::all();

        foreach ($clubLocations as $clubLocation) {
            $qrCodeGeneratorAction->handle(clubLocation: $clubLocation);
        }

        Schema::table('clubs', function (Blueprint $table) {
            $table->string('visit_qr_code_path')->nullable()->after('name');
        });

        $clubs = Club::all();
        foreach ($clubs as $club) {
            $qrCodeGeneratorAction->handle(club: $club);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('club_locations', function (Blueprint $table) {
            $table->dropColumn('visit_qr_code_path');
        });

        Schema::table('clubs', function (Blueprint $table) {
            $table->dropColumn('visit_qr_code_path');
        });
    }
};

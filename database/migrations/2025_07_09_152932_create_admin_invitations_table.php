<?php

declare(strict_types=1);

use App\Enums\ClubRole;
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
        Schema::create('admin_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->index()->constrained('clubs');
            $table->string('email');
            $table->string('token');
            $table->enum('role', [ClubRole::ADMIN->value])->default(ClubRole::ADMIN->value);
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_invitations');
    }
};

<?php

declare(strict_types=1);

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
        Schema::table('club_user', function (Blueprint $table) {
            $table->foreignId('reward_level_id')->nullable()->constrained('reward_levels')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('club_user', function (Blueprint $table) {
            $table->dropForeign(['reward_level_id']);
            $table->dropColumn('reward_level_id');
        });
    }
};

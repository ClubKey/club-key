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
            $table->after('reward_level_id', function (Blueprint $table) {
                $table->integer('visits_past_30_days')->default(0);
                $table->foreignId('next_reward_level_id')->nullable()->constrained('reward_levels');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('club_user', function (Blueprint $table) {
            $table->dropColumn('visits_past_30_days');
            $table->dropForeign(['next_reward_level_id']);
            $table->dropColumn('next_reward_level_id');
        });
    }
};

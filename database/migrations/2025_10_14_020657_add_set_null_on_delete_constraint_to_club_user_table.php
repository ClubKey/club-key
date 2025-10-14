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
            $table->dropForeign(['reward_level_id']);
            $table->dropForeign(['next_reward_level_id']);

            $table->foreign('reward_level_id')->references('id')->on('reward_levels')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('next_reward_level_id')->references('id')->on('reward_levels')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};

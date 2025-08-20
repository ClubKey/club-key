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
        Schema::table('users', function (Blueprint $table) {
            $table->after('password', function ($table) {
                $table->dateTime('accepted_terms')->nullable();
                $table->dateTime('accepted_marketing')->nullable();
            });
        });

        DB::table('users')->update(['accepted_terms' => now(), 'accepted_marketing' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('accepted_terms');
            $table->dropColumn('accepted_marketing');
        });
    }
};

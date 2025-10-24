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
        Schema::table('clubs', function (Blueprint $table): void {
            $table->string('subscription_name')->nullable()->after('slug');
            $table->string('stripe_subscription_id')->nullable()->after('subscription_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table): void {
            $table->dropColumn([
                'subscription_name',
                'stripe_subscription_id',
            ]);
        });
    }
};

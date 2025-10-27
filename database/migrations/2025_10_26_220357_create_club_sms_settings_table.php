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
        Schema::create('club_sms_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->string('vonage_number')->unique();
            $table->string('vonage_number_id')->nullable();
            $table->string('current_plan');
            $table->unsignedInteger('monthly_limit')->default(0);
            $table->unsignedInteger('messages_sent_this_period')->default(0);
            $table->unsignedInteger('messages_received_this_period')->default(0);
            $table->unsignedInteger('overage_messages')->default(0);
            $table->unsignedInteger('base_price_cents')->default(0);
            $table->unsignedInteger('overage_rate_cents')->default(0);
            $table->timestamp('billing_cycle_resets_at')->nullable();
            $table->string('billing_provider_subscription_id')->nullable();
            $table->timestamps();

            $table->unique('club_id');
            $table->index('current_plan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_sms_settings');
    }
};

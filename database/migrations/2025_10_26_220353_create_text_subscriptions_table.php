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
        Schema::create('text_subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('phone_number');
            $table->boolean('is_subscribed')->default(true);
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('opt_in_source')->nullable();
            $table->timestamps();

            $table->unique(['club_id', 'phone_number']);
            $table->index(['club_id', 'is_subscribed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('text_subscriptions');
    }
};

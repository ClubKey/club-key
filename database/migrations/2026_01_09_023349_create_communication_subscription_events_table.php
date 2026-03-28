<?php

declare(strict_types=1);

use App\Enums\SubscriptionEventType;
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
        Schema::create('communication_subscription_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')
                ->constrained('communication_subscriptions')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->enum('event_type', SubscriptionEventType::values());
            $table->dateTime('occurred_at');
            $table->string('actor_type')->nullable();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('source')->nullable();
            $table->json('provider_payload')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('subscription_id');
            $table->index('event_type');
            $table->index('occurred_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communication_subscription_events');
    }
};

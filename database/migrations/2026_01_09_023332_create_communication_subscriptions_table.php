<?php

declare(strict_types=1);

use App\Enums\ContactType;
use App\Enums\SubscriptionStatus;
use App\Enums\Topic;
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
        Schema::create('communication_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('club_id')
                ->constrained('clubs')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->enum('contact_type', ContactType::values());
            $table->string('contact_value');
            $table->enum('topic', Topic::values());
            $table->enum('status', SubscriptionStatus::values())->default(SubscriptionStatus::SUBSCRIBED->value);
            $table->dateTime('subscribed_at')->nullable();
            $table->dateTime('unsubscribed_at')->nullable();
            $table->text('unsubscribe_reason')->nullable();
            $table->string('source')->nullable();
            $table->string('source_message_id')->nullable();
            $table->string('provider_message_id')->nullable();
            $table->json('consent_proof')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index(['contact_type', 'contact_value']);
            $table->index('status');
            $table->index('club_id');
            $table->unique(['contact_type', 'contact_value', 'topic', 'club_id'], 'unique_subscription');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communication_subscriptions');
    }
};

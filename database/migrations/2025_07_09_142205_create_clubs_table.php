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
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('slug')->unique();
            $table->text('logo_path')->nullable();
            $table->text('description')->nullable();
            $table->text('website')->nullable();
            $table->text('email')->nullable();
            $table->text('phone')->nullable();
            $table->text('street_address')->nullable();
            $table->text('city')->nullable();
            $table->text('state')->nullable();
            $table->text('zip')->nullable();
            $table->text('facebook')->nullable();
            $table->text('instagram')->nullable();
            $table->text('x')->nullable();
            $table->text('youtube')->nullable();
            $table->text('tiktok')->nullable();
            $table->text('linkedin')->nullable();
            $table->text('pinterest')->nullable();
            $table->text('snapchat')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};

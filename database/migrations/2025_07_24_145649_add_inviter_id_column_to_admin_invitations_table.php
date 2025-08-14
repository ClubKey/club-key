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
        Schema::table('admin_invitations', function (Blueprint $table) {
            $table->foreignId('inviter_id')
                ->nullable()
                ->index()
                ->after('id')
                ->comment('The ID of the user who sent the invitation')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_invitations', function (Blueprint $table) {
            $table->dropForeign(['inviter_id']);
            $table->dropColumn('inviter_id');
        });
    }
};

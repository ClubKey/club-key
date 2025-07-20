<?php

namespace Database\Seeders;

use App\Enums\AppRole;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $superAdminName = config('app.super_admin_name');
        $superAdminEmail = config('app.super_admin_email');
        $superAdminPassword = config('app.super_admin_password');

        if ($superAdminName && $superAdminEmail && $superAdminPassword) {
            User::factory()->create([
                'name' => $superAdminName,
                'email' => $superAdminEmail,
                'password' => $superAdminPassword,
                'role' => AppRole::SUPER_ADMIN,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    /**
     * Create or update the single admin account from config/admin.php.
     */
    public function run(): void
    {
        $email = config('admin.email');
        $password = config('admin.password');

        if (blank($email) || blank($password)) {
            throw new RuntimeException('Set ADMIN_EMAIL and ADMIN_PASSWORD in the .env file before seeding the admin account.');
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => config('admin.name'),
                'password' => $password,
                'email_verified_at' => now(),
            ],
        );
    }
}

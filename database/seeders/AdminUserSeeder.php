<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = config('app.admin_seed_password');

        if (! is_string($password) || $password === '') {
            throw new RuntimeException('Set ADMIN_SEED_PASSWORD before seeding the admin account.');
        }

        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ],
        );

        $admin->syncRoles(['admin']);
    }
}

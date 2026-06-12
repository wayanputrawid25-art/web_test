<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'Super Admin')->first();

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
                'status' => 'active',
            ]
        );

        if ($superAdminRole && !$admin->hasRole('Super Admin')) {
            $admin->assignRole($superAdminRole);
        }

        $this->command?->info('Default Super Admin created: admin@example.com / password123');
    }
}
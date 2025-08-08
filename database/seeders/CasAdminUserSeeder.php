<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CasAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('cas_user.users')->insertOrIgnore([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('cas_user.users')->insertOrIgnore([
            'username' => 'user',
            'email' => 'user@example.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
            'first_name' => 'Regular',
            'last_name' => 'User',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->command->info('Admin users created successfully!');
        $this->command->info('Admin Login: admin / admin123');
        $this->command->info('User Login: user / user123');
    }
}

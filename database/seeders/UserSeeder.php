<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create CAS admin users with Laravel password hashing
        $users = [
            [
                'username' => 'admin',
                'email' => 'admin@onesystem.com',
                'password' => Hash::make('admin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'rajan',
                'email' => 'rajan@onesystem.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['username' => $userData['username']],
                array_merge($userData, [
                    'role' => $userData['username'] === 'admin' ? 'admin' : 'user',
                    'first_name' => ucfirst($userData['username']),
                    'last_name' => 'User',
                    'is_active' => true,
                ])
            );
        }
    }


}

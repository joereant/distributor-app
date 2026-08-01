<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = 'password';
        $area = Area::first();

        $users = [
            ['name' => 'Demo Owner', 'email' => 'owner@demo.com', 'role' => 'owner'],
            ['name' => 'Demo Admin', 'email' => 'admin@demo.com', 'role' => 'admin'],
            ['name' => 'Demo Sales', 'email' => 'sales@demo.com', 'role' => 'sales', 'sales_area_id' => $area?->id],
            ['name' => 'Demo Customer', 'email' => 'customer@demo.com', 'role' => 'customer'],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                array_merge([
                    'password' => Hash::make($password),
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'verified_at' => now(),
                ], $user)
            );
        }

        $this->command->info("Demo users siap. Password semua: {$password}");
    }
}

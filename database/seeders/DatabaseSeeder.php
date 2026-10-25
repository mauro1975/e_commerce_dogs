<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@dogshop.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        // Demo customer
        User::firstOrCreate(
            ['email' => 'customer@dogshop.com'],
            [
                'name'     => 'Jane Customer',
                'password' => Hash::make('password'),
                'role'     => 'user',
            ]
        );

        $this->call([
            ColorSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            DiscountSeeder::class,
        ]);
    }
}

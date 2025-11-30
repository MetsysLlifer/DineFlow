<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            ProductSeeder::class,
        ]);

        // Seed default admin if not exists
        User::firstOrCreate(
            ['email' => 'admin@dineflow.local'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('Admin!12345'),
                'role' => 'admin',
            ]
        );

        // Optional manager account
        User::firstOrCreate(
            ['email' => 'manager@dineflow.local'],
            [
                'name' => 'Manager',
                'password' => Hash::make('Manager!12345'),
                'role' => 'manager',
            ]
        );
    }
}

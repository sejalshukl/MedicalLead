<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password', // Password is 'password' by default in factory, but good to be explicit or rely on factory default if hashed
            'role' => 'admin',
        ]);

        // Create Coordinator User
        User::factory()->create([
            'name' => 'Coordinator User',
            'email' => 'coordinator@example.com',
            'password' => 'password',
            'role' => 'coordinator',
        ]);
    }
}

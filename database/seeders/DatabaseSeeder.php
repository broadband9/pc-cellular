<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 10 random users
        User::factory(10)->create();

        // Create a specific admin user
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@ezy2.com',
            'password' => Hash::make('password'), // Set the password here
        ]);

        // Ensure roles and permissions are properly set up

        // Assign the admin role to the created user
        $adminUser->assignRole('super_admin');
    }

}

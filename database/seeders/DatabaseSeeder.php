<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // Make sure to import the User model
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $superAdmin = Role::create(['name' => 'super_admin']);
       

        // Create the admin user
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@ezy2.com',
            'password' => Hash::make('password'), // Set the password here
        ]);

        // Assign the role to the user
        $adminUser->assignRole($superAdmin);
    }
}

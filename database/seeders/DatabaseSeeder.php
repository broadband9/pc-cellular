<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create the super_admin role
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);

        // Create the admin user
        $adminUser = User::firstOrCreate([
            'email' => 'admin@ezy2.com'
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('password'), // Set the password here
        ]);

        // Assign the role to the user
        $adminUser->assignRole($superAdminRole);
    }
}

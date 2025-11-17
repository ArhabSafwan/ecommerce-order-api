<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // --- 1. Define Roles (Ensure RoleSeeder has run first!) ---
        $adminRole = Role::firstWhere('name', 'admin');
        $vendorRole = Role::firstWhere('name', 'vendor');
        $customerRole = Role::firstWhere('name', 'customer');

        // --- 2. Define Users and their Roles ---
        $usersToSeed = [
            // Admin User (For full control and testing all features)
            [
                'name' => 'Admin Arhab',
                'email' => 'asafwan72@gmail.com',
                'password' => '12345678',
                'role' => $adminRole,
            ],

            // Vendor User 1 (For testing product creation and ownership)
            [
                'name' => 'Vendor Alice',
                'email' => 'vendor1@example.com',
                'password' => '12345678',
                'role' => $vendorRole,
            ],

            // Vendor User 2 (For testing product ownership separation)
            [
                'name' => 'Vendor Bob',
                'email' => 'vendor2@example.com',
                'password' => '12345678',
                'role' => $vendorRole,
            ],

            // Customer User 1 (For testing order placement and unauthorized access)
            [
                'name' => 'Customer Carol',
                'email' => 'customer1@example.com',
                'password' => '12345678',
                'role' => $customerRole,
            ],

            // Customer User 2
            [
                'name' => 'Customer David',
                'email' => 'customer2@example.com',
                'password' => '12345678',
                'role' => $customerRole,
            ],
        ];

        // --- 3. Create Users and Attach Roles ---
        foreach ($usersToSeed as $userData) {
            $role = $userData['role'];
            unset($userData['role']); // Remove role object before creating user

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                ]
            );

            // Attach the role only if it's not already attached
            if (!$user->roles()->where('role_id', $role->id)->exists()) {
                $user->roles()->attach($role);
            }
        }
    }
}

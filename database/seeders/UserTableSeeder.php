<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'asafwan72@gmail.com'],
            [
                'name' => 'arhab',
                'password' => bcrypt('12345678'), // Use a secure password in production
            ]
        );
        $role = \App\Models\Role::firstWhere('name', 'admin');
        $user->roles()->attach($role);

    }
}

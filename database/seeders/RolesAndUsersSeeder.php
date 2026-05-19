<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['Super Admin', 'Team Leader', 'CEO', 'HR Manager'];

        foreach ($roles as $role) {
            User::firstOrCreate(
                ['email' => str_replace(' ', '', strtolower($role)) . '@bpo4all.test'],
                [
                    'name' => $role . ' User',
                    'password' => Hash::make('password'),
                    'role' => $role,
                ]
            );
        }
    }
}

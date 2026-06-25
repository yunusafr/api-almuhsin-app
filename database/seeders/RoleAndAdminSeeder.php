<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RoleAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Roles
        $roles = ['Super Admin', 'Asatidz', 'Bendahara', 'Keamanan'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // 2. Buat User Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@almuhsin.app'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password123'), // Default password
            ]
        );

        // 3. Assign Role ke User
        $admin->assignRole('Super Admin');
    }
}

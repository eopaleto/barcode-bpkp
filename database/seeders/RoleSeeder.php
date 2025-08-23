<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['SuperAdmin', 'Admin', 'Operator'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('123456'),
            ]
        );
        $superAdmin->assignRole('SuperAdmin');

        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('123456'),
            ]
        );
        $admin->assignRole('Admin');

        $operator = User::firstOrCreate(
            ['email' => 'operator@gmail.com'],
            [
                'name' => 'Operator',
                'password' => Hash::make('123456'),
            ]
        );
        $operator->assignRole('Operator');
    }
}

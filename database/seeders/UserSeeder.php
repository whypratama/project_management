<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Super Admin jika belum ada
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@pro.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'job_title_id' => 1,
                'organization_id' => 1,
            ]
        );
        $superAdmin->assignRole('Super Admin');

        // Buat Admin Aplikasi jika belum ada
        $adminUser = User::firstOrCreate(
            ['email' => 'admin-app@pro.test'],
            [
                'name' => 'Admin Aplikasi',
                'password' => Hash::make('password'),
                'job_title_id' => 2,
                'organization_id' => 5,
            ]
        );
        $adminUser->assignRole('Admin');
    }
}
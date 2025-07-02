<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Urutan ini SANGAT PENTING.
        // Role harus dibuat terlebih dahulu sebelum bisa diberikan ke User.
        $this->call([
            RoleSeeder::class,
            JobTitleSeeder::class,
            OrganizationSeeder::class,
            UserSeeder::class,
        ]);
    }
}

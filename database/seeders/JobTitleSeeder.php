<?php

namespace Database\Seeders;

use App\Models\JobTitle;
use Illuminate\Database\Seeder;

class JobTitleSeeder extends Seeder
{
    public function run(): void
    {
        $titles = [
            ['id' => 1, 'name' => 'Direksi'],
            ['id' => 2, 'name' => 'Pemimpin Divisi'],
            ['id' => 3, 'name' => 'Pemimpin Departemen'],
            ['id' => 4, 'name' => 'Staff'],
        ];

        foreach ($titles as $title) {
            JobTitle::updateOrCreate(['id' => $title['id']], $title);
        }
    }
}
<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $orgs = [
            ['id' => 1, 'name' => 'Direktur Utama', 'parent_id' => null],
            ['id' => 2, 'name' => 'Direktur Kepatuhan', 'parent_id' => 1],
            ['id' => 3, 'name' => 'Direktur Pemasaran', 'parent_id' => 1],
            ['id' => 4, 'name' => 'Direktur Operasional', 'parent_id' => 1],
            ['id' => 5, 'name' => 'Divisi Human Capital', 'parent_id' => 2],
            ['id' => 6, 'name' => 'Bidang Operasional', 'parent_id' => 5],
            ['id' => 7, 'name' => 'Bidang Diklat', 'parent_id' => 5],
            ['id' => 8, 'name' => 'Bidang Strategi dan Pengembangan', 'parent_id' => 5],
        ];

        foreach ($orgs as $org) {
            Organization::updateOrCreate(['id' => $org['id']], $org);
        }
    }
}
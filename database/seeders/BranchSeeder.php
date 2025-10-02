<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('branch')->insert([
            ['name' => 'Chi nhánh trung tâm', 'address' => '12 Đô Lương, phường Vũng Tàu, tỉnh HCM'],
            ['name' => 'Chi nhánh 1', 'address' => '177 Lê Hồng Phong, phường Vũng Tàu, tỉnh HCM'],
            ['name' => 'Chi nhánh 2', 'address' => '24 đường 3/2, phường Vũng Tàu, tỉnh HCM']
        ]);
    }
}

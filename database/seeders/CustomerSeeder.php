<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('customer')->insert([
            ['full_name' => 'Phạm Chí Trọng', 'gender' => 'other', 'birth_date' => '2002-10-22', 'address' => '12 đô lương', 'email' => 'phamchitrong@gmail.com'],
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('room')->insert([
            ['name' => "P.101", "room_type_id" => 1, "area" => "Tầng 1", "status" => "Đang kinh doanh", "note" => "", "branch_id" => 1],
            ['name' => "P.102", "room_type_id" => 3, "area" => "Tầng 1", "status" => "Đang kinh doanh", "note" => "", "branch_id" => 2],
            ['name' => "P.103", "room_type_id" => 2, "area" => "Tầng 1", "status" => "Đang kinh doanh", "note" => "", "branch_id" => 3],
            ['name' => "P.104", "room_type_id" => 4, "area" => "Tầng 1", "status" => "Đang kinh doanh", "note" => "", "branch_id" => 2],
            ['name' => "P.202", "room_type_id" => 1, "area" => "Tầng 2", "status" => "Đang kinh doanh", "note" => "", "branch_id" => 1],
            ['name' => "P.203", "room_type_id" => 4, "area" => "Tầng 2", "status" => "Đang kinh doanh", "note" => "", "branch_id" => 3],
            ['name' => "P.305", "room_type_id" => 3, "area" => "Tầng 3", "status" => "Đang kinh doanh", "note" => "", "branch_id" => 2],
            ['name' => "P.109", "room_type_id" => 2, "area" => "Tầng 1", "status" => "Đang kinh doanh", "note" => "", "branch_id" => 2],

        ]);
    }
}

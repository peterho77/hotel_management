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
            ['name' => "P.101", "room_type_id" => 1, "floor" => 1, "status" => "active", "note" => "", "branch_id" => 1],
            ['name' => "P.102", "room_type_id" => 3, "floor" => 1, "status" => "inactive", "note" => "", "branch_id" => 2],
            ['name' => "P.103", "room_type_id" => 2, "floor" => 1, "status" => "active", "note" => "", "branch_id" => 3],
            ['name' => "P.104", "room_type_id" => 4, "floor" => 1, "status" => "active", "note" => "", "branch_id" => 2],
            ['name' => "P.202", "room_type_id" => 1, "floor" => 2, "status" => "inactive", "note" => "", "branch_id" => 1],
            ['name' => "P.203", "room_type_id" => 4, "floor" => 2, "status" => "active", "note" => "", "branch_id" => 3],
            ['name' => "P.305", "room_type_id" => 3, "floor" => 3, "status" => "inactive", "note" => "", "branch_id" => 2],
            ['name' => "P.109", "room_type_id" => 2, "floor" => 1, "status" => "active", "note" => "", "branch_id" => 2],

        ]);
    }
}

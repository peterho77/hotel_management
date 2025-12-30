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
        $rooms = [
            // Tầng 1 (Standard - ID 1)
            ['name' => "P.101", "room_type_id" => 1, "floor" => 1, "status" => "active", "note" => "", "branch_id" => 1],
            ['name' => "P.102", "room_type_id" => 1, "floor" => 1, "status" => "inactive", "note" => "", "branch_id" => 1],
            ['name' => "P.103", "room_type_id" => 1, "floor" => 1, "status" => "active", "note" => "", "branch_id" => 1],
            ['name' => "P.104", "room_type_id" => 1, "floor" => 1, "status" => "active", "note" => "", "branch_id" => 1],
            ['name' => "P.105", "room_type_id" => 1, "floor" => 1, "status" => "active", "note" => "", "branch_id" => 1],

            // Tầng 2 (Standard - ID 1)
            ['name' => "P.201", "room_type_id" => 1, "floor" => 2, "status" => "inactive", "note" => "", "branch_id" => 1],
            ['name' => "P.202", "room_type_id" => 1, "floor" => 2, "status" => "inactive", "note" => "", "branch_id" => 1],
            ['name' => "P.203", "room_type_id" => 1, "floor" => 2, "status" => "active", "note" => "", "branch_id" => 1],
            ['name' => "P.204", "room_type_id" => 1, "floor" => 2, "status" => "active", "note" => "", "branch_id" => 1],
            ['name' => "P.205", "room_type_id" => 1, "floor" => 2, "status" => "active", "note" => "", "branch_id" => 1],

            // Tầng 3 (Superior - ID 2 & Deluxe - ID 3)
            ['name' => "P.301", "room_type_id" => 2, "floor" => 3, "status" => "active", "note" => "", "branch_id" => 1],
            ['name' => "P.302", "room_type_id" => 2, "floor" => 3, "status" => "active", "note" => "", "branch_id" => 1],
            ['name' => "P.303", "room_type_id" => 2, "floor" => 3, "status" => "active", "note" => "", "branch_id" => 1],
            ['name' => "P.304", "room_type_id" => 3, "floor" => 3, "status" => "inactive", "note" => "", "branch_id" => 1],
            ['name' => "P.305", "room_type_id" => 3, "floor" => 3, "status" => "active", "note" => "", "branch_id" => 1],

            // Tầng 4 (Deluxe - ID 3 & Executive - ID 4)
            ['name' => "P.401", "room_type_id" => 3, "floor" => 4, "status" => "active", "note" => "", "branch_id" => 1],
            ['name' => "P.402", "room_type_id" => 3, "floor" => 4, "status" => "active", "note" => "", "branch_id" => 1],
            ['name' => "P.403", "room_type_id" => 4, "floor" => 4, "status" => "active", "note" => "", "branch_id" => 1],
            ['name' => "P.404", "room_type_id" => 4, "floor" => 4, "status" => "active", "note" => "", "branch_id" => 1],
            ['name' => "P.405", "room_type_id" => 4, "floor" => 4, "status" => "inactive", "note" => "", "branch_id" => 1],

            // Tầng 5, 6 (Presidential - ID 5)
            ['name' => "P.501", "room_type_id" => 5, "floor" => 5, "status" => "active", "note" => "", "branch_id" => 1],
            ['name' => "P.601", "room_type_id" => 5, "floor" => 6, "status" => "active", "note" => "", "branch_id" => 1],
        ];

        // Insert Batch (Nhanh)
        DB::table('room')->insert($rooms);
        $types = DB::table('room_type')->get();

        foreach ($types as $type) {
            // A. Đếm tổng vật lý (Bất kể trạng thái) -> Update RoomType
            $totalCount = DB::table('room')
                ->where('room_type_id', $type->id)
                ->count();

            DB::table('room_type')
                ->where('id', $type->id)
                ->update(['total_quantity' => $totalCount]);

            // B. Đếm tổng bán được (Chỉ Active) -> Update RoomOption
            $activeCount = DB::table('room')
                ->where('room_type_id', $type->id)
                ->where('status', 'active')
                ->count();

            DB::table('room_option')
                ->where('room_type_id', $type->id)
                ->update(['available_quantity' => $activeCount]);
        }

        $this->command->info('Đã insert phòng và đồng bộ số lượng (Total & Available)!');
    }
}

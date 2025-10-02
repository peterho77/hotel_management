<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('room_type')->insert([
            ['name' => "Double Bedroom", "description" => "Phòng 1 giường đôi cho 2 người", "quantity" => 10, "hourly_rate" => 10000, "full_day_rate" => 200000, "overnight_rate" => 100000, "status" => "Đang kinh doanh"],
            ['name' => "Single Bedroom", "description" => "Phòng 1 giường đơn", "quantity" => 10, "hourly_rate" => 10000, "full_day_rate" => 200000, "overnight_rate" => 100000, "status" => "Đang kinh doanh"],
            ['name' => "Triple Bedroom", "description" => "Phòng 1 giường đôi và 1 giường đơn cho 3 người", "quantity" => 10, "hourly_rate" => 10000, "full_day_rate" => 200000, "overnight_rate" => 100000, "status" => "Đang kinh doanh"],
            ['name' => "Twin Bedroom", "description" => "Phòng 2 giường đơn", "quantity" => 10, "hourly_rate" => 10000, "full_day_rate" => 200000, "overnight_rate" => 100000, "status" => "Đang kinh doanh"],
        ]);
    }
}

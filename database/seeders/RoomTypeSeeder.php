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
        collect([
            // --- STANDARD ROOM ---
            [
                'name' => 'Standard Room',
                'bed_type' => 'single/twin',
                'description' => 'Phòng tiêu chuẩn, yên tĩnh, phù hợp cho khách lẻ hoặc cặp đôi.',
                'max_adults' => 1,
                'total_quantity' => 20,
                'status' => 'active',
                'base_price' => 500000,
                'hourly_rate' => 30000
            ],

            // --- SUPERIOR & DELUXE ROOM ---
            [
                'name' => 'Superior Room',
                'bed_type' => 'queen',
                'description' => 'Phòng cao cấp hơn với tầm nhìn đẹp hơn hoặc tiện nghi được nâng cấp.',
                'max_adults' => 3,
                'max_children' => 1,
                'total_quantity' => 8,
                'status' => 'active',
                'base_price' => 800000,
            ],
            [
                'name' => 'Deluxe Room',
                'bed_type' => 'queen',
                'description' => 'Phòng rộng rãi, có ban công riêng biệt và khu vực tiếp khách nhỏ.',
                'max_adults' => 4,
                'max_children' => 2,
                'total_quantity' => 6,
                'status' => 'active',
                'base_price' => 1000000,
            ],

            // --- SUITE & VIP ---
            [
                'name' => 'Executive Suite',
                'bed_type' => 'king',
                'description' => 'Suite cao cấp, có phòng khách riêng biệt và tiện nghi văn phòng, lý tưởng cho khách công tác.',
                'max_adults' => 5,
                'max_children' => 2,
                'total_quantity' => 2,
                'status' => 'inactive',
                'base_price' => 1500000,
            ],
            [
                'name' => 'Presidential Suite',
                'bed_type' => 'king',
                'description' => 'Phòng sang trọng nhất của khách sạn, dịch vụ 24/7, phòng ngủ và phòng khách riêng biệt.',
                'max_adults' => 6,
                'max_children' => 2,
                'total_quantity' => 1,
                'status' => 'active',
                'base_price' => 2000000,
            ],
        ])->each(fn($data) => DB::table('room_type')->insert($data));
    }
}

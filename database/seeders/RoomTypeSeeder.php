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
                'hourly_rate' => 30000,
                'images' => ['/img/room/standard-room/sr1.avif','/img/room/standard-room/sr2.avif','/img/room/standard-room/sr3.avif', '/img/room/standard-room/sr4.avif']
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
                'images' => ['/img/room/superior-room/supr1.jpg','/img/room/superior-room/supr2.jpg','/img/room/superior-room/supr3.jpg','/img/room/superior-room/supr4.jpg']
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
                'images' => ['/img/room/room-b2.jpg']
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
                'images' => ['/img/room/room-b3.jpg']
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
                'images' => ['/img/room/room-b4.jpg']
            ],
        ])->each(function ($data) {
            // Tách lấy mảng images ra khỏi dữ liệu room_type
            $images = $data['images'] ?? [];
            unset($data['images']);

            // Insert Room Type và lấy ID
            $roomTypeId = DB::table('room_type')->insertGetId($data);

            // Chèn ảnh mẫu cho Room Type này
            foreach ($images as $index => $path) {
                DB::table('images')->insert([
                    'room_type_id' => $roomTypeId,
                    'path'         => $path,
                    'is_featured'  => $index === 0, // Ảnh đầu tiên làm ảnh đại diện
                    'sort_order'   => $index + 1,
                    'alt_text'     => "Ảnh cho " . $data['name'],
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        });
    }
}

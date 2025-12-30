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
        $roomTypes = [
            // --- STANDARD ROOM (ID: 1) ---
            [
                'id' => 1, // Fix cứng ID để bên RoomSeeder tham chiếu cho đúng
                'name' => 'Standard Room',
                'bed_type' => 'single/twin',
                'description' => 'Phòng tiêu chuẩn, yên tĩnh, phù hợp cho khách lẻ hoặc cặp đôi.',
                'max_adults' => 1,
                'max_children' => 0,
                'total_quantity' => 0, // Để 0, RoomSeeder sẽ tự tính lại
                'status' => 'active',
                'base_price' => 500000,
                'images' => ['/img/room/standard-room/sr1.avif', '/img/room/standard-room/sr2.avif', '/img/room/standard-room/sr3.avif', '/img/room/standard-room/sr4.avif']
            ],

            // --- SUPERIOR ROOM (ID: 2) ---
            [
                'id' => 2,
                'name' => 'Superior Room',
                'bed_type' => 'queen',
                'description' => 'Phòng cao cấp hơn với tầm nhìn đẹp hơn hoặc tiện nghi được nâng cấp.',
                'max_adults' => 3,
                'max_children' => 1,
                'total_quantity' => 0,
                'status' => 'active',
                'base_price' => 800000,
                'images' => ['/img/room/superior-room/supr1.jpg', '/img/room/superior-room/supr2.jpg', '/img/room/superior-room/supr3.jpg', '/img/room/superior-room/supr4.jpg']
            ],

            // --- DELUXE ROOM (ID: 3) ---
            [
                'id' => 3,
                'name' => 'Deluxe Room',
                'bed_type' => 'queen',
                'description' => 'Phòng rộng rãi, có ban công riêng biệt và khu vực tiếp khách nhỏ.',
                'max_adults' => 4,
                'max_children' => 2,
                'total_quantity' => 0,
                'status' => 'active',
                'base_price' => 1000000,
                'images' => ['/img/room/room-b2.jpg']
            ],

            // --- EXECUTIVE SUITE (ID: 4) ---
            [
                'id' => 4,
                'name' => 'Executive Suite',
                'bed_type' => 'king',
                'description' => 'Suite cao cấp, có phòng khách riêng biệt và tiện nghi văn phòng.',
                'max_adults' => 5,
                'max_children' => 2,
                'total_quantity' => 0,
                'status' => 'inactive',
                'base_price' => 1500000,
                'images' => ['/img/room/room-b3.jpg']
            ],

            // --- PRESIDENTIAL SUITE (ID: 5) ---
            [
                'id' => 5,
                'name' => 'Presidential Suite',
                'bed_type' => 'king',
                'description' => 'Phòng sang trọng nhất của khách sạn, dịch vụ 24/7.',
                'max_adults' => 6,
                'max_children' => 2,
                'total_quantity' => 0,
                'status' => 'active',
                'base_price' => 2000000,
                'images' => ['/img/room/room-b4.jpg']
            ],
        ];

        foreach ($roomTypes as $data) {
            // Tách ảnh ra khỏi mảng data để insert bảng room_type trước
            $images = $data['images'] ?? [];
            unset($data['images']);

            // Insert hoặc Update nếu ID đã tồn tại
            DB::table('room_type')->updateOrInsert(['id' => $data['id']], $data);

            // Insert ảnh
            foreach ($images as $index => $path) {
                DB::table('images')->insert([
                    'room_type_id' => $data['id'],
                    'path'         => $path,
                    'is_featured'  => $index === 0,
                    'sort_order'   => $index + 1,
                    'alt_text'     => "Ảnh cho " . $data['name'],
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }
    }
}

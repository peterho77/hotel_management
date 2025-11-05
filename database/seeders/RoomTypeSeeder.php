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
        // Khởi tạo mảng tiện nghi cơ bản
        $basicAmenities = [
            'view' => 'City View',
            'bathroom' => 'Standing Shower',
            'features' => ['Free Wi-Fi', 'Air Conditioning', 'Electric Kettle']
        ];

        // Khởi tạo mảng tiện nghi nâng cao
        $premiumAmenities = [
            'view' => 'Sea View / Premium Corner',
            'bathroom' => 'Separate Bathtub and Shower',
            'features' => array_merge($basicAmenities['features'], ['Minibar', 'Workspace Desk', 'Nespresso Machine', 'Complimentary Ironing Set', 'Premium Toiletries'])
        ];


        DB::table('room_type')->insert([
            // --- STANDARD ROOM (ID 1 & 2) ---
            [
                'name' => 'Standard Room',
                'bed_type' => 'single',
                'description' => 'Phòng tiêu chuẩn, yên tĩnh, phù hợp cho khách lẻ hoặc cặp đôi.',
                'max_adults' => 2,
                'max_children' => 1,
                'total_quantity' => 15,
                'base_price_per_night' => 850000.00,
                'hourly_rate' => NULL,
                'full_day_rate' => NULL,
                'overnight_rate' => NULL,
                'status' => 'active',
                'amenities' => json_encode($basicAmenities), // Tiện nghi cơ bản
            ],
            [
                'name' => 'Standard Room',
                'bed_type' => 'twin',
                'description' => 'Phòng tiêu chuẩn 2 giường đơn, lý tưởng cho đồng nghiệp hoặc bạn bè.',
                'max_adults' => 2,
                'max_children' => 1,
                'total_quantity' => 10,
                'base_price_per_night' => 850000.00,
                'hourly_rate' => NULL,
                'full_day_rate' => NULL,
                'overnight_rate' => NULL,
                'status' => 'active',
                'amenities' => json_encode($basicAmenities),
            ],

            // --- SUPERIOR & DELUXE ROOM (ID 3-6) ---
            [
                'name' => 'Superior Room',
                'bed_type' => 'king',
                'description' => 'Phòng cao cấp hơn với tầm nhìn đẹp hơn hoặc tiện nghi được nâng cấp.',
                'max_adults' => 2,
                'max_children' => 1,
                'total_quantity' => 8,
                'base_price_per_night' => 1100000.00,
                'hourly_rate' => NULL,
                'full_day_rate' => NULL,
                'overnight_rate' => NULL,
                'status' => 'active',
                'amenities' => json_encode(array_merge($basicAmenities, ['view' => 'River View'])), // Nâng cấp tầm nhìn
            ],
            [
                'name' => 'Deluxe Room',
                'bed_type' => 'king',
                'description' => 'Phòng rộng rãi, có ban công riêng biệt và khu vực tiếp khách nhỏ.',
                'max_adults' => 3,
                'max_children' => 2,
                'total_quantity' => 6,
                'base_price_per_night' => 1500000.00,
                'hourly_rate' => NULL,
                'full_day_rate' => 1200000.00,
                'overnight_rate' => NULL,
                'status' => 'active',
                'amenities' => json_encode($premiumAmenities), // Tiện nghi nâng cao
            ],

            // --- SUITE & VIP (ID 8 & 9) ---
            [
                'name' => 'Executive Suite',
                'bed_type' => 'king',
                'description' => 'Suite cao cấp, có phòng khách riêng biệt và tiện nghi văn phòng, lý tưởng cho khách công tác.',
                'max_adults' => 3,
                'max_children' => 2,
                'total_quantity' => 3,
                'base_price_per_night' => 3500000.00,
                'hourly_rate' => NULL,
                'full_day_rate' => NULL,
                'overnight_rate' => NULL,
                'status' => 'active',
                'amenities' => json_encode(array_merge($premiumAmenities, ['view' => 'Panoramic View', 'features' => array_merge($premiumAmenities['features'], ['Private Kitchenette'])])),
            ],
            [
                'name' => 'Presidential Suite',
                'bed_type' => 'king',
                'description' => 'Phòng sang trọng nhất của khách sạn, dịch vụ 24/7, phòng ngủ và phòng khách riêng biệt.',
                'max_adults' => 4,
                'max_children' => 2,
                'total_quantity' => 1,
                'base_price_per_night' => 12000000.00,
                'hourly_rate' => NULL,
                'full_day_rate' => NULL,
                'overnight_rate' => NULL,
                'status' => 'active',
                'amenities' => json_encode(array_merge($premiumAmenities, ['view' => 'Panoramic View', 'features' => array_merge($premiumAmenities['features'], ['Private Butler Service', 'Jacuzzi'])])),
            ],
        ]);
    }
}

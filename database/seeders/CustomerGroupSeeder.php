<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('customer_group')->insert([
            [
                'code' => 'WALKIN',
                'name' => 'Walk-in',
                'customer_type_id' => 1, // Cá nhân
                'discount_percent' => 0,
                'description' => 'Khách vãng lai đến trực tiếp',
            ],
            [
                'code' => 'OTA',
                'name' => 'Online Travel Agency (OTA)',
                'customer_type_id' => 1, // Cá nhân
                'discount_percent' => 15,
                'description' => 'Khách đặt qua Booking, Agoda, Expedia, Traveloka...',
            ],
            [
                'code' => 'CORP',
                'name' => 'Corporate',
                'customer_type_id' => 2, // Công ty
                'discount_percent' => 20,
                'description' => 'Khách công ty theo hợp đồng',
            ],
            [
                'code' => 'TA',
                'name' => 'Travel Agent',
                'customer_type_id' => 3, // Đại lý
                'discount_percent' => 15,
                'description' => 'Khách đặt qua đại lý hoặc công ty lữ hành',
            ],
            [
                'code' => 'GROUP',
                'name' => 'Group / Tour',
                'customer_type_id' => 4, // Đoàn
                'discount_percent' => 25,
                'description' => 'Khách đi theo đoàn, công ty, tour du lịch',
            ],
            [
                'code' => 'LONGSTAY',
                'name' => 'Long-stay',
                'customer_type_id' => 1,
                'discount_percent' => 30,
                'description' => 'Khách ở dài hạn (tuần, tháng)',
            ],
            [
                'code' => 'VIP',
                'name' => 'VIP',
                'customer_type_id' => 5, // VIP
                'discount_percent' => 40,
                'description' => 'Khách thân thiết hoặc khách đặc biệt',
            ],
            [
                'code' => 'MICE',
                'name' => 'MICE',
                'customer_type_id' => 2, // Công ty
                'discount_percent' => 25,
                'description' => 'Khách hội nghị, hội thảo, sự kiện',
            ],
        ]);
    }
}

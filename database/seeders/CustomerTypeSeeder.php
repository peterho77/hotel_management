<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('customer_type')->insert([
            ['code' => 'IND', 'name' => 'Cá nhân', 'description' => 'Khách cá nhân tự đặt hoặc đặt qua OTA'],
            ['code' => 'CORP', 'name' => 'Công ty', 'description' => 'Khách là tổ chức, doanh nghiệp ký hợp đồng'],
            ['code' => 'TA', 'name' => 'Đại lý du lịch', 'description' => 'Khách qua đại lý hoặc công ty du lịch'],
            ['code' => 'GROUP', 'name' => 'Đoàn', 'description' => 'Khách đi theo đoàn, tour, sự kiện'],
            ['code' => 'VIP', 'name' => 'VIP', 'description' => 'Khách thân thiết, khách ưu đãi đặc biệt'],
        ]);
    }
}

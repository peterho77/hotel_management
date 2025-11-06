<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        collect([
            [
                'name' => "Ưu đãi cơ bản",
                "discount_type" => "percentage",
                "discount_value" => 20,
                'stacking_rule' => 'cumulative',
                'priority_level' => 1,
            ],
            [
                'name' => "Ưu đãi lưu trú dài ngày",
                "discount_type" => "percentage",
                "discount_value" => 10,
                'stacking_rule' => 'cumulative',
                'priority_level' => 2,
                'condition' => json_encode([
                    'quantity_based' => ['min_nights' => 7]
                ])
            ],
            [
                'name' => "Ưu đãi đặt phòng theo đoàn",
                "discount_type" => "percentage",
                "discount_value" => 5,
                'stacking_rule' => 'cumulative',
                'priority_level' => 2,
                'condition' => json_encode([
                    'quantity_based' => ['min_total_guests' => 5]
                ])
            ],
            [
                'name' => "Ưu đãi thanh toán VNPay",
                "discount_type" => "fixed",
                "discount_value" => 50000,
                'stacking_rule' => 'cumulative',
                'priority_level' => 3,
                'condition' => json_encode([
                    'payment_based' => ['payment_method_required' => 'VNPAY']
                ])
            ]
        ])->each(fn($data) => DB::table('discount')->insert($data));
    }
}

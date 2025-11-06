<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RatePolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            [
                'name' => "Hủy miễn phí, thanh toán tại khách sạn",
                "cancellation_type" => "free_cancellation",
                "payment_requirement" => "pay_at_hotel",
                'is_refundable' => true,
                'can_change_date' => true,
                'change_deadline_days' => 3,
            ],
            [
                'name' => "Không hoàn tiền, linh động đổi ngày",
                "cancellation_type" => "flexible_change",
                "payment_requirement" => "full_prepayment",
                'is_refundable' => false,
                'can_change_date' => true,
                'change_deadline_days' => 1,
            ],
            [
                'name' => "Đặt cọc 50%, hủy giữ lại cọc",
                "cancellation_type" => "partial_refund",
                "payment_requirement" => "deposit_required",
                'is_refundable' => true,
                'can_change_date' => true,
                'change_deadline_days' => 7,
                'deposit_type' => 'percentage',
                'deposit_value' => 50
            ],
        ])->each(fn($data) => DB::table('rate_policy')->insert($data));
    }
}

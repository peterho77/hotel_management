<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\RoomOption;

class RoomOptionDiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy discount có scope = 'all' và stacking_rule = 'accumulate', sắp theo applied_order
        $discounts = DB::table('discount')
            ->where('scope', 'all')
            ->where('stacking_rule', 'cumulative')
            ->orderBy('applied_order', 'asc')
            ->get();

        if ($discounts->isEmpty()) {
            return;
        }
        $roomOptions = RoomOption::with('room_type')->get();

        foreach ($roomOptions as $option) {
            $currentPrice = $option->price;

            foreach ($discounts as $discount) {
                $discountAmount = 0;

                // Tính số tiền giảm
                if ($discount->discount_type === 'percentage') {
                    $discountAmount = $currentPrice * ($discount->discount_value / 100);
                    $currentPrice -= $discountAmount;
                } elseif ($discount->discount_type === 'fixed') {
                    $discountAmount = min($currentPrice, $discount->discount_value); // không cho âm
                    $currentPrice -= $discountAmount;
                }

                // Ghi vào bảng room_rate_discount
                DB::table('room_option_discount')->insert([
                    'room_option_id' => $option->id,
                    'discount_id' => $discount->id,
                    'applied_amount' => $discount->discount_value,
                    'applied_order' => $discount->priority_level,
                    'discount_applied' => $discountAmount,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}

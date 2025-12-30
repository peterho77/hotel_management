<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomTypes = DB::table('room_type')->get();

        foreach ($roomTypes as $roomType) {

            // 1. Tính số lượng khả dụng thực tế (Chỉ đếm phòng Active)
            // Đây là bước quan trọng nhất để dữ liệu khớp với thực tế
            $activeCount = DB::table('room')
                ->where('room_type_id', $roomType->id)
                ->where('status', 'active')
                ->count();

            // Tính tổng sức chứa tối đa
            $maxGuests = $roomType->max_adults + $roomType->max_children;

            // Tạo các option dựa trên số lượng khách (1 khách, 2 khách...)
            for ($guest = 1; $guest <= $maxGuests; $guest++) {

                // Tính toán số người lớn và trẻ em cho option này
                $numAdults = min($guest, $roomType->max_adults);
                $numChildren = max(0, $guest - $roomType->max_adults);

                DB::table('room_option')->insert([
                    'room_type_id'       => $roomType->id,

                    // SỬA Ở ĐÂY: Dùng số lượng active đã đếm ở trên
                    'available_quantity' => $activeCount,

                    'num_adults'         => $numAdults,
                    'num_children'       => $numChildren,

                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            };
        }
    }
}

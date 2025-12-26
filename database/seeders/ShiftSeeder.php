<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shifts = [
            [
                'name' => 'Ca Sáng',
                'start_time' => '06:00:00',
                'end_time' => '14:00:00',
            ],
            [
                'name' => 'Ca Chiều',
                'start_time' => '14:00:00',
                'end_time' => '22:00:00',
            ],
            [
                'name' => 'Ca Tối', // Hoặc Ca Đêm
                'start_time' => '22:00:00',
                'end_time' => '06:00:00', // Qua ngày hôm sau
            ],
        ];

        foreach ($shifts as $shift) {
            // Sử dụng updateOrInsert để tránh trùng lặp nếu chạy seeder nhiều lần
            // Điều kiện kiểm tra là: Tên ca + Chi nhánh
            DB::table('shift')->updateOrInsert(
                [
                    'name' => $shift['name'],
                ],
                [
                    'start_time' => $shift['start_time'],
                    'end_time' => $shift['end_time'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }
}

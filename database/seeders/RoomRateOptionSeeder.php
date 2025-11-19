<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomRateOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomTypes = DB::table('room_type')->get();

        foreach ($roomTypes as $roomType) {
            $maxGuests = $roomType->max_adults + $roomType->max_children;

            for ($guest = 1; $guest <= $maxGuests; $guest++) {
                DB::table('room_rate_option')->insert([
                    'room_type_id'   => $roomType->id,
                    'available_quantity' => $roomType->total_quantity,
                    'num_adults' => min($guest, $roomType->max_adults),
                    'num_children' => max(0, $guest - $roomType->max_adults)
                ]);
            };
        }
    }
}

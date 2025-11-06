<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomTypeAmenitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomTypes = DB::table('room_type')->get();
        $amenities = DB::table('amenities')->get();

        foreach ($roomTypes as $roomType) {
            foreach ($amenities as $amenity) {
                DB::table('room_type_amenities')->insert([
                    'room_type_id' => $roomType->id,
                    'amenity_id' => $amenity->id
                ]);
            }
        }
    }
}

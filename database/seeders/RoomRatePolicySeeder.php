<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomRatePolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $options  = DB::table('room_rate_option')->get();
        $policies = DB::table('rate_policy')
            ->where('cancellation_type', '!=', 'free_cancellation') // ẩn free for guest
            ->get();

        foreach ($options as $option) {
            foreach ($policies as $policy) {
                DB::table('room_rate_policy')->insert([
                    'room_rate_option_id' => $option->id,
                    'rate_policy_id'      => $policy->id,
                ]);
            }
        }
    }
}

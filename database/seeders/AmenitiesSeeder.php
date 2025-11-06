<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmenitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            ['name' => 'wifi', 'icon' => 'pi pi-wifi'],
            ['name' => 'phone', 'icon' => 'pi pi-phone'],
            ['name' => 'TV', 'icon' => 'pi pi-desktop'],
            ['name' => 'balcony'],
            ['name' => 'bathroom'],
        ])->each(fn($data) => DB::table('amenities')->insert($data));
    }
}

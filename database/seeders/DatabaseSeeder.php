<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $test_users = [[
            'user_name' => 'testadmin',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => bcrypt('thiendat123'),
        ], [
            'user_name' => 'testmanager',
            'email' => 'manager@example.com',
            'role' => 'manager',
            'password' => bcrypt('thiendat123'),
        ]];

        foreach ($test_users as $user) {
            User::factory()->create($user);
        }

        $this->call([
            BranchSeeder::class,
            AmenitiesSeeder::class,
            DiscountSeeder::class,
            RoomTypeSeeder::class,
            RoomTypeBranchSeeder::class,
            RoomTypeAmenitiesSeeder::class,
            RoomSeeder::class,
            CustomerTypeSeeder::class,
            CustomerGroupSeeder::class,
            CustomerSeeder::class,
            RatePolicySeeder::class,
            RoomRateOptionSeeder::class,
        ]);
    }
}

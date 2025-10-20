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
            'full_name' => 'Test Admin',
            'user_name' => 'testadmin',
            'phone' => '0123456779',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => bcrypt('thiendat123'),
        ], [
            'full_name' => 'Test Manager',
            'user_name' => 'testmanager',
            'phone' => '0123456789',
            'email' => 'manager@example.com',
            'role' => 'manager',
            'password' => bcrypt('thiendat123'),
        ]];

        foreach ($test_users as $user) {
            User::factory()->create($user);
        }

        $this->call([
            BranchSeeder::class,
            RoomTypeSeeder::class,
            RoomTypeBranchSeeder::class,
            RoomSeeder::class
        ]);
    }
}

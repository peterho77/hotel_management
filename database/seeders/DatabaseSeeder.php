<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
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
        $test_users = [
            [
                'user_name' => 'testadmin',
                'email' => 'admin@example.com',
                'role' => 'admin',
                'password' => bcrypt('thiendat123'),
            ],
            [
                'user_name' => 'testmanager',
                'email' => 'manager@example.com',
                'role' => 'manager',
                'password' => bcrypt('thiendat123'),
            ],
            [
                'user_name' => 'testemployee',
                'email' => 'employee@example.com',
                'role' => 'employee',
                'password' => bcrypt('thiendat123'),
            ]
        ];

        foreach ($test_users as $user) {
            // Kiểm tra role: Nếu là 'employee' (hoặc 'manager') thì tạo kèm bảng Employee
            $user = User::factory()->create($user);

            // 2. Nếu là employee, lấy ID của $user vừa tạo nhét vào bảng Employee
            if ($user['role'] == 'employee') {
                Employee::factory()->create([
                    'user_id' => $user->id, 
                ]);
            }
        }

        $this->call([
            BranchSeeder::class,
            AmenitiesSeeder::class,
            RoomTypeSeeder::class,
            RoomTypeBranchSeeder::class,
            RoomTypeAmenitiesSeeder::class,
            RoomSeeder::class,
            CustomerTypeSeeder::class,
            CustomerGroupSeeder::class,
            CustomerSeeder::class,
            RatePolicySeeder::class,
            RoomOptionSeeder::class,
            DiscountSeeder::class,
            RoomOptionRatePolicySeeder::class,
            RoomOptionDiscountSeeder::class,
            ShiftSeeder::class,
            ProductSeeder::class,
            ServiceSeeder::class
        ]);
    }
}

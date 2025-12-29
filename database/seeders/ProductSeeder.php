<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Danh sách hàng hóa mẫu cho khách sạn
        $products = [
            // --- NHÓM ĐỒ UỐNG (Minibar) ---
            [
                'name' => 'Nước suối Dasani 350ml',
                'category' => 'drink',
                'cost_price' => 3500,   // Giá nhập
                'selling_price' => 10000, // Giá bán khách sạn (thường cao hơn)
                'min_inventory' => 50,
                'max_inventory' => 500,
                'weight' => 0.35,
                'location' => 'Kho Minibar',
                'description' => 'Nước tinh khiết đóng chai.',
                'note' => 'Hạn sử dụng 12 tháng',
            ],
            [
                'name' => 'Bia Heineken (Lon)',
                'category' => 'drink',
                'cost_price' => 16000,
                'selling_price' => 35000,
                'min_inventory' => 24,
                'max_inventory' => 200,
                'weight' => 0.33,
                'location' => 'Kho Lạnh',
                'description' => 'Bia lon Heineken bạc.',
                'note' => null,
            ],
            [
                'name' => 'Coca Cola (Lon)',
                'category' => 'drink',
                'cost_price' => 9000,
                'selling_price' => 25000,
                'min_inventory' => 24,
                'max_inventory' => 200,
                'weight' => 0.33,
                'location' => 'Kho Minibar',
                'description' => 'Nước ngọt có gas.',
                'note' => null,
            ],
            [
                'name' => 'Rượu Vang Đà Lạt (Chai nhỏ)',
                'category' => 'drink',
                'cost_price' => 45000,
                'selling_price' => 120000,
                'min_inventory' => 5,
                'max_inventory' => 50,
                'weight' => 0.75,
                'location' => 'Kho Rượu',
                'description' => 'Vang đỏ classic.',
                'note' => 'Cần dụng cụ khui',
            ],

            // --- NHÓM ĐỒ ĂN (Snacks/Mì gói) ---
            [
                'name' => 'Mì ly Hảo Hảo Tôm chua cay',
                'category' => 'food',
                'cost_price' => 8000,
                'selling_price' => 20000,
                'min_inventory' => 30,
                'max_inventory' => 300,
                'weight' => 0.1,
                'location' => 'Kho Đồ khô',
                'description' => 'Mì ăn liền dạng ly tiện lợi.',
                'note' => null,
            ],
            [
                'name' => 'Snack khoai tây Pringles (Nhỏ)',
                'category' => 'food',
                'cost_price' => 18000,
                'selling_price' => 45000,
                'min_inventory' => 10,
                'max_inventory' => 100,
                'weight' => 0.15,
                'location' => 'Kho Minibar',
                'description' => 'Khoai tây chiên ống đỏ.',
                'note' => null,
            ],
            [
                'name' => 'Đậu phộng da cá',
                'category' => 'food',
                'cost_price' => 5000,
                'selling_price' => 15000,
                'min_inventory' => 20,
                'max_inventory' => 200,
                'weight' => 0.05,
                'location' => 'Kho Đồ khô',
                'description' => 'Gói nhỏ ăn kèm bia.',
                'note' => null,
            ],
            [
                'name' => 'Chocolate KitKat',
                'category' => 'food',
                'cost_price' => 10000,
                'selling_price' => 25000,
                'min_inventory' => 10,
                'max_inventory' => 100,
                'weight' => 0.04,
                'location' => 'Kho Mát',
                'description' => 'Thanh socola xốp.',
                'note' => 'Tránh để nơi nóng chảy',
            ],
        ];

        foreach ($products as $item) {
            // Sử dụng create để kích hoạt Event 'created' trong Model (sinh mã code tự động)
            Product::create($item);
        }
    }
}

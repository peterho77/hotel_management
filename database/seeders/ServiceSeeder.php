<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Danh sách dịch vụ mẫu cho khách sạn
        $services = [
            // --- DỊCH VỤ GIẶT LÀ ---
            [
                'name' => 'Giặt ủi thường (Theo Kg)',
                'duration' => 24, // 24 giờ trả đồ
                'cost_price' => 10000, // Tiền điện, nước, bột giặt
                'selling_price' => 50000,
                'description' => 'Giặt sấy khô gấp gọn, tính theo cân.',
                'note' => 'Không bao gồm tẩy vết bẩn khó',
            ],
            [
                'name' => 'Giặt hấp Vest/Váy dạ hội (Bộ)',
                'duration' => 48, // 48 giờ
                'cost_price' => 50000,
                'selling_price' => 150000,
                'description' => 'Giặt khô là hơi cao cấp.',
                'note' => 'Kiểm tra kỹ tình trạng áo trước khi nhận',
            ],

            // --- DỊCH VỤ VẬN CHUYỂN ---
            [
                'name' => 'Đón/Tiễn Sân Bay (4 chỗ)',
                'duration' => 2, // Khoảng 2 giờ di chuyển
                'cost_price' => 250000, // Xăng xe + Lương tài xế
                'selling_price' => 450000,
                'description' => 'Xe 4 chỗ đời mới, bao gồm phí cầu đường.',
                'note' => 'Cần đặt trước 4 tiếng',
            ],
            [
                'name' => 'Thuê xe máy (Ngày)',
                'duration' => 24, // 1 ngày
                'cost_price' => 0, // Khấu hao xe
                'selling_price' => 150000,
                'description' => 'Xe tay ga Airblade/Vision.',
                'note' => 'Giữ CMND hoặc Passport của khách',
            ],

            // --- DỊCH VỤ SPA & GIẢI TRÍ ---
            [
                'name' => 'Massage Body Thụy Điển (60p)',
                'duration' => 1, // 1 giờ
                'cost_price' => 100000, // Tip KTV + Phụ liệu
                'selling_price' => 350000,
                'description' => 'Massage thư giãn toàn thân với tinh dầu.',
                'note' => null,
            ],
            [
                'name' => 'Vé hồ bơi (Khách vãng lai)',
                'duration' => 0, // Không giới hạn giờ
                'cost_price' => 0,
                'selling_price' => 100000,
                'description' => 'Bao gồm khăn tắm và tủ đồ.',
                'note' => 'Miễn phí cho khách lưu trú',
            ],
            [
                'name' => 'Câu cá giải trí (Lượt)', // Theo ảnh bạn cung cấp trước đó
                'duration' => 4, // Gói 4 giờ
                'cost_price' => 50000, // Mồi câu + cần
                'selling_price' => 200000,
                'description' => 'Trải nghiệm câu cá tại hồ sinh thái.',
                'note' => 'Cung cấp cần và mồi',
            ],

            // --- DỊCH VỤ KHÁC ---
            [
                'name' => 'Setup tiệc BBQ sân vườn',
                'duration' => 3, // Thời gian phục vụ
                'cost_price' => 200000, // Than + Công setup
                'selling_price' => 500000,
                'description' => 'Chuẩn bị bếp nướng, than và bàn ghế.',
                'note' => 'Chưa bao gồm thực phẩm',
            ],
        ];

        foreach ($services as $item) {
            // Sử dụng Service::create để kích hoạt Event 'created' (sinh mã DV...)
            Service::create($item);
        }
    }
}

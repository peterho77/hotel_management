<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Models\RoomBookingItem;
use Carbon\Carbon;
use App\Models\Booking;

class DashboardController extends Controller
{

    public function index()
    {
        $bookingItems = RoomBookingItem::with('room_option.room_type')
            ->whereHas('booking', function ($q) {
                $q->where('status', '!=', 'cancelled'); // Chỉ lấy đơn thành công
            })
            ->get();
        $driver = DB::connection()->getDriverName();

        // 2. Chọn câu lệnh SQL phù hợp
        if ($driver === 'sqlite') {
            // SQLite dùng strftime('%m', ...) trả về chuỗi "01", "02". 
            // Cần CAST sang số nguyên để logic mảng phía dưới hoạt động đúng.
            $monthSelect = "CAST(strftime('%m', created_at) AS INTEGER)";
        } else {
            // MySQL dùng hàm MONTH()
            $monthSelect = "MONTH(created_at)";
        }

        // 3. Thực hiện Query
        $stats = Booking::select(
            DB::raw("$monthSelect as month"), // Sử dụng biến đã chọn ở trên
            DB::raw('SUM(total_price) as total_revenue')
        )
            ->where('status', 'confirmed')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // 4. Chuẩn bị mảng dữ liệu cho 12 tháng
        $revenueByMonth = array_fill(0, 12, 0);

        foreach ($stats as $item) {
            // $item->month sẽ là số từ 1 đến 12
            $revenueByMonth[$item->month - 1] = (int) $item->total_revenue;
        }
        return Inertia::render('Admin/Dashboard', [
            'bookingItems' => $bookingItems,
            'revenueData' => $revenueByMonth
        ]);
    }
}

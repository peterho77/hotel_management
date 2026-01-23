<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Employee;
use App\Models\User;
use App\Models\Branch;
use App\Models\Room;
use App\Models\EmployeeSchedule;
use App\Models\RoomBookingItem;

class EmployeeController extends Controller
{
    public function index()
    {
        $employeeList = Employee::with('branch')->get();
        $branchList = Branch::get();
        return Inertia::render('Manager/Employee', [
            'employeeList' => $employeeList,
            'branchList' => $branchList,
        ]);
    }

    public function dashboard()
    {
        $roomList = Room::with([
            'room_type',                            // Để lấy giá niêm yết
            'current_booking_item.booking.customer'  // Để lấy tên khách đang ở
        ])
            ->orderBy('floor')
            ->orderBy('name')
            ->get()
            ->map(function ($room) {
                return [
                    'id' => $room->id,
                    'name' => $room->name,
                    'floor' => $room->floor,

                    // Logic màu sắc (Gọi accessor getUiStatusAttribute)
                    'status' => $room->ui_status,
                    'housekeeping_status' => $room->housekeeping_status,

                    // Logic Badge Sạch/Bẩn (Lấy trực tiếp từ DB)
                    'isDirty' => $room->housekeeping_status === Room::HK_DIRTY,

                    // Giá tiền: Lấy giá chốt trong booking item (nếu có khách), không thì lấy giá niêm yết
                    'price' => $room->currentBookingItem ? $room->current_booking_item->final_price : $room->room_type->price,

                    'room_type' => $room->room_type,

                    // Thông tin khách (Gọi accessor helper)
                    'guestName' => $room->current_guest_name,
                    'time' => $room->current_time_str,

                    // Note bảo trì (nếu có)
                    'note' => $room->note
                ];
            });
        $roomBookingList = RoomBookingItem::get();
        return Inertia::render('Employee/Dashboard', [
            'roomList' => $roomList,
            'roomBookingList' => $roomBookingList,
        ]);
    }

    public function schedule(string $user_name)
    {
        $userId = User::where('user_name', $user_name)->value('id');
        $employee = Employee::where('user_id', $userId)->first();
        $employeeSchedule = EmployeeSchedule::where('employee_id', $employee->id)
            ->with(['employee', 'shift'])
            ->get();
        return Inertia::render('Employee/Schedule', [
            'employeeSchedule' => $employeeSchedule,
            'employee' => $employee
        ]);
    }
}

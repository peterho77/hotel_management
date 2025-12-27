<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Models\Shift;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use Illuminate\Validation\ValidationException;

class WorkScheduleController extends Controller
{
    public function index(Request $request)
    {
        $weekStart = $request->input('week_start', Carbon::now()->startOfWeek()->format('Y-m-d'));
        $weekEnd = Carbon::parse($weekStart)->endOfWeek()->format('Y-m-d');

        return Inertia::render('Manager/WorkSchedule', [
            // Load danh sách nhân viên
            'allShifts' => Shift::all(),

            // Load lịch làm việc kèm quan hệ shift và employee
            'scheduleData' => Employee::with(['schedules' => function ($query) use ($weekStart, $weekEnd) {
                $query->whereBetween('schedule_date', [$weekStart, $weekEnd])->with('shift');
            }])->get(),

            // Truyền lại ngày bắt đầu để UI hiển thị
            'filters' => [
                'week_start' => $weekStart
            ]
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id'   => 'required|exists:employee,id',
            'shift_id'      => 'required|exists:shift,id',
            'schedule_date' => 'required|string', // Nhận string để parse định dạng VN
            'note'          => 'nullable|string|max:255',
        ]);

        try {
            $formattedDate = Carbon::createFromFormat('d/m/Y', $data['schedule_date'])->format('Y-m-d');
        } catch (\Exception $e) {
            return redirect()->back()->with(
                'error',
                'Định dạng ngày không hợp lệ (yêu cầu ngày/tháng/năm)'
            );
        }

        $isDuplicateSchedule = EmployeeSchedule::where('employee_id', $data['employee_id'])
            ->where('shift_id', $data['shift_id'])
            ->whereDate('schedule_date', $formattedDate)
            ->exists();

        if ($isDuplicateSchedule) {
            // Nếu trùng, trả về lỗi Validation (Frontend sẽ hứng được trong props.errors)
            return redirect()->back()->with(
                'error',
                'Nhân viên này đã được xếp ca làm việc đó trong ngày đã chọn.'
            );
        }

        // 3. Tạo lịch làm việc
        EmployeeSchedule::create([
            'employee_id'   => $data['employee_id'],
            'shift_id'      => $data['shift_id'],
            'schedule_date' => $formattedDate, // Lưu dạng 2021-05-09
            'notes'         => $data['note'] ?? null,
            'created_by'    => Auth::user()->user_name,
        ]);

        return redirect()->back()->with('success', 'Thêm lịch làm việc thành công.');
    }

    public function update(Request $request, $id)
    {
        // 1. Tìm bản ghi cần sửa (nếu không thấy sẽ báo 404)
        $schedule = EmployeeSchedule::findOrFail($id);

        // 2. Validate dữ liệu đầu vào
        $data = $request->validate([
            'employee_id'   => 'required|exists:employee,id',
            'shift_id'      => 'required|exists:shift,id',
            'schedule_date' => 'required|string',
            'note'          => 'nullable|string|max:255',
        ]);

        // 3. Xử lý format ngày tháng
        try {
            $formattedDate = Carbon::createFromFormat('d/m/Y', $data['schedule_date'])->format('Y-m-d');
        } catch (\Exception $e) {
            // Trả về lỗi dạng validation để frontend hiển thị đỏ input
            throw ValidationException::withMessages([
                'schedule_date' => 'Định dạng ngày không hợp lệ (yêu cầu ngày/tháng/năm).'
            ]);
        }

        // 4. KIỂM TRA TRÙNG LẶP (Logic Update)
        // Kiểm tra: Có bản ghi nào KHÁC (id != $id) mà có cùng (User + Shift + Date) không?
        $isDuplicate = EmployeeSchedule::where('employee_id', $data['employee_id'])
            ->where('shift_id', $data['shift_id'])
            ->whereDate('schedule_date', $formattedDate)
            ->where('id', '!=', $id)
            ->exists();

        if ($isDuplicate) {
            // Ném lỗi Validation để Dialog không bị đóng và hiện đỏ ô input
            throw ValidationException::withMessages([
                'shift_id' => 'Nhân viên này đã có ca làm việc trùng trong ngày này.'
            ]);
        }

        // 5. Thực hiện Update
        $schedule->update([
            'employee_id'   => $data['employee_id'],
            'shift_id'      => $data['shift_id'],
            'schedule_date' => $formattedDate,
            'note'         => $data['note'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Cập nhật lịch làm việc thành công.');
    }
}

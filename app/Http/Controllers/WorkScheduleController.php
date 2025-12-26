<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Models\Shift;
use App\Models\EmployeeSchedule;
use App\Models\Employee;

class WorkScheduleController extends Controller
{
    public function index(Request $request)
    {
        $weekStart = $request->input('week_start', Carbon::now()->startOfWeek()->format('Y-m-d'));
        $weekEnd = Carbon::parse($weekStart)->endOfWeek()->format('Y-m-d');

        return Inertia::render('Manager/WorkSchedule', [
            // Load danh sách nhân viên
            'allEmployees' => Employee::all(),

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
}

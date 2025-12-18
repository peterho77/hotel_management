<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Employee;
use App\Models\Branch;

class EmployeeController extends Controller
{
    public function index(){
        $employeeList = Employee::with('branch')->get();
        $branchList = Branch::get();
        return Inertia::render('Manager/Employee', [
            'employeeList' => $employeeList,
            'branchList' => $branchList,
        ]);
    }
}

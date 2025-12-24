<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Models\Employee;

class AdminController extends Controller
{
    public function createAccount($request){
        $request->validate([
            'full_name' => 'required|string|max:30',
            'user_name' => 'required|string|min:8|max:16|unique:users,user_name',
            'gender' => 'required|in:male,female,other',
            'email' => 'required|email|max:30|unique:users,email',
            'phone' => 'nullable|string|max:12',
            'role' => 'required|string',
            'password' => 'required|string|min:8|max:16|confirmed',
            'note' => 'nullable|string|max:200',
        ]);

        $newUser = [
            'full_name' => $request->full_name,
            'phone' => $request->phone ?? '',
            'note' => $request->note ?? '',
            'total_quantity' => $request->total_quantity,
            'max_adults' => $request->max_adults,
            'max_children' => $request->max_children,
            'base_price' => $request->base_price,
            'hourly_rate' => $request->hourly_rate,
            'full_day_rate' => $request->full_day_rate,
            'overnight_rate' => $request->overnight_rate,
            'status' => $request->status,
        ];
    }
}

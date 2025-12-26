<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AdminController extends Controller
{
    public function createAccount(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:30',
            'user_name' => 'required|string|min:8|max:16|unique:users,user_name',
            'gender' => 'required|in:male,female,other',
            'email' => 'required|email|max:30|unique:users,email',
            'phone' => 'nullable|string|max:12',
            'role' => 'required|string',
            'password' => 'required|string|min:8|max:16|confirmed',
            'note' => 'nullable|string|max:200',
        ]);

        $newUser = DB::transaction(function () use ($data) {

            // Mã hóa mật khẩu trước khi lưu
            $data['password'] = Hash::make($data['password']);

            // Tạo User
            $user = User::create($data);

            // 3. Nếu là customer, tạo bản ghi bên bảng customer
            if ($user->role === 'customer') {
                $user->customer()->create([
                    'full_name' => $data['full_name'],
                    'email'     => $user->email,
                    'phone'     => $user->phone ?? '',
                    'note'      => $user->note ?? '',
                ]);
            } else if ($user->role === 'employee') {
                $user->employee()->create([
                    'full_name' => $data['full_name'],
                    'gender' => $data['gender'],
                    'email'  => $user->email,
                    'phone'  => $user->phone ?? '',
                ]);
            }

            return $user;
        });

        return redirect()->back()->with('success', 'Tạo thành công tài khoản ' . $newUser->user_name . ' thành công!');
    }
}

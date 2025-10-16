<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register($request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'user_name' => ['required', 'string', 'max:50', 'unique:users,user_name'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:15', 'regex:/^[0-9]+$/'], // không bắt buộc
            'password' => ['required', 'string', 'min:8','max:12', 'confirmed'],
        ]);

        $user = User::create([
            'full_name' => $validated['full_name'],
            'user_name' => $validated['user_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(['message' => 'Đăng ký thành công', 'user' => $user]);
    }
}

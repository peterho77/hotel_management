<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:30'],
            'user_name' => ['required', 'string', 'max:30', 'unique:users,user_name'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:15', 'regex:/^[0-9]+(?: [0-9]+)*$/'], // không bắt buộc
            'password' => ['required', 'string', 'min:8', 'max:15', 'confirmed'],
        ]);

        $user = User::create([
            'user_name' => $validated['user_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $customer = $user->customer()->create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'has_account' => true
        ]);

        Auth::login($user);
        return redirect()->route('home')
            ->with('user', Auth::user())
            ->with('customer', $customer)
            ->with('success', 'You’ve successfully signed up!');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'account_name' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $input = $credentials['account_name'];

        // Check nhập username hay email
        $user = User::where('email', $input)
            ->orWhere('user_name', $input)
            ->first();

        // Check nhập số điện thoại
        if (!$user && preg_match('/^[0-9]+$/', $input)) {
            $user = User::whereHas('customer', function ($q) use ($input) {
                $q->where('phone', $input);
            })->first();
        }

        // Không tồn tại
        if (!$user) {
            return back()->with('error', 'Account does not exist!');
        }

        // Kiểm tra mật khẩu có đúng không
        if (!Hash::check($credentials['password'], $user->password)) {
            return redirect()
                ->route('home')
                ->with('error', 'Incorrect password!');
        }

        // Login thành công
        Auth::login($user);

        $request->session()->regenerate();

        // Chuyển role
        if ($user->role === 'admin') {
            return redirect()
                ->route('admin.room-type.index')
                ->with('success', 'Welcome back, Admin!');
        } elseif ($user->role === 'manager') {
            return redirect()
                ->route('manager.customer')
                ->with('success', 'Welcome back, Manager!');
        }

        return redirect()
            ->intended(route('home'))
            ->with('success', 'You signed in successfully');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}

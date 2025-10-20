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
            'full_name' => $validated['full_name'],
            'user_name' => $validated['user_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        return redirect()->route('home')
            ->with('user', Auth::user())
            ->with('success', 'You’ve successfully signed up!');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'account_name' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);
        $accountName = $credentials['account_name'];

        // Xác định người dùng nhập email, username hay phone
        if (filter_var($accountName, FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        } elseif (preg_match('/^[0-9]+$/', $accountName)) {
            $field = 'phone';
        } else {
            $field = 'user_name';
        }
        if (Auth::attempt([$field => $accountName, 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()
                    ->route('admin.room-type-management')
                    ->with('user', $user)
                    ->with('success', 'Welcome back, Admin!');
            } else if ($user->role === 'manager') {
                return redirect()
                    ->route('manager.partners')
                    ->with('user', $user)
                    ->with('success', 'Welcome back, Manager!');
            }

            return redirect()->intended(route('home'))
                ->with('user', $user)
                ->with('success', 'You signed in successfully');
        }

        return redirect()->route('home')
            ->with('error', 'You failed to login!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}

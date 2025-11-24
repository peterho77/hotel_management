<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

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
                    ->route('manager.customer')
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

    public function changePassword(Request $request, $user_name)
    {
        $request->validate([
            'current_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|confirmed', // new_password_confirmation
        ]);

        $user = Auth::user(); // hoặc $user = User::find($id);

        // 1. Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Mật khẩu hiện tại không đúng.');
        }

        // 2. Cập nhật mật khẩu mới và password_changed_at
        $user->password = Hash::make($request->new_password);
        $user->password_changed_at = now();
        $user->save();

        return back()->with('success', 'Đổi mật khẩu thành công.');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'full_name'  => 'required|string|max:30',
            'gender'     => 'required|in:male,female',
            'birth_date' => 'required|date'
        ]);

        $user = Auth::user();

        if (!$user || !$user->customer) {
            return back()->with('error', 'Khách hàng không tồn tại');
        };

        $customer = $user->customer;

        $customer->fill([
            'full_name'  => $request->full_name,
            'gender'     => $request->gender,
            'birth_date' => Carbon::parse($request->birth_date)->format('Y-m-d'),
        ]);

        $customer->save();

        return redirect()->route('user.dashboard', $user->user_name )
            ->with('success', 'Thông tin quý khách cập nhật thành công.')
            ->with('reloadUserProfile', true);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}

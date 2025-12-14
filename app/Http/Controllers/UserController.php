<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Booking;
use App\Models\User;

class UserController extends Controller
{
    public function index(){
        $usersList = User::get();
        return Inertia::render(
            'Admin/Account',
            [
                'usersList' => $usersList,
            ]
        );
    }
    public function changePassword(Request $request)
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

        return redirect()->route('user.profile', $user->user_name)
            ->with('success', 'Thông tin quý khách cập nhật thành công.');
    }

    public function bookingHistory()
    {
        $userId = Auth::id();

        $bookings = Booking::with('latestPayment')->whereHas('customer', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->with(['customer'])
            ->get();
        return Inertia::render(
            'User/Booking-history',
            [
                'bookings' => $bookings,
                'user_name' => Auth::user()->user_name
            ]
        );
    }
}

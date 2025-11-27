<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\GuestController;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

Route::redirect('/home', '/');
Route::inertia('/', 'Guest/Home')->name('home');

//login, register account
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

// booking page
Route::prefix('booking')->group(function () {
    Route::name('booking.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::post('/detail', [BookingController::class, 'detail'])->name('detail');
        Route::post('/confirm', [BookingController::class, 'confirm'])->name('confirm');
    });
    Route::get('/{bookingId}/payment/vnpay', [PaymentController::class, 'vnpayCheckout'])->name('payment.vnpay');
    Route::get('/{bookingId}/payment/vnpay/return', [PaymentController::class, 'vnpayReturn'])->name('payment.vnpay.return');
});

// Role Authentication
Route::middleware('guest')->group(function () {
    Route::inertia('/about', 'Guest/About')->name('about');
    Route::get('/room', [GuestController::class, 'rooms'])->name('room');
});

Route::middleware('auth')->group(function () {
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

        //room type section
        Route::get('/room-type', [RoomTypeController::class, 'index'])
            ->name('room-type-management');
        Route::post('/room-type/add-new', [RoomTypeController::class, 'store']);
        Route::put('/room-type/update/{id}', [RoomTypeController::class, 'update'])->name('room-type.update');
        Route::delete('/room-type/delete/{id}', [RoomTypeController::class, 'destroy'])->name('room-type.delete');

        // room section
        Route::get('/room', [RoomController::class, 'index'])
            ->name('room-management');
        Route::post('/room/add-new', [RoomController::class, 'store']);
        Route::put('/room/update/{id}', [RoomController::class, 'update'])->name('room.update');
        Route::delete('/room/delete/{id}', [RoomController::class, 'destroy'])->name('room.delete');
    });

    Route::middleware('role:manager')->prefix('manager')->name('manager.')->group(function () {
        Route::get('/customer', [CustomerController::class, 'index'])->name('customer');
        Route::post('/customer/add-new', [CustomerController::class, 'store']);
    });

    Route::middleware('role:customer')->group(function () {
        Route::prefix('dashboard')->group(function(){
            Route::get('/{user_name}/profile', function ($user_name) {
                abort_unless(Auth::user()->user_name === $user_name, 403);
                return Inertia::render('User/Profile');
            })->name('user.profile');
            Route::get('/{user_name}/booking-history', [UserController::class, 'bookingHistory'])->name('user.booking-history');
            Route::post('/{user_name}/change-password', [UserController::class, 'changePassword'])->name('user.change_password');
            Route::post('/{user_name}/update-profile', [UserController::class, 'updateProfile'])->name('user.update_profile');
        });
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('user.logout');
});

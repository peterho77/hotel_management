<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\WorkScheduleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

// home page
Route::redirect('/home', '/');
Route::get('/', [GuestController::class, 'index'])->name('home');

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

// rooms page
Route::prefix('rooms')->name('rooms.')->group(function () {
    Route::get('/', [GuestController::class, 'rooms'])->name('index');
    Route::get('/{id}/detail', [GuestController::class, 'roomDetail'])->name('detail');
});

// review page
Route::get('/review', [GuestController::class, 'review'])->name('review');

// Role Authentication
Route::middleware('guest')->group(function () {
    Route::inertia('/about', 'Guest/About')->name('about');
});

Route::middleware('auth')->group(function () {
    // dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {


        //room type section
        Route::prefix('room-type')->name('room-type.')->group(function () {
            Route::get('/', [RoomTypeController::class, 'index'])->name('index');
            Route::post('/create', [RoomTypeController::class, 'store'])->name('store');
            Route::put('/{id}/update', [RoomTypeController::class, 'update'])->name('update');
            Route::delete('/{id}/delete', [RoomTypeController::class, 'destroy'])->name('delete');
        });

        // room section
        Route::prefix('room')->name('room.')->group(function () {
            Route::get('/', [RoomController::class, 'index'])->name('index');
            Route::post('/create', [RoomController::class, 'store'])->name('store');
            Route::put('/{id}/update', [RoomController::class, 'update'])->name('update');
            Route::delete('/{id}/delete', [RoomController::class, 'destroy'])->name('delete');
        });

        // account section
        Route::prefix('account')->name('account.')->group(function () {
            Route::get('/', [UserController::class, 'index'])
                ->name('index');
            Route::post('/account/create', [AdminController::class, 'createAccount'])
                ->name('create');
        });

        // product section
        Route::prefix('product')->name('product.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])
                ->name('index');
        });
    });

    Route::middleware('role:manager')->prefix('manager')->name('manager.')->group(function () {
        Route::get('/customer', [CustomerController::class, 'index'])->name('customer');
        Route::post('/customer/add-new', [CustomerController::class, 'store']);

        Route::get('/employee', [EmployeeController::class, 'index'])->name('employee');

        Route::prefix('work-schedule')->name('work-schedule.')->group(function () {
            Route::get('/', [WorkScheduleController::class, 'index'])->name('index');
            Route::post('/store', [WorkScheduleController::class, 'store'])->name('store');
            Route::put('/{id}/update', [WorkScheduleController::class, 'update'])->name('update');
            Route::delete('/{id}/delete', [WorkScheduleController::class, 'destroy'])->name('delete');
        });
    });

    Route::middleware('role:employee')->group(function () {
        Route::prefix('employee')->name('employee.')->group(function () {
            Route::get('/{user_name}/dashboard', [EmployeeController::class, 'dashboard'])->name('index');
            Route::get('/{user_name}/schedule', [EmployeeController::class, 'schedule'])->name('schedule');
        });
    });

    Route::middleware('role:customer')->group(function () {
        Route::prefix('dashboard')->group(function () {
            Route::get('/{user_name}/profile', function ($user_name) {
                abort_unless(Auth::user()->user_name === $user_name, 403);
                return Inertia::render('User/Profile');
            })->name('user.profile');
            Route::get('/{user_name}/booking-history', [UserController::class, 'bookingHistory'])->name('user.booking-history');
            Route::post('/{user_name}/change-password', [UserController::class, 'changePassword'])->name('user.change_password');
            Route::post('/{user_name}/update-profile', [UserController::class, 'updateProfile'])->name('user.update_profile');
            Route::post('/{user_name}/review/store', [ReviewController::class, 'store'])->name('review.store');
        });
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('user.logout');
});

Route::post('/api/chatbot/chat', [ChatbotController::class, 'chat'])->name('chatbot.chat');
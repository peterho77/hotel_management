<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

Route::inertia('/', 'Guest/Home')->name('home');

// Role Authentication
Route::middleware('guest')->group(function () {
    Route::inertia('/about', 'Guest/About')->name('about');
});
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

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

        Route::get('/partner', [CustomerController::class, 'index'])->name('partners');
    });

    Route::middleware('role:manager')->prefix('manager')->name('manager.')->group(function(){
        Route::get('/partner', [CustomerController::class, 'index'])->name('partners');
    });

    Route::middleware('role:customer')->group(function () {
        Route::get('/{user_name}/dashboard', function ($user_name) {
            abort_unless(Auth::user()->user_name === $user_name, 403);
            return Inertia::render('Guest/Dashboard');
        })->name('user.dashboard');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('user.logout');
});


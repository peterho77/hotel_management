<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RoomController;


Route::inertia('/', 'Guest/Home')->name('home');
Route::inertia('/about', 'Guest/About')->name('about');

// Admin pages
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/room-type', [RoomTypeController::class, 'index'])
        ->name('room-type-management');
    Route::get('/room', [RoomController::class, 'index'])
        ->name('room-management');
    Route::post('/room/add-new', [RoomController::class, 'store']);
});

<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Guest/Home');
});

Route::inertia('/about', 'Guest/About');

// Admin pages
Route::prefix('admin')->name('admin.')->group(function () {
    Route::inertia('/room-management','Admin/Room');
});

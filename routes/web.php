<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/drivers/{driver}', [DriverController::class, 'show'])->name('drivers.show');
Route::post('/drivers/{driver}/reviews', [DriverController::class, 'storeReview'])->name('drivers.reviews.store');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('/dashboard')->name('dashboard.')->middleware('auth', 'admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/taxis', [DashboardController::class, 'taxis'])->name('taxis');
    Route::get('/drivers', [DashboardController::class, 'drivers'])->name('drivers');
    Route::get('/reviews', [DashboardController::class, 'reviews'])->name('reviews');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');

    Route::post('/drivers', [DashboardController::class, 'storeDriver'])->name('drivers.store');
    Route::put('/drivers/{driver}', [DashboardController::class, 'updateDriver'])->name('drivers.update');
    Route::delete('/drivers/{driver}', [DashboardController::class, 'destroyDriver'])->name('drivers.destroy');

    Route::post('/taxis', [DashboardController::class, 'storeTaxi'])->name('taxis.store');
    Route::put('/taxis', [DashboardController::class, 'storeTaxi'])->name('taxis.update');
    Route::delete('/taxis/{driver}', [DashboardController::class, 'destroyTaxi'])->name('taxis.destroy');

    Route::put('/reviews/{review}', [DashboardController::class, 'toggleReviewVisibility'])->name('reviews.toggle');
    Route::delete('/reviews/{review}', [DashboardController::class, 'destroyReview'])->name('reviews.destroy');

    Route::post('/settings', [DashboardController::class, 'updateSettings'])->name('settings');
    Route::post('/profile', [DashboardController::class, 'updateProfile'])->name('profile');
});

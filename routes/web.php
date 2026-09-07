<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public site - English (default, no URL prefix)
|--------------------------------------------------------------------------
*/

Route::middleware('locale')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/drivers/{driver}', [DriverController::class, 'show'])->name('drivers.show');
    Route::post('/drivers/{driver}/reviews', [DriverController::class, 'storeReview'])->name('drivers.reviews.store');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Public site - French (/fr prefix)
|--------------------------------------------------------------------------
*/

Route::prefix('/fr')->middleware('locale')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home.fr');
    Route::get('/drivers/{driver}', [DriverController::class, 'show'])->name('drivers.show.fr');
    Route::post('/drivers/{driver}/reviews', [DriverController::class, 'storeReview'])->name('drivers.reviews.store.fr');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login.fr');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout.fr');
});

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::prefix('/dashboard')->name('dashboard.')->middleware('auth', 'admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/taxis', [DashboardController::class, 'taxis'])->name('taxis');
    Route::get('/taxis/create', [DashboardController::class, 'createTaxi'])->name('taxis.create');
    Route::get('/taxis/{driver}/edit', [DashboardController::class, 'editTaxi'])->name('taxis.edit');
    Route::get('/drivers', [DashboardController::class, 'drivers'])->name('drivers');
    Route::get('/drivers/create', [DashboardController::class, 'createDriver'])->name('drivers.create');
    Route::get('/drivers/{driver}/edit', [DashboardController::class, 'editDriver'])->name('drivers.edit');
    Route::get('/reviews', [DashboardController::class, 'reviews'])->name('reviews');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');

    Route::post('/drivers', [DashboardController::class, 'storeDriver'])->name('drivers.store');
    Route::put('/drivers/{driver}', [DashboardController::class, 'updateDriver'])->name('drivers.update');
    Route::delete('/drivers/{driver}', [DashboardController::class, 'destroyDriver'])->name('drivers.destroy');

    Route::post('/taxis', [DashboardController::class, 'storeTaxi'])->name('taxis.store');
    Route::put('/taxis', [DashboardController::class, 'storeTaxi'])->name('taxis.update');
    Route::put('/taxis/{taxi}/availability', [DashboardController::class, 'toggleTaxiAvailability'])->name('taxis.availability');
    Route::delete('/taxis/{driver}', [DashboardController::class, 'destroyTaxi'])->name('taxis.destroy');

    Route::put('/reviews/{review}', [DashboardController::class, 'toggleReviewVisibility'])->name('reviews.toggle');
    Route::delete('/reviews/{review}', [DashboardController::class, 'destroyReview'])->name('reviews.destroy');

    Route::post('/uploads/avatar', [DashboardController::class, 'uploadAvatar'])->name('uploads.avatar');
    Route::post('/uploads/taxi-image', [DashboardController::class, 'uploadTaxiImage'])->name('uploads.taxi-image');
    Route::post('/uploads/taxi-gallery', [DashboardController::class, 'uploadTaxiGallery'])->name('uploads.taxi-gallery');

    Route::post('/settings', [DashboardController::class, 'updateSettings'])->name('settings');
    Route::post('/profile', [DashboardController::class, 'updateProfile'])->name('profile');
});

<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\Customer;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// ---------------------------------------------------------------- Publik
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/katalog/{equipment}', [CatalogController::class, 'show'])->name('catalog.show');

// ---------------------------------------------------------------- Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.store');
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register'])->middleware('throttle:10,1')->name('register.store');
});

Route::post('/keluar', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ---------------------------------------------------------------- Customer
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::resource('penyewaan', Customer\RentalController::class)
        ->parameters(['penyewaan' => 'rental'])
        ->names('customer.rentals');
});

// ---------------------------------------------------------------- Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('kategori', Admin\CategoryController::class)
        ->except('show')
        ->parameters(['kategori' => 'category'])
        ->names('categories');

    Route::resource('alat', Admin\EquipmentController::class)
        ->except('show')
        ->parameters(['alat' => 'equipment'])
        ->names('equipment');

    Route::get('penyewaan', [Admin\RentalController::class, 'index'])->name('rentals.index');
    Route::get('penyewaan/{rental}', [Admin\RentalController::class, 'show'])->name('rentals.show');
    Route::patch('penyewaan/{rental}/status', [Admin\RentalController::class, 'updateStatus'])->name('rentals.status');
    Route::delete('penyewaan/{rental}', [Admin\RentalController::class, 'destroy'])->name('rentals.destroy');
});

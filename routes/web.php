<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KostController;
use App\Http\Controllers\OwnerBookingController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\OwnerKostController;
use App\Http\Controllers\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/kosts/{kost}', [KostController::class, 'show'])->name('kosts.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/kosts/{kost}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/kosts/{kost}/booking', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/kosts/{kost}/booking', [BookingController::class, 'store'])->name('bookings.store');
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');
});

Route::prefix('owner')->name('owner.')->middleware(['auth', 'owner'])->group(function () {
    Route::get('/', [OwnerDashboardController::class, 'index'])->name('dashboard');
    Route::redirect('/dashboard', '/owner')->name('dashboard.redirect');
    Route::get('/kosts', [OwnerKostController::class, 'index'])->name('kosts.index');
    Route::get('/kosts/create', [OwnerKostController::class, 'create'])->name('kosts.create');
    Route::post('/kosts', [OwnerKostController::class, 'store'])->name('kosts.store');
    Route::get('/kosts/{kost}/edit', [OwnerKostController::class, 'edit'])->name('kosts.edit');
    Route::put('/kosts/{kost}', [OwnerKostController::class, 'update'])->name('kosts.update');
    Route::delete('/kosts/{kost}', [OwnerKostController::class, 'destroy'])->name('kosts.destroy');
    Route::get('/bookings', [OwnerBookingController::class, 'index'])->name('bookings.index');
    Route::get('/riwayat', [OwnerBookingController::class, 'history'])->name('history');
    Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.status');
    Route::patch('/bookings/{booking}/payment-status', [OwnerBookingController::class, 'updatePaymentStatus'])->name('bookings.payment-status');
    Route::get('/bookings/pembayaran', [OwnerBookingController::class, 'payments'])->name('bookings.payments');
    Route::get('/bookings/{booking}/review', [OwnerBookingController::class, 'review'])->name('bookings.review');
});

// Payment gateway webhook (provider will call this URL)
Route::post('/webhooks/payment', [PaymentWebhookController::class, 'handle'])->name('webhooks.payment');

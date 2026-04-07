<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WishlistController;

// ==================== PUBLIC ROUTES ====================

// Public Routes
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/', [ProductController::class, 'homeProducts'])->name('home');

// Product Routes (Public)
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('products.show');
});

// ==================== AUTHENTICATION ROUTES ====================
Route::middleware('guest')->group(function () {
    // Registration
    Route::controller(RegisteredUserController::class)->group(function () {
        Route::get('register', 'create')->name('register');
        Route::post('register', 'store');
    });

    // Login
    Route::controller(AuthenticatedSessionController::class)->group(function () {
        Route::get('login', 'create')->name('login');
        Route::post('login', 'store');
    });

    // Password Reset
    Route::controller(PasswordResetLinkController::class)->group(function () {
        Route::get('forgot-password', 'create')->name('password.request');
        Route::post('forgot-password', 'store')->name('password.email');
    });

    Route::controller(NewPasswordController::class)->group(function () {
        Route::get('reset-password/{token}', 'create')->name('password.reset');
        Route::post('reset-password', 'store')->name('password.store');
    });
});

// ==================== PROTECTED ROUTES ====================
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Profile Management
    Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::put('/', 'update')->name('update');
        Route::put('/password', 'updatePassword')->name('password.update'); // Add this line
        Route::delete('/', 'destroy')->name('destroy');
    });

    // Shopping Cart
    Route::prefix('cart')->name('cart.')->controller(CartController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/checkout', 'checkout')->name('checkout');

        Route::post('/add', 'add')->name('add');
        Route::put('/update/{id}', 'update')->name('update');
        Route::delete('/remove/{id}', 'remove')->name('remove');
        Route::delete('/clear', 'clear')->name('clear');
        Route::get('/count', 'getCount')->name('count');
    });

    // Wishlist
    Route::prefix('wishlist')->name('wishlist.')->controller(WishlistController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/add', 'add')->name('add');
        Route::delete('/remove/{id}', 'remove')->name('remove');
        Route::post('/toggle', 'toggle')->name('toggle');
        Route::get('/check/{productId}', 'check')->name('check');
    });
    // Order Management
    Route::prefix('orders')->name('orders.')->controller(OrderController::class)->group(function () {

        Route::post('/place', 'placeOrder')->name('place');
        Route::get('/place/{productId}/{variantId?}', 'directOrderForm')->name('place');
        Route::get('/success/{order}', 'success')->name('success');
        Route::get('/my-orders', 'myOrders')->name('my');
        Route::get('/{order}', 'show')->name('show');
        Route::put('/{order}/cancel', 'cancel')->name('cancel');
    });
});

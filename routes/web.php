<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// User has to be guest or have verified email address here:
Route::middleware(['guestOrVerified'])->group(function(){
    Route::get('/', [App\Http\Controllers\ProductController::class, 'index'])->name('home');
    Route::get('/product/{product:slug}', [App\Http\Controllers\ProductController::class, 'view'])->name('product.view');

    Route::prefix('/cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add/{product:slug}', [CartController::class, 'add'])->name('add');
        Route::post('/remove/{product:slug}', [CartController::class, 'remove'])->name('remove');
        Route::post('/update-quantity/{product:slug}', [CartController::class, 'updateQuantity'])->name('update-quantity');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // TODO remove dashboard, it's not needed - really??? I don't think so.:
    Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');
    Route::get('/details', [CustomerController::class, 'view'])->name('details');
    Route::post('/details', [CustomerController::class, 'store'])->name('details.update');
    Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/{order}', [CheckoutController::class, 'checkoutOrder'])->name('checkout-order');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/failure', [CheckoutController::class, 'failure'])->name('checkout.failure');
    Route::get('/orders', [OrderController::class, 'index'])->name('order.index');
    Route::get('/orders/view/:order', [OrderController::class, 'view'])->name('order.view');
});

require __DIR__.'/auth.php';

<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/products', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/api/products', [App\Http\Controllers\ProductController::class, 'getProducts'])->name('products.get');
Route::post('/api/cart/add', [App\Http\Controllers\ProductController::class, 'addToCart'])->name('cart.add');
Route::post('/api/cart/remove', [App\Http\Controllers\ProductController::class, 'removeFromCart'])->name('cart.remove');
Route::get('/api/cart', [App\Http\Controllers\ProductController::class, 'getCart'])->name('cart.get');

// Order routes (customer)
Route::post('/api/orders/submit', [App\Http\Controllers\OrderController::class, 'submit'])->name('orders.submit');
Route::get('/api/orders/{orderNumber}/status', [App\Http\Controllers\OrderController::class, 'getStatus'])->name('orders.status');

// Cashier routes
Route::get('/cashier', [App\Http\Controllers\CashierController::class, 'dashboard'])->name('cashier.dashboard');
Route::get('/api/cashier/orders', [App\Http\Controllers\CashierController::class, 'getPendingOrders'])->name('cashier.orders');
Route::post('/api/cashier/orders/{id}/approve', [App\Http\Controllers\CashierController::class, 'approveOrder'])->name('cashier.approve');
Route::post('/api/cashier/orders/{id}/reject', [App\Http\Controllers\CashierController::class, 'rejectOrder'])->name('cashier.reject');
Route::post('/api/cashier/orders/{id}/ready', [App\Http\Controllers\CashierController::class, 'markReady'])->name('cashier.ready');

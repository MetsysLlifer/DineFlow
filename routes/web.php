<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminUserController;

Route::get('/', function () {
    return view('welcome');
});

// Customer menu page
Route::get('/menu', [ProductController::class, 'index'])->name('menu.index');

// Admin / Staff Auth + Protected Area
Route::prefix('admin')->group(function () {
    // Auth only
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Protected staff area (allowed roles)
    Route::middleware(['role:admin,manager,cashier,kitchen,host,analyst'])->group(function () {
        Route::get('/', [AdminDashboardController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/logs', [AdminDashboardController::class, 'logs'])->name('admin.logs');
        Route::get('/profit', [AdminDashboardController::class, 'profit'])->name('admin.profit');
        Route::get('/profit/data', [AdminDashboardController::class, 'profitData'])->name('admin.profit.data');
        Route::get('/roles', [AdminDashboardController::class, 'roles'])->middleware('role:admin,manager')->name('admin.roles');
        Route::put('/roles/{user}', [AdminDashboardController::class, 'updateRole'])->middleware('role:admin,manager')->name('admin.roles.update');
    });
});

Route::get('/api/products', [ProductController::class, 'getProducts'])->name('products.get');
Route::post('/api/cart/add', [ProductController::class, 'addToCart'])->name('cart.add');
Route::post('/api/cart/remove', [ProductController::class, 'removeFromCart'])->name('cart.remove');
Route::post('/api/cart/update', [ProductController::class, 'updateCart'])->name('cart.update');
Route::get('/api/cart', [ProductController::class, 'getCart'])->name('cart.get');

// Order routes (customer)
Route::post('/api/orders/submit', [App\Http\Controllers\OrderController::class, 'submit'])->name('orders.submit');
Route::get('/api/orders/{orderNumber}/status', [App\Http\Controllers\OrderController::class, 'getStatus'])->name('orders.status');

// Cashier / kitchen protected routes
Route::middleware(['role:admin,manager,cashier,kitchen'])->group(function () {
    Route::get('/cashier', [App\Http\Controllers\CashierController::class, 'dashboard'])->name('cashier.dashboard');
    Route::get('/api/cashier/orders', [App\Http\Controllers\CashierController::class, 'getPendingOrders'])->name('cashier.orders');
    Route::get('/api/cashier/orders-unapproved', [App\Http\Controllers\CashierController::class, 'getUnapprovedOrders'])->name('cashier.orders.unapproved');
    Route::post('/api/cashier/approve-by-code', [App\Http\Controllers\CashierController::class, 'approveByCode'])->name('cashier.approve.code');
    Route::post('/api/cashier/orders/{id}/approve', [App\Http\Controllers\CashierController::class, 'approveOrder'])->name('cashier.approve');
    Route::post('/api/cashier/orders/{id}/reject', [App\Http\Controllers\CashierController::class, 'rejectOrder'])->name('cashier.reject');
    Route::post('/api/cashier/orders/{id}/ready', [App\Http\Controllers\CashierController::class, 'markReady'])->name('cashier.ready');
});

// Admin menu item editor (protected)
Route::prefix('admin')->middleware(['role:admin,manager'])->group(function () {
    Route::get('/menu-items', [ProductController::class, 'adminIndex'])->name('admin.menu-items.index');
    Route::get('/menu-items/create', [ProductController::class, 'create'])->name('admin.menu-items.create');
    Route::post('/menu-items', [ProductController::class, 'store'])->name('admin.menu-items.store');
    Route::get('/menu-items/{product}/edit', [ProductController::class, 'edit'])->name('admin.menu-items.edit');
    Route::put('/menu-items/{product}', [ProductController::class, 'update'])->name('admin.menu-items.update');
    Route::delete('/menu-items/{product}', [ProductController::class, 'destroy'])->name('admin.menu-items.destroy');
});

// Admin user management (admin only)
Route::prefix('admin')->middleware(['role:admin'])->group(function () {
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
});

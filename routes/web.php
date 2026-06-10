<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\ProfileController;

// ── Public ────────────────────────────────────────────────────

Route::get('/', [ProductController::class, 'index'])->name('menu');

// Cart read — public so JS can fetch on page load for any user state
Route::get('/cart', [ProductController::class, 'getCart'])->name('cart.get');

// ── Auth: Cart (write) ────────────────────────────────────────

Route::middleware('auth')->prefix('cart')->name('cart.')->group(function () {
    Route::post('/add',    [ProductController::class, 'addToCart'])->name('add');
    Route::post('/update', [ProductController::class, 'updateCart'])->name('update');
    Route::post('/clear',  [ProductController::class, 'clearCart'])->name('clear');
});

// ── Auth: User area ───────────────────────────────────────────

Route::middleware('auth')->group(function () {

    // Profile (Breeze)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',    [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/',  [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Checkout flow
    Route::get('/checkout',  [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [OrderController::class, 'processCheckout'])->name('checkout.process');

    // Payment
    Route::get('/payment',     [OrderController::class, 'payment'])->name('payment');
    Route::post('/payment',    [OrderController::class, 'store'])->name('payment.auto');

    // Order detail / waiting
    Route::get('/waiting/{order}', [OrderController::class, 'waiting'])->name('order.waiting');

    // Upload payment proof
    Route::post('/orders/{order}/upload-payment', [OrderController::class, 'uploadPayment'])
         ->name('order.uploadPayment');

    // My orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
});

// ── Admin ─────────────────────────────────────────────────────

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Rekapitulasi
    Route::prefix('rekapitulasi')->name('rekapitulasi.')->group(function () {
        Route::get('/',      [AdminReportController::class, 'index'])->name('index');
        Route::get('/pdf',   [AdminReportController::class, 'exportPdf'])->name('pdf');
        Route::get('/excel', [AdminReportController::class, 'exportExcel'])->name('excel');
    });

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/',                       [OrderController::class, 'adminOrders'])->name('index');
        Route::get('/{id}',                   [OrderController::class, 'show'])->name('show');
        Route::put('/{id}/status',            [OrderController::class, 'updateStatus'])->name('updateStatus');
        Route::post('/{id}/approve-payment',  [OrderController::class, 'approvePayment'])->name('approvePayment');
        Route::post('/{id}/reject-payment',   [OrderController::class, 'rejectPayment'])->name('rejectPayment');
    });

    // Products
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/',             [ProductController::class, 'adminIndex'])->name('index');
        Route::post('/',            [ProductController::class, 'store'])->name('store');
        Route::put('/{id}',         [ProductController::class, 'update'])->name('update');
        Route::post('/{id}/stock',  [ProductController::class, 'addStock'])->name('addStock');
        Route::post('/{id}/toggle', [ProductController::class, 'toggle'])->name('toggle');
        Route::delete('/{id}',      [ProductController::class, 'destroy'])->name('delete');
    });
});

require __DIR__ . '/auth.php';

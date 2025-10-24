<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrdersController as AdminOrdersController;
use App\Http\Controllers\Admin\ProductsController as AdminProductsController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\CheckoutController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Front routes
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/items', [CartController::class, 'addItem'])->name('cart.add');
Route::patch('/cart/items', [CartController::class, 'updateItem'])->name('cart.update');
Route::delete('/cart/items', [CartController::class, 'removeItem'])->name('cart.remove');

Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.place');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

// Account routes
Route::middleware('auth')->group(function () {
    Route::get('/account', function () {
        return view('account.dashboard');
    })->name('account.dashboard');
    
    Route::get('/account/profile', function () {
        return view('account.profile');
    })->name('account.profile');
    
    Route::get('/account/orders', function () {
        return view('account.orders');
    })->name('account.orders');
    
    Route::get('/account/orders/{order}', function ($order) {
        return view('account.order-detail', compact('order'));
    })->name('account.order-detail');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::prefix('admin')->middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    Route::resource('products', AdminProductsController::class);
    Route::patch('products/{product}/toggle-active', [AdminProductsController::class, 'toggleActive'])->name('admin.products.toggle-active');
    Route::delete('products/images/{image}', [AdminProductsController::class, 'deleteImage'])->name('admin.products.images.delete');
    Route::patch('products/images/{image}/primary', [AdminProductsController::class, 'setPrimaryImage'])->name('admin.products.images.primary');
    
    Route::get('orders', [AdminOrdersController::class, 'index'])->name('admin.orders.index');
    Route::get('orders/{order}', [AdminOrdersController::class, 'show'])->name('admin.orders.show');
    Route::patch('orders/{order}/status', [AdminOrdersController::class, 'updateStatus'])->name('admin.orders.update-status');
    Route::get('orders/{order}/print', [AdminOrdersController::class, 'print'])->name('admin.orders.print');
    
    Route::get('settings', [AdminSettingsController::class, 'index'])->name('admin.settings.index');
    Route::post('settings', [AdminSettingsController::class, 'update'])->name('admin.settings.update');
    Route::post('settings/test-zalo', [AdminSettingsController::class, 'testZalo'])->name('admin.settings.test-zalo');
    Route::post('settings/test-messenger', [AdminSettingsController::class, 'testMessenger'])->name('admin.settings.test-messenger');
});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StatsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API routes
Route::get('/search/products', [ProductController::class, 'search'])->name('api.products.search');

// Admin API routes
Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin,staff'])->group(function () {
    Route::get('/stats/revenue', [StatsController::class, 'revenue'])->name('api.stats.revenue');
    Route::get('/stats/top-products', [StatsController::class, 'topProducts'])->name('api.stats.top-products');
});

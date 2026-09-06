<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\SearchLogController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CouponController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\InquiryController;
use App\Http\Controllers\Api\V1\WishlistController;
use App\Http\Controllers\Api\V1\Admin\ProductManageController;
use App\Http\Controllers\Api\V1\Admin\InventoryController;
use App\Http\Controllers\Api\V1\Admin\SalesController;

Route::prefix('v1')->group(function () {
    // 公開 API
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);

    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::get('/products/{id}/reviews', [ReviewController::class, 'index']);
    Route::post('/search/trends', [SearchLogController::class, 'store']);
    Route::post('/inquiries', [InquiryController::class, 'store']);

    // 認証必須 API
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::post('/products/{id}/reviews', [ReviewController::class, 'store']);

        // カート API
        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart/items', [CartController::class, 'add']);
        Route::delete('/cart/items/{id}', [CartController::class, 'remove']);

        // クーポン API
        Route::post('/coupons/verify', [CouponController::class, 'verify']);

        // 注文・鑑定書 API
        Route::post('/orders/checkout', [OrderController::class, 'checkout']);
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::get('/orders/{id}/certificate', [OrderController::class, 'getCertificate']);

        // お気に入り API
        Route::get('/wishlist', [WishlistController::class, 'index']);
        Route::post('/wishlist/toggle', [WishlistController::class, 'toggle']);

        // 管理者 API
        Route::prefix('admin')->group(function () {
            Route::get('/products', [ProductManageController::class, 'index']);
            Route::post('/products', [ProductManageController::class, 'store']);
            Route::put('/products/{id}', [ProductManageController::class, 'update']);
            Route::delete('/products/{id}', [ProductManageController::class, 'destroy']);

            Route::get('/inventory', [InventoryController::class, 'index']);
            Route::patch('/inventory/{id}', [InventoryController::class, 'updateStock']);

            Route::get('/sales/dashboard', [SalesController::class, 'dashboard']);
        });
    });
});
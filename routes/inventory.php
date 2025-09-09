<?php

use Illuminate\Support\Facades\Route;
use Src\InventoryManagement\Infrastructure\Http\Controllers\ProductController;
use App\Http\Controllers\API\CategoryController as ApiCategoryController;
use App\Http\Controllers\API\BrandController as ApiBrandController;
use Src\InventoryManagement\Infrastructure\Http\Controllers\WarehouseController;
use Src\InventoryManagement\Infrastructure\Http\Controllers\StockController;

Route::prefix('inventory')->group(function () {
    // Products Routes
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/stats', [ProductController::class, 'stats']);
        Route::get('/{product}', [ProductController::class, 'show']);
        Route::post('/', [ProductController::class, 'store']);
        Route::put('/{product}', [ProductController::class, 'update']);
        Route::delete('/{product}', [ProductController::class, 'destroy']);
        Route::get('/{product}/stock', [ProductController::class, 'stockHistory']);
    });

    // Categories Routes
    Route::prefix('categories')->group(function () {
        Route::get('/', [ApiCategoryController::class, 'index']);
        Route::get('/roots', [ApiCategoryController::class, 'rootCategories']);
        Route::get('/active', [ApiCategoryController::class, 'active']);
        Route::get('/with-product-count', [ApiCategoryController::class, 'withProductCount']);
        Route::get('/{category}', [ApiCategoryController::class, 'show']);
        Route::post('/', [ApiCategoryController::class, 'store']);
        Route::put('/{category}', [ApiCategoryController::class, 'update']);
        Route::delete('/{category}', [ApiCategoryController::class, 'destroy']);
    });

    // Brands Routes
    Route::prefix('brands')->group(function () {
        Route::get('/', [ApiBrandController::class, 'index']);
        Route::get('/active', [ApiBrandController::class, 'active']);
        Route::get('/with-contact-info', [ApiBrandController::class, 'withContactInfo']);
        Route::get('/search', [ApiBrandController::class, 'search']);
        Route::get('/{brand}', [ApiBrandController::class, 'show']);
        Route::post('/', [ApiBrandController::class, 'store']);
        Route::put('/{brand}', [ApiBrandController::class, 'update']);
        Route::delete('/{brand}', [ApiBrandController::class, 'destroy']);
    });

    // Warehouses Routes - Comentado temporalmente
    // Route::prefix('warehouses')->group(function () {
    //     Route::get('/', [WarehouseController::class, 'index']);
    //     Route::get('/{warehouse}', [WarehouseController::class, 'show']);
    //     Route::post('/', [WarehouseController::class, 'store']);
    //     Route::put('/{warehouse}', [WarehouseController::class, 'update']);
    //     Route::delete('/{warehouse}', [WarehouseController::class, 'destroy']);
    //     Route::get('/{warehouse}/stock', [WarehouseController::class, 'stock']);
    // });

    // Stock Movements Routes - Comentado temporalmente
    // Route::prefix('stock')->group(function () {
    //     Route::get('/', [StockController::class, 'index']);
    //     Route::get('/movements', [StockController::class, 'movements']);
    //     Route::post('/adjust', [StockController::class, 'adjust']);
    //     Route::post('/transfer', [StockController::class, 'transfer']);
    //     Route::get('/low-stock', [StockController::class, 'lowStock']);
    //     Route::get('/out-of-stock', [StockController::class, 'outOfStock']);
    // });

    // Inventory Dashboard
    Route::get('/dashboard', [ProductController::class, 'dashboard']);
});
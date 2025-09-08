<?php

use Illuminate\Support\Facades\Route;
use Src\InventoryManagement\Infrastructure\Http\Controllers\ProductController;
use Src\InventoryManagement\Infrastructure\Http\Controllers\CategoryController;
use Src\InventoryManagement\Infrastructure\Http\Controllers\BrandController;
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
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/{category}', [CategoryController::class, 'show']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{category}', [CategoryController::class, 'update']);
        Route::delete('/{category}', [CategoryController::class, 'destroy']);
        Route::get('/{category}/products', [CategoryController::class, 'products']);
    });

    // Brands Routes
    Route::prefix('brands')->group(function () {
        Route::get('/', [BrandController::class, 'index']);
        Route::get('/{brand}', [BrandController::class, 'show']);
        Route::post('/', [BrandController::class, 'store']);
        Route::put('/{brand}', [BrandController::class, 'update']);
        Route::delete('/{brand}', [BrandController::class, 'destroy']);
        Route::get('/{brand}/products', [BrandController::class, 'products']);
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
<?php

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

use App\Http\Controllers\CustomerWebController;
use App\Http\Controllers\InventoryWebController;
use App\Http\Controllers\Inventory\CategoryController;
use App\Http\Controllers\Inventory\BrandController;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/welcome', function () {
    return view('welcome');
});

// Customer Management Routes
Route::prefix('customers')->group(function () {
    Route::get('/', [CustomerWebController::class, 'index'])->name('customers.index');
    Route::get('/create', [CustomerWebController::class, 'create'])->name('customers.create');
    Route::get('/{customer}', [CustomerWebController::class, 'show'])->name('customers.show');
    Route::get('/{customer}/edit', [CustomerWebController::class, 'edit'])->name('customers.edit');
});

// Inventory Management Routes
Route::prefix('inventory')->group(function () {
    // Dashboard
    Route::get('/dashboard', [InventoryWebController::class, 'dashboard'])->name('inventory.dashboard');
    
    // Products
    Route::prefix('products')->group(function () {
        Route::get('/', [InventoryWebController::class, 'productsIndex'])->name('inventory.products.index');
        Route::get('/create', [InventoryWebController::class, 'productsCreate'])->name('inventory.products.create');
        Route::get('/{product}', [InventoryWebController::class, 'productsShow'])->name('inventory.products.show');
        Route::get('/{product}/edit', [InventoryWebController::class, 'productsEdit'])->name('inventory.products.edit');
    });
    
    // Categories
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('inventory.categories.index');
        Route::get('/create', [CategoryController::class, 'create'])->name('inventory.categories.create');
        Route::get('/{category}', [CategoryController::class, 'show'])->name('inventory.categories.show');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('inventory.categories.edit');
    });
    
    // Brands
    Route::prefix('brands')->group(function () {
        Route::get('/', [BrandController::class, 'index'])->name('inventory.brands.index');
        Route::get('/create', [BrandController::class, 'create'])->name('inventory.brands.create');
        Route::get('/{brand}', [BrandController::class, 'show'])->name('inventory.brands.show');
        Route::get('/{brand}/edit', [BrandController::class, 'edit'])->name('inventory.brands.edit');
    });
    
    // Warehouses
    Route::prefix('warehouses')->group(function () {
        Route::get('/', [InventoryWebController::class, 'warehousesIndex'])->name('inventory.warehouses.index');
        Route::get('/create', [InventoryWebController::class, 'warehousesCreate'])->name('inventory.warehouses.create');
        Route::get('/{warehouse}/edit', [InventoryWebController::class, 'warehousesEdit'])->name('inventory.warehouses.edit');
    });
    
    // Movements
    Route::prefix('movements')->group(function () {
        Route::get('/', [InventoryWebController::class, 'movementsIndex'])->name('inventory.movements.index');
        Route::get('/create', [InventoryWebController::class, 'movementsCreate'])->name('inventory.movements.create');
    });
    
    // Alerts
    Route::get('/alerts', [InventoryWebController::class, 'alertsIndex'])->name('inventory.alerts.index');
});


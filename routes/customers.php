<?php

use Illuminate\Support\Facades\Route;
use Src\CustomerManagement\Infrastructure\Http\Controllers\CustomerController;

Route::prefix('api/customers')->group(function () {
    // Get all customers
    Route::get('/', [CustomerController::class, 'index']);
    
    // Create a new customer
    Route::post('/', [CustomerController::class, 'store']);
    
    // Search customers by name
    Route::get('/search', [CustomerController::class, 'search']);
    
    // Customer resource routes
    Route::prefix('{customer}')->group(function () {
        // Get specific customer
        Route::get('/', [CustomerController::class, 'show']);
        
        // Update customer
        Route::put('/', [CustomerController::class, 'update']);
        
        // Delete customer
        Route::delete('/', [CustomerController::class, 'destroy']);
    });
});
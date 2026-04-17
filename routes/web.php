<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::resources([
    'categories' => CategoryController::class,
    'products' => ProductController::class,
    'customers' => CustomerController::class,
    'sales' => SaleController::class,
]);

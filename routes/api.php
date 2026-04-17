<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;
use Illuminate\Support\Facades\Route;

Route::apiResources([
    'categories' => CategoryController::class,
    'products' => ProductController::class,
    'customers' => CustomerController::class,
    'sales' => SaleController::class,
]);

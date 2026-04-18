<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;


// // Redirect root ke login
// Route::get('/test', function () {
//     return 'TEST OK';
// });
// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::resources([
        'categories' => CategoryController::class,
        'products'   => ProductController::class,
        'customers'  => CustomerController::class,
        'sales'      => SaleController::class,
    ]);

    // Reports
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/top-products', [ReportController::class, 'topProducts'])->name('reports.top-products');

    // Exports
    Route::get('/reports/sales/export/excel', [ReportController::class, 'exportSalesExcel'])->name('reports.sales.export.excel');
    Route::get('/reports/sales/export/pdf', [ReportController::class, 'exportSalesPdf'])->name('reports.sales.export.pdf');
    Route::get('/reports/top-products/export/excel', [ReportController::class, 'exportTopProductsExcel'])->name('reports.top-products.export.excel');
    Route::get('/reports/top-products/export/pdf', [ReportController::class, 'exportTopProductsPdf'])->name('reports.top-products.export.pdf');
});

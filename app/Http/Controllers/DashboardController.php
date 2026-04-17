<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        if (! Schema::hasTable('sales')) {
            return view('dashboard', [
                'totalProducts' => 0,
                'lowStockProducts' => 0,
                'totalCustomers' => 0,
                'totalCategories' => 0,
                'todaySales' => 0,
                'latestSales' => collect(),
            ]);
        }

        $todaySales = Sale::query()
            ->whereDate('sale_date', now()->toDateString())
            ->sum('grand_total');

        $data = [
            'totalProducts' => Product::query()->count(),
            'lowStockProducts' => Product::query()->where('stock', '<=', 5)->count(),
            'totalCustomers' => Customer::query()->count(),
            'totalCategories' => Category::query()->count(),
            'todaySales' => $todaySales,
            'latestSales' => Sale::query()->with(['customer', 'user'])->latest('sale_date')->limit(8)->get(),
        ];

        return view('dashboard', $data);
    }
}

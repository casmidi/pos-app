<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|Response
    {
        if (! Schema::hasTable('sales')) {
            $fallback = [
                'totalProducts' => 0,
                'lowStockProducts' => 0,
                'totalCustomers' => 0,
                'totalCategories' => 0,
                'todaySales' => 0,
                'latestSales' => collect(),
                'sort' => 'sale_date',
                'direction' => 'desc',
            ];

            return view('dashboard', $fallback);
        }

        $allowedSorts = [
            'invoice_no' => 'invoice_no',
            'sale_date'  => 'sale_date',
            'customer'   => 'customer',
            'cashier'    => 'cashier',
            'grand_total' => 'grand_total',
        ];

        $sort      = $request->string('sort')->toString();
        $direction = strtolower($request->string('direction')->toString()) === 'asc' ? 'asc' : 'desc';

        if (! array_key_exists($sort, $allowedSorts)) {
            $sort      = 'sale_date';
            $direction = 'desc';
        }

        $query = Sale::query()->with(['customer', 'user']);

        if ($sort === 'customer') {
            $query->leftJoin('customers as dash_customers', 'dash_customers.id', '=', 'sales.customer_id')
                ->select('sales.*')
                ->orderBy('dash_customers.name', $direction)
                ->orderBy('sales.id', 'desc');
        } elseif ($sort === 'cashier') {
            $query->leftJoin('users as dash_users', 'dash_users.id', '=', 'sales.user_id')
                ->select('sales.*')
                ->orderBy('dash_users.name', $direction)
                ->orderBy('sales.id', 'desc');
        } else {
            $query->orderBy('sales.' . $sort, $direction)
                ->orderBy('sales.id', 'desc');
        }

        $latestSales = $query->limit(10)->get();

        $todaySales = Sale::query()
            ->whereDate('sale_date', now()->toDateString())
            ->sum('grand_total');

        $gridData = compact('latestSales', 'sort', 'direction');

        if ($this->isGridAsyncRequest($request)) {
            return response()->view('dashboard.partials.grid', $gridData);
        }

        $data = array_merge($gridData, [
            'totalProducts'   => Product::query()->count(),
            'lowStockProducts' => Product::query()->where('stock', '<=', 5)->count(),
            'totalCustomers'  => Customer::query()->count(),
            'totalCategories' => Category::query()->count(),
            'todaySales'      => $todaySales,
        ]);

        return view('dashboard', $data);
    }
}

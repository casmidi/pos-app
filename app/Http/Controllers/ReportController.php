<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function sales(Request $request): View
    {
        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to'   => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $dateFrom = $request->date('date_from') ?? now()->startOfMonth();
        $dateTo   = $request->date('date_to')   ?? now()->endOfDay();

        $sales = Sale::query()
            ->with(['customer', 'user'])
            ->whereBetween('sale_date', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->latest('sale_date')
            ->get();

        $summary = [
            'total_transactions' => $sales->count(),
            'subtotal'           => $sales->sum('subtotal'),
            'discount_total'     => $sales->sum('discount_total'),
            'tax_total'          => $sales->sum('tax_total'),
            'grand_total'        => $sales->sum('grand_total'),
        ];

        return view('reports.sales', compact('sales', 'summary', 'dateFrom', 'dateTo'));
    }
}

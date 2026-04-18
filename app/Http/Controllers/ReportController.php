<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Exports\TopProductsReportExport;
use App\Models\Sale;
use App\Models\SaleItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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

    public function topProducts(Request $request): View
    {
        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to'   => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $dateFrom = $request->date('date_from') ?? now()->startOfMonth();
        $dateTo   = $request->date('date_to') ?? now()->endOfDay();

        $topProducts = SaleItem::query()
            ->selectRaw('product_id, SUM(qty) as total_qty, SUM(line_total) as total_sales, COUNT(DISTINCT sale_id) as transaction_count')
            ->with('product')
            ->whereHas('sale', function ($query) use ($dateFrom, $dateTo): void {
                $query->whereBetween('sale_date', [$dateFrom->startOfDay(), $dateTo->endOfDay()]);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->orderByDesc('total_sales')
            ->get();

        return view('reports.top-products', compact('topProducts', 'dateFrom', 'dateTo'));
    }

    public function exportSalesExcel(Request $request): BinaryFileResponse
    {
        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to'   => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $dateFrom = $request->date('date_from') ?? now()->startOfMonth();
        $dateTo   = $request->date('date_to') ?? now()->endOfDay();

        $filename = 'laporan-penjualan-' . $dateFrom->format('Ymd') . '-' . $dateTo->format('Ymd') . '.xlsx';

        return Excel::download(new SalesReportExport($dateFrom, $dateTo), $filename);
    }

    public function exportSalesPdf(Request $request): Response
    {
        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to'   => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $dateFrom = $request->date('date_from') ?? now()->startOfMonth();
        $dateTo   = $request->date('date_to') ?? now()->endOfDay();

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

        $pdf = Pdf::loadView('reports.pdf.sales', compact('sales', 'summary', 'dateFrom', 'dateTo'))
            ->setPaper('a4', 'landscape');

        $filename = 'laporan-penjualan-' . $dateFrom->format('Ymd') . '-' . $dateTo->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    public function exportTopProductsExcel(Request $request): BinaryFileResponse
    {
        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to'   => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $dateFrom = $request->date('date_from') ?? now()->startOfMonth();
        $dateTo   = $request->date('date_to') ?? now()->endOfDay();

        $filename = 'laporan-barang-terlaris-' . $dateFrom->format('Ymd') . '-' . $dateTo->format('Ymd') . '.xlsx';

        return Excel::download(new TopProductsReportExport($dateFrom, $dateTo), $filename);
    }

    public function exportTopProductsPdf(Request $request): Response
    {
        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to'   => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $dateFrom = $request->date('date_from') ?? now()->startOfMonth();
        $dateTo   = $request->date('date_to') ?? now()->endOfDay();

        $topProducts = SaleItem::query()
            ->selectRaw('product_id, SUM(qty) as total_qty, SUM(line_total) as total_sales, COUNT(DISTINCT sale_id) as transaction_count')
            ->with('product')
            ->whereHas('sale', function ($query) use ($dateFrom, $dateTo): void {
                $query->whereBetween('sale_date', [$dateFrom->startOfDay(), $dateTo->endOfDay()]);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->orderByDesc('total_sales')
            ->get();

        $pdf = Pdf::loadView('reports.pdf.top-products', compact('topProducts', 'dateFrom', 'dateTo'))
            ->setPaper('a4', 'portrait');

        $filename = 'laporan-barang-terlaris-' . $dateFrom->format('Ymd') . '-' . $dateTo->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}

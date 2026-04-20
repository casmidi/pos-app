<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Exports\TopProductsReportExport;
use App\Models\Sale;
use App\Models\SaleItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function sales(Request $request): View
    {
        [$dateFrom, $dateTo] = $this->resolveDateRange($request);

        $allowedSorts = [
            'invoice_no'     => 'sales.invoice_no',
            'sale_date'      => 'sales.sale_date',
            'customer'       => 'customer',
            'cashier'        => 'cashier',
            'subtotal'       => 'sales.subtotal',
            'discount_total' => 'sales.discount_total',
            'tax_total'      => 'sales.tax_total',
            'grand_total'    => 'sales.grand_total',
            'payment_method' => 'sales.payment_method',
        ];

        $sort      = $request->string('sort')->toString();
        $direction = strtolower($request->string('direction')->toString()) === 'asc' ? 'asc' : 'desc';

        if (! array_key_exists($sort, $allowedSorts)) {
            $sort      = 'sale_date';
            $direction = 'desc';
        }

        $query = Sale::query()->with(['customer', 'user'])
            ->whereBetween('sale_date', [$dateFrom->startOfDay(), $dateTo->endOfDay()]);

        if ($sort === 'customer') {
            $query->leftJoin('customers as rep_customers', 'rep_customers.id', '=', 'sales.customer_id')
                ->select('sales.*')
                ->orderBy('rep_customers.name', $direction)
                ->orderBy('sales.id', 'desc');
        } elseif ($sort === 'cashier') {
            $query->leftJoin('users as rep_users', 'rep_users.id', '=', 'sales.user_id')
                ->select('sales.*')
                ->orderBy('rep_users.name', $direction)
                ->orderBy('sales.id', 'desc');
        } else {
            $query->orderBy($allowedSorts[$sort], $direction)
                ->orderBy('sales.id', 'desc');
        }

        $sales = $query->get();

        $summary = [
            'total_transactions' => $sales->count(),
            'subtotal'           => $sales->sum('subtotal'),
            'discount_total'     => $sales->sum('discount_total'),
            'tax_total'          => $sales->sum('tax_total'),
            'grand_total'        => $sales->sum('grand_total'),
        ];

        return view('reports.sales', compact('sales', 'summary', 'dateFrom', 'dateTo', 'sort', 'direction'));
    }

    public function topProducts(Request $request): View
    {
        [$dateFrom, $dateTo] = $this->resolveDateRange($request);

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
        [$dateFrom, $dateTo] = $this->resolveDateRange($request);

        $filename = 'laporan-penjualan-' . $dateFrom->format('Ymd') . '-' . $dateTo->format('Ymd') . '.xlsx';

        return Excel::download(new SalesReportExport($dateFrom, $dateTo), $filename);
    }

    public function exportSalesPdf(Request $request): Response
    {
        [$dateFrom, $dateTo] = $this->resolveDateRange($request);

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
        [$dateFrom, $dateTo] = $this->resolveDateRange($request);

        $filename = 'laporan-barang-terlaris-' . $dateFrom->format('Ymd') . '-' . $dateTo->format('Ymd') . '.xlsx';

        return Excel::download(new TopProductsReportExport($dateFrom, $dateTo), $filename);
    }

    public function exportTopProductsPdf(Request $request): Response
    {
        [$dateFrom, $dateTo] = $this->resolveDateRange($request);

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

    /**
     * Resolve report date range from dd-MM-yyyy inputs.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    private function resolveDateRange(Request $request): array
    {
        $request->validate([
            'date_from' => ['nullable', 'date_format:d-m-Y'],
            'date_to' => ['nullable', 'date_format:d-m-Y'],
        ]);

        $dateFrom = $request->filled('date_from')
            ? Carbon::createFromFormat('d-m-Y', (string) $request->input('date_from'))->startOfDay()
            : now()->startOfMonth();

        $dateTo = $request->filled('date_to')
            ? Carbon::createFromFormat('d-m-Y', (string) $request->input('date_to'))->endOfDay()
            : now()->endOfDay();

        if ($dateTo->lt($dateFrom)) {
            throw ValidationException::withMessages([
                'date_to' => 'Tanggal sampai harus setelah atau sama dengan tanggal dari.',
            ]);
        }

        return [$dateFrom, $dateTo];
    }
}

<?php

namespace App\Exports;

use App\Models\SaleItem;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TopProductsReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithTitle
{
    public function __construct(
        private readonly Carbon $dateFrom,
        private readonly Carbon $dateTo,
    ) {}

    public function collection()
    {
        return SaleItem::query()
            ->selectRaw('product_id, SUM(qty) as total_qty, SUM(line_total) as total_sales, COUNT(DISTINCT sale_id) as transaction_count')
            ->with('product')
            ->whereHas('sale', function ($query): void {
                $query->whereBetween('sale_date', [
                    $this->dateFrom->copy()->startOfDay(),
                    $this->dateTo->copy()->endOfDay(),
                ]);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->orderByDesc('total_sales')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Rank',
            'SKU',
            'Nama Produk',
            'Qty Terjual',
            'Total Penjualan (Rp)',
            'Jumlah Transaksi',
        ];
    }

    public function map($item): array
    {
        static $rank = 0;
        $rank++;

        return [
            $rank,
            $item->product?->sku ?? '-',
            $item->product?->name ?? '-',
            (int) $item->total_qty,
            (float) $item->total_sales,
            (int) $item->transaction_count,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A5F'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Barang Terlaris';
    }
}

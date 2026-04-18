<?php

namespace App\Exports;

use App\Models\Sale;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesReportExport implements
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
        return Sale::query()
            ->with(['customer', 'user'])
            ->whereBetween('sale_date', [
                $this->dateFrom->copy()->startOfDay(),
                $this->dateTo->copy()->endOfDay(),
            ])
            ->latest('sale_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            '#',
            'Invoice',
            'Tanggal',
            'Pelanggan',
            'Kasir',
            'Subtotal (Rp)',
            'Diskon (Rp)',
            'Pajak (Rp)',
            'Total (Rp)',
            'Metode Bayar',
        ];
    }

    public function map($sale): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $sale->invoice_no,
            $sale->sale_date->format('d/m/Y H:i'),
            $sale->customer?->name ?? '-',
            $sale->user?->name ?? '-',
            (float) $sale->subtotal,
            (float) $sale->discount_total,
            (float) $sale->tax_total,
            (float) $sale->grand_total,
            ucfirst($sale->payment_method),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A5F'],
                ],
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan Penjualan';
    }
}

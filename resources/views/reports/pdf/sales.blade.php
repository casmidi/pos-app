<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
        }

        .header {
            text-align: center;
            margin-bottom: 16px;
        }

        .header h2 {
            font-size: 16px;
            font-weight: bold;
            color: #1e3a5f;
        }

        .header p {
            font-size: 10px;
            color: #555;
            margin-top: 2px;
        }

        .summary {
            display: flex;
            gap: 8px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        .summary-box {
            flex: 1;
            min-width: 90px;
            border: 1px solid #d0d0d0;
            border-radius: 4px;
            padding: 6px 8px;
            text-align: center;
        }

        .summary-box .val {
            font-size: 12px;
            font-weight: bold;
            color: #1e3a5f;
        }

        .summary-box .lbl {
            font-size: 9px;
            color: #777;
            margin-top: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        thead tr {
            background-color: #1e3a5f;
            color: #fff;
        }

        thead th {
            padding: 6px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
        }

        thead th.num {
            text-align: right;
        }

        tbody tr:nth-child(even) {
            background-color: #f5f7fa;
        }

        tbody td {
            padding: 5px 8px;
            font-size: 10px;
            border-bottom: 1px solid #e8e8e8;
            vertical-align: middle;
        }

        tbody td.num {
            text-align: right;
        }

        tfoot tr {
            background-color: #e8f0fe;
            font-weight: bold;
        }

        tfoot td {
            padding: 6px 8px;
            font-size: 10px;
            border-top: 2px solid #1e3a5f;
        }

        tfoot td.num {
            text-align: right;
        }

        .footer {
            margin-top: 14px;
            text-align: right;
            font-size: 9px;
            color: #888;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            background: #bee3f8;
            color: #1a6291;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Laporan Pendapatan Penjualan</h2>
        <p>Periode: {{ $dateFrom->format('d-m-Y') }} &ndash; {{ $dateTo->format('d-m-Y') }}</p>
    </div>

    <div class="summary">
        <div class="summary-box">
            <div class="val">{{ $summary['total_transactions'] }}</div>
            <div class="lbl">Transaksi</div>
        </div>
        <div class="summary-box">
            <div class="val">{{ number_format($summary['subtotal'], 0, ',', '.') }}</div>
            <div class="lbl">Subtotal (Rp)</div>
        </div>
        <div class="summary-box">
            <div class="val">{{ number_format($summary['discount_total'], 0, ',', '.') }}</div>
            <div class="lbl">Diskon (Rp)</div>
        </div>
        <div class="summary-box">
            <div class="val">{{ number_format($summary['tax_total'], 0, ',', '.') }}</div>
            <div class="lbl">Pajak (Rp)</div>
        </div>
        <div class="summary-box">
            <div class="val">{{ number_format($summary['grand_total'], 0, ',', '.') }}</div>
            <div class="lbl">Total (Rp)</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Invoice</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Kasir</th>
                <th class="num">Subtotal</th>
                <th class="num">Diskon</th>
                <th class="num">Pajak</th>
                <th class="num">Total</th>
                <th>Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sales as $i => $sale)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $sale->invoice_no }}</td>
                    <td>{{ $sale->sale_date->format('d-m-Y H:i') }}</td>
                    <td>{{ $sale->customer?->name ?? '-' }}</td>
                    <td>{{ $sale->user?->name ?? '-' }}</td>
                    <td class="num">{{ number_format($sale->subtotal, 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($sale->discount_total, 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($sale->tax_total, 0, ',', '.') }}</td>
                    <td class="num"><strong>{{ number_format($sale->grand_total, 0, ',', '.') }}</strong></td>
                    <td><span class="badge">{{ ucfirst($sale->payment_method) }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align:center; color:#999; padding:12px;">Tidak ada data transaksi.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if ($sales->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align:right;">TOTAL</td>
                    <td class="num">{{ number_format($summary['subtotal'], 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($summary['discount_total'], 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($summary['tax_total'], 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($summary['grand_total'], 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer">Dicetak: {{ now()->format('d-m-Y H:i') }}</div>
</body>

</html>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Barang Terlaris</title>
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

        .rank {
            display: inline-block;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #1e3a5f;
            color: #fff;
            font-weight: bold;
            text-align: center;
            line-height: 22px;
            font-size: 9px;
        }

        .rank-1 {
            background: #f6ad55;
            color: #7b3e00;
        }

        .rank-2 {
            background: #a0aec0;
            color: #1a1a1a;
        }

        .rank-3 {
            background: #c6a96a;
            color: #4a3000;
        }

        .footer {
            margin-top: 14px;
            text-align: right;
            font-size: 9px;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Laporan Barang Terlaris</h2>
        <p>Periode: {{ $dateFrom->format('d-m-Y') }} &ndash; {{ $dateTo->format('d-m-Y') }}</p>
    </div>

    <table>
        <thead>
            <div class="footer">Dicetak: {{ now()->format('d-m-Y H:i') }}</div>
            <th>Rank</th>
            <th>SKU</th>
            <th>Nama Produk</th>
            <th class="num">Qty Terjual</th>
            <th class="num">Total Penjualan (Rp)</th>
            <th class="num">Jumlah Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($topProducts as $index => $item)
                <tr>
                    <td>
                        <span class="rank rank-{{ $index + 1 <= 3 ? $index + 1 : 0 }}">
                            #{{ $index + 1 }}
                        </span>
                    </td>
                    <td>{{ $item->product?->sku ?? '-' }}</td>
                    <td>{{ $item->product?->name ?? '-' }}</td>
                    <td class="num"><strong>{{ number_format($item->total_qty, 0, ',', '.') }}</strong></td>
                    <td class="num">{{ number_format($item->total_sales, 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($item->transaction_count, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center; color:#999; padding:12px;">Belum ada data penjualan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dicetak: {{ now()->format('d-m-Y H:i') }}</div>
</body>

</html>

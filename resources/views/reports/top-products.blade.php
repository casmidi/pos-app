@extends('adminlte::page')

@section('title', 'Laporan Barang Terlaris')

@section('css')
    <style>
        .report-filter .form-control {
            min-width: 180px;
        }

        .report-table table {
            min-width: 760px;
        }

        @media (max-width: 767.98px) {

            .report-filter .form-control,
            .report-filter .btn {
                width: 100%;
            }
        }
    </style>
@stop

@section('content_header')
    <h1>Laporan Barang Terlaris</h1>
@stop

@section('content')
    @include('partials.flash')

    <div class="card card-outline card-primary mb-4">
        <div class="card-header">
            <h3 class="card-title">Filter Periode</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.top-products') }}" class="report-filter row align-items-end">
                <div class="form-group col-12 col-md-4 col-lg-3 mb-2">
                    <label>Dari</label>
                    <input type="date" name="date_from" value="{{ $dateFrom->format('Y-m-d') }}"
                        class="form-control @error('date_from') is-invalid @enderror">
                    @error('date_from')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group col-12 col-md-4 col-lg-3 mb-2">
                    <label>Sampai</label>
                    <input type="date" name="date_to" value="{{ $dateTo->format('Y-m-d') }}"
                        class="form-control @error('date_to') is-invalid @enderror">
                    @error('date_to')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group col-12 col-md-4 col-lg-3 mb-2">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                    <a href="{{ route('reports.top-products') }}" class="btn btn-default mt-2 mt-md-0 ml-md-1">Reset</a>
                </div>
                <div class="form-group col-12 col-lg-auto mb-2 ml-lg-auto">
                    <a href="{{ route('reports.top-products.export.excel', request()->only('date_from', 'date_to')) }}"
                        class="btn btn-success">
                        <i class="fas fa-file-excel mr-1"></i> Excel
                    </a>
                    <a href="{{ route('reports.top-products.export.pdf', request()->only('date_from', 'date_to')) }}"
                        class="btn btn-danger ml-1">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-outline card-success">
        <div class="card-header">
            <h3 class="card-title">
                Ranking Barang Terlaris
                <span class="badge badge-secondary ml-md-2 mt-2 mt-md-0">
                    {{ $dateFrom->format('d/m/Y') }} - {{ $dateTo->format('d/m/Y') }}
                </span>
            </h3>
        </div>
        <div class="card-body table-responsive p-0 report-table">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>SKU</th>
                        <th>Nama Produk</th>
                        <th class="text-right">Qty Terjual</th>
                        <th class="text-right">Total Penjualan</th>
                        <th class="text-right">Jumlah Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topProducts as $index => $item)
                        <tr>
                            <td><span class="badge badge-info">#{{ $index + 1 }}</span></td>
                            <td>{{ $item->product?->sku ?? '-' }}</td>
                            <td>{{ $item->product?->name ?? '-' }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($item->total_qty, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item->total_sales, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item->transaction_count, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada data penjualan pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

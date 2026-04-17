@extends('adminlte::page')

@section('title', 'Dashboard POS')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">Dashboard POS</h1>
        <a href="{{ route('sales.create') }}" class="btn btn-warning font-weight-bold">+ Transaksi Baru</a>
    </div>
@stop

@section('content')
    @include('partials.flash')

    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-info">
                <div class="inner">
                    <h3>{{ $totalProducts }}</h3>
                    <p>Total Produk</p>
                </div>
                <div class="icon"><i class="fas fa-boxes"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-danger">
                <div class="inner">
                    <h3>{{ $lowStockProducts }}</h3>
                    <p>Produk Stok Menipis</p>
                </div>
                <div class="icon"><i class="fas fa-triangle-exclamation"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-success">
                <div class="inner">
                    <h3>{{ $totalCustomers }}</h3>
                    <p>Total Pelanggan</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-warning">
                <div class="inner">
                    <h3>Rp {{ number_format($todaySales, 0, ',', '.') }}</h3>
                    <p>Penjualan Hari Ini</p>
                </div>
                <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
            </div>
        </div>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Transaksi Terbaru</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Kasir</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($latestSales as $sale)
                        <tr>
                            <td>{{ $sale->invoice_no }}</td>
                            <td>{{ $sale->sale_date->format('d-m-Y H:i') }}</td>
                            <td>{{ $sale->customer?->name ?? '-' }}</td>
                            <td>{{ $sale->user?->name ?? '-' }}</td>
                            <td>Rp {{ number_format((float) $sale->grand_total, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-xs btn-primary">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

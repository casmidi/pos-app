@extends('adminlte::page')

@section('title', 'Dashboard POS')

@include('partials.premium-ui-styles')
@include('partials.premium-grid-styles')

@section('content_header')
    <div class="premium-shell premium-dashboard-hero d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <h1 class="mb-1">Dashboard POS</h1>
            <p class="premium-dashboard-subtitle">Monitor operasional, penjualan, dan performa outlet dalam satu layar.</p>
        </div>
        <a href="{{ route('sales.create') }}" class="btn premium-btn-warning mt-3 mt-sm-0"><i
                class="fas fa-plus mr-1"></i>Transaksi Baru</a>
    </div>
@stop

@section('content')
    @include('partials.flash')

    <div class="premium-shell">
        <div class="row mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="small-box premium-dashboard-stat bg-gradient-info">
                    <div class="inner">
                        <h3>{{ $totalProducts }}</h3>
                        <p>Total Produk</p>
                    </div>
                    <div class="icon"><i class="fas fa-boxes"></i></div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="small-box premium-dashboard-stat bg-gradient-danger">
                    <div class="inner">
                        <h3>{{ $lowStockProducts }}</h3>
                        <p>Produk Stok Menipis</p>
                    </div>
                    <div class="icon"><i class="fas fa-triangle-exclamation"></i></div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="small-box premium-dashboard-stat bg-gradient-success">
                    <div class="inner">
                        <h3>{{ $totalCustomers }}</h3>
                        <p>Total Pelanggan</p>
                    </div>
                    <div class="icon"><i class="fas fa-users"></i></div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="small-box premium-dashboard-stat bg-gradient-warning">
                    <div class="inner">
                        <h3>Rp {{ number_format($todaySales, 0, ',', '.') }}</h3>
                        <p>Penjualan Hari Ini</p>
                    </div>
                    <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                </div>
            </div>
        </div>

        @include('dashboard.partials.grid')
    </div>
@stop

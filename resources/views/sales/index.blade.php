@extends('adminlte::page')

@section('title', 'Transaksi Penjualan')

@include('partials.premium-grid-styles')

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-center">
        <h1 class="mb-0">Transaksi Penjualan</h1>
        <a href="{{ route('sales.create') }}" class="btn btn-primary mt-2 mt-sm-0">+ Transaksi Baru</a>
    </div>
@stop

@section('content')
    @include('partials.flash')

    @include('sales.partials.grid')
@stop

@extends('adminlte::page')

@section('title', 'Edit Transaksi')

@section('content_header')
    <h1>Edit Transaksi {{ $sale->invoice_no }}</h1>
@stop

@section('content')
    @include('partials.flash')

    <div class="card card-outline card-warning">
        <form action="{{ route('sales.update', $sale) }}" method="POST">
            @csrf
            @method('PUT')
            @include('sales._form', ['sale' => $sale])
            <div class="card-footer">
                <button class="btn btn-warning">Perbarui Transaksi</button>
                <a href="{{ route('sales.index') }}" class="btn btn-default">Kembali</a>
            </div>
        </form>
    </div>
@stop

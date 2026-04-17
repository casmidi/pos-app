@extends('adminlte::page')

@section('title', 'Transaksi Baru')

@section('content_header')
    <h1>Transaksi Baru</h1>
@stop

@section('content')
    @include('partials.flash')

    <div class="card card-outline card-primary">
        <form action="{{ route('sales.store') }}" method="POST">
            @csrf
            @include('sales._form')
            <div class="card-footer">
                <button class="btn btn-primary">Simpan Transaksi</button>
                <a href="{{ route('sales.index') }}" class="btn btn-default">Kembali</a>
            </div>
        </form>
    </div>
@stop

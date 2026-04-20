@extends('adminlte::page')

@section('title', 'Transaksi Baru')

@include('partials.premium-ui-styles')

@section('content_header')
    <div class="premium-shell">
        <h1>Transaksi Baru</h1>
    </div>
@stop

@section('content')
    @include('partials.flash')

    <div class="premium-shell">
        <div class="card premium-form-card card-outline card-primary">
            <form action="{{ route('sales.store') }}" method="POST">
                @csrf
                @include('sales._form')
                <div class="card-footer">
                    <button class="btn premium-btn-primary"><i class="fas fa-floppy-disk mr-1"></i>Simpan Transaksi</button>
                    <a href="{{ route('sales.index') }}" class="btn premium-btn-secondary"><i
                            class="fas fa-arrow-left mr-1"></i>Kembali</a>
                </div>
            </form>
        </div>
    </div>
@stop

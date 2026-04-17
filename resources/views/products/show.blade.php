@extends('adminlte::page')

@section('title', 'Detail Produk')

@section('content_header')
    <h1>Detail Produk</h1>
@stop

@section('content')
    <div class="card card-outline card-info">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">SKU</dt>
                <dd class="col-sm-9">{{ $product->sku }}</dd>
                <dt class="col-sm-3">Nama</dt>
                <dd class="col-sm-9">{{ $product->name }}</dd>
                <dt class="col-sm-3">Kategori</dt>
                <dd class="col-sm-9">{{ $product->category?->name ?? '-' }}</dd>
                <dt class="col-sm-3">Harga Modal</dt>
                <dd class="col-sm-9">Rp {{ number_format((float) $product->cost_price, 0, ',', '.') }}</dd>
                <dt class="col-sm-3">Harga Jual</dt>
                <dd class="col-sm-9">Rp {{ number_format((float) $product->sell_price, 0, ',', '.') }}</dd>
                <dt class="col-sm-3">Stok</dt>
                <dd class="col-sm-9">{{ $product->stock }}</dd>
                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</dd>
                <dt class="col-sm-3">Deskripsi</dt>
                <dd class="col-sm-9">{{ $product->description ?: '-' }}</dd>
            </dl>
        </div>
        <div class="card-footer">
            <a href="{{ route('products.index') }}" class="btn btn-default">Kembali</a>
        </div>
    </div>
@stop

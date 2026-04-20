@extends('adminlte::page')

@section('title', 'Detail Produk')

@include('partials.premium-ui-styles')

@section('content_header')
    <div class="premium-shell">
        <h1>Detail Produk</h1>
    </div>
@stop

@section('content')
    <div class="premium-shell">
        <div class="card premium-detail-card card-outline card-info">
            <div class="card-body">
                <dl class="row mb-0 premium-detail-list">
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
                    <dd class="col-sm-9">
                        @if ($product->is_active)
                            <span class="badge badge-success premium-detail-pill"><i class="fas fa-circle"></i>Aktif</span>
                        @else
                            <span class="badge badge-secondary premium-detail-pill"><i
                                    class="fas fa-circle"></i>Nonaktif</span>
                        @endif
                    </dd>
                    <dt class="col-sm-3">Deskripsi</dt>
                    <dd class="col-sm-9">{{ $product->description ?: '-' }}</dd>
                    @if ($product->image)
                        <dt class="col-sm-3">Gambar</dt>
                        <dd class="col-sm-9">
                            <img src="{{ Storage::disk('public')->url($product->image) }}"
                                alt="{{ $product->name }}"
                                style="max-width:300px;max-height:250px;border-radius:10px;border:1.5px solid #dee2e6;object-fit:contain;">
                        </dd>
                    @endif
                </dl>
            </div>
            <div class="card-footer">
                <a href="{{ route('products.index') }}" class="btn premium-btn-secondary"><i
                        class="fas fa-arrow-left mr-1"></i>Kembali</a>
            </div>
        </div>
    </div>
@stop

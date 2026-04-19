@extends('adminlte::page')

@section('title', 'Produk')

@include('partials.premium-grid-styles')

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-center">
        <h1 class="mb-0">Produk</h1>
        <a href="{{ route('products.create') }}" class="btn btn-primary mt-2 mt-sm-0">+ Tambah Produk</a>
    </div>
@stop

@section('content')
    @include('partials.flash')

    @php
        $sortDirection = fn($column) => ($sort ?? '') === $column && ($direction ?? 'desc') === 'asc' ? 'desc' : 'asc';
        $sortIcon = fn($column) => ($sort ?? '') === $column
            ? (($direction ?? 'desc') === 'asc'
                ? 'fas fa-sort-up'
                : 'fas fa-sort-down')
            : 'fas fa-sort';
    @endphp

    <div class="premium-grid-page">
        <div class="card premium-grid-card card-outline card-primary">
            <div class="card-header">
                <form action="{{ route('products.index') }}" method="GET" class="premium-toolbar row align-items-end">
                    <div class="col-12 col-md-8 col-lg-6">
                        <label for="product-search" class="mb-1">Search Product</label>
                        <div class="input-group">
                            <input type="text" id="product-search" name="q" class="form-control"
                                value="{{ $search ?? '' }}" placeholder="Search by SKU, product name, or category">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search mr-1"></i> Search
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-auto mt-2 mt-md-0">
                        <a href="{{ route('products.index') }}" class="btn btn-default btn-block">
                            <i class="fas fa-rotate-left mr-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover premium-grid-table zebra-grid">
                    <thead>
                        <tr>
                            <th><a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'sku', 'direction' => $sortDirection('sku'), 'page' => 1])) }}"
                                    class="grid-sort-link">SKU <i class="{{ $sortIcon('sku') }}"></i></a></th>
                            <th><a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => $sortDirection('name'), 'page' => 1])) }}"
                                    class="grid-sort-link">Nama <i class="{{ $sortIcon('name') }}"></i></a></th>
                            <th><a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'category', 'direction' => $sortDirection('category'), 'page' => 1])) }}"
                                    class="grid-sort-link">Kategori <i class="{{ $sortIcon('category') }}"></i></a></th>
                            <th><a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'sell_price', 'direction' => $sortDirection('sell_price'), 'page' => 1])) }}"
                                    class="grid-sort-link">Harga Jual <i class="{{ $sortIcon('sell_price') }}"></i></a>
                            </th>
                            <th><a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'stock', 'direction' => $sortDirection('stock'), 'page' => 1])) }}"
                                    class="grid-sort-link">Stok <i class="{{ $sortIcon('stock') }}"></i></a></th>
                            <th><a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'status', 'direction' => $sortDirection('status'), 'page' => 1])) }}"
                                    class="grid-sort-link">Status <i class="{{ $sortIcon('status') }}"></i></a></th>
                            <th class="text-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>{{ $product->sku }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category?->name ?? '-' }}</td>
                                <td>Rp {{ number_format((float) $product->sell_price, 0, ',', '.') }}</td>
                                <td>{{ $product->stock }}</td>
                                <td>
                                    @if ($product->is_active)
                                        <span class="badge badge-success premium-status-badge"><i
                                                class="fas fa-circle"></i>Aktif</span>
                                    @else
                                        <span class="badge badge-secondary premium-status-badge"><i
                                                class="fas fa-circle"></i>Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-nowrap premium-action-cell">
                                    <a class="btn btn-xs btn-info" href="{{ route('products.show', $product) }}">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                    <a class="btn btn-xs btn-warning" href="{{ route('products.edit', $product) }}">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST"
                                        class="d-inline js-premium-delete-form"
                                        data-confirm-message="Delete this product permanently?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger">
                                            <i class="fas fa-trash mr-1"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">Data produk kosong.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="premium-grid-summary mb-2 mb-md-0">
                    Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of
                    {{ $products->total() }}
                    entries
                </div>
                <div>
                    {{ $products->links('pagination.premium-grid') }}
                </div>
            </div>
        </div>
    </div>
@stop

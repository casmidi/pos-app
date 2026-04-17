@extends('adminlte::page')

@section('title', 'Produk')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">Produk</h1>
        <a href="{{ route('products.create') }}" class="btn btn-primary">+ Tambah Produk</a>
    </div>
@stop

@section('content')
    @include('partials.flash')

    <div class="card card-outline card-primary">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th style="width: 170px"></th>
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
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-xs btn-info" href="{{ route('products.show', $product) }}">Detail</a>
                                <a class="btn btn-xs btn-warning" href="{{ route('products.edit', $product) }}">Edit</a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</button>
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
        <div class="card-footer">{{ $products->links() }}</div>
    </div>
@stop

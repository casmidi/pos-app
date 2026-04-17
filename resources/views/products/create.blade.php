@extends('adminlte::page')

@section('title', 'Tambah Produk')

@section('content_header')
    <h1>Tambah Produk</h1>
@stop

@section('content')
    @include('partials.flash')

    <div class="card card-outline card-primary">
        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            <div class="card-body row">
                <div class="form-group col-md-4">
                    <label>SKU</label>
                    <input type="text" name="sku" class="form-control" value="{{ old('sku') }}" required>
                </div>
                <div class="form-group col-md-8">
                    <label>Nama Produk</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Kategori</label>
                    <select name="category_id" class="form-control">
                        <option value="">-</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Harga Modal</label>
                    <input type="number" step="0.01" name="cost_price" class="form-control" value="{{ old('cost_price', 0) }}" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Harga Jual</label>
                    <input type="number" step="0.01" name="sell_price" class="form-control" value="{{ old('sell_price', 0) }}" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Stok</label>
                    <input type="number" name="stock" class="form-control" value="{{ old('stock', 0) }}" required>
                </div>
                <div class="form-group col-md-8">
                    <label>Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>
                <div class="form-group col-md-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" @checked(old('is_active', true))>
                        <label class="custom-control-label" for="is_active">Produk aktif</label>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary">Simpan</button>
                <a href="{{ route('products.index') }}" class="btn btn-default">Kembali</a>
            </div>
        </form>
    </div>
@stop

@extends('adminlte::page')

@section('title', 'Edit Kategori')

@section('content_header')
    <h1>Edit Kategori</h1>
@stop

@section('content')
    @include('partials.flash')

    <div class="card card-outline card-warning">
        <form action="{{ route('categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Kategori</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description', $category->description) }}</textarea>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-warning">Perbarui</button>
                <a href="{{ route('categories.index') }}" class="btn btn-default">Kembali</a>
            </div>
        </form>
    </div>
@stop

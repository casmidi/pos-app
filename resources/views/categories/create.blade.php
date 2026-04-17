@extends('adminlte::page')

@section('title', 'Tambah Kategori')

@section('content_header')
    <h1>Tambah Kategori</h1>
@stop

@section('content')
    @include('partials.flash')

    <div class="card card-outline card-primary">
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Kategori</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary">Simpan</button>
                <a href="{{ route('categories.index') }}" class="btn btn-default">Kembali</a>
            </div>
        </form>
    </div>
@stop

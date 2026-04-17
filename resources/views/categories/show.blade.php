@extends('adminlte::page')

@section('title', 'Detail Kategori')

@section('content_header')
    <h1>Detail Kategori</h1>
@stop

@section('content')
    <div class="card card-outline card-info">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9">{{ $category->id }}</dd>
                <dt class="col-sm-3">Nama</dt>
                <dd class="col-sm-9">{{ $category->name }}</dd>
                <dt class="col-sm-3">Deskripsi</dt>
                <dd class="col-sm-9">{{ $category->description ?: '-' }}</dd>
            </dl>
        </div>
        <div class="card-footer">
            <a href="{{ route('categories.index') }}" class="btn btn-default">Kembali</a>
        </div>
    </div>
@stop

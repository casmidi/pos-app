@extends('adminlte::page')

@section('title', 'Kategori')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">Kategori</h1>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">+ Tambah Kategori</a>
    </div>
@stop

@section('content')
    @include('partials.flash')

    <div class="card card-outline card-primary">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th style="width: 170px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->description ?: '-' }}</td>
                            <td>
                                <a class="btn btn-xs btn-info" href="{{ route('categories.show', $category) }}">Detail</a>
                                <a class="btn btn-xs btn-warning" href="{{ route('categories.edit', $category) }}">Edit</a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">Data kategori kosong.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $categories->links() }}</div>
    </div>
@stop

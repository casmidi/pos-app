@extends('adminlte::page')

@section('title', 'Pelanggan')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">Pelanggan</h1>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">+ Tambah Pelanggan</a>
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
                        <th>Telepon</th>
                        <th>Email</th>
                        <th style="width: 170px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td>{{ $customer->id }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->phone ?: '-' }}</td>
                            <td>{{ $customer->email ?: '-' }}</td>
                            <td>
                                <a class="btn btn-xs btn-info" href="{{ route('customers.show', $customer) }}">Detail</a>
                                <a class="btn btn-xs btn-warning" href="{{ route('customers.edit', $customer) }}">Edit</a>
                                <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Data pelanggan kosong.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $customers->links() }}</div>
    </div>
@stop

@extends('adminlte::page')

@section('title', 'Pelanggan')

@include('partials.premium-grid-styles')

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-center">
        <h1 class="mb-0">Pelanggan</h1>
        <a href="{{ route('customers.create') }}" class="btn btn-primary mt-2 mt-sm-0">+ Tambah Pelanggan</a>
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
                <form action="{{ route('customers.index') }}" method="GET" class="premium-toolbar row align-items-end">
                    <div class="col-12 col-md-8 col-lg-6">
                        <label for="customer-search" class="mb-1">Search Customer</label>
                        <div class="input-group">
                            <input type="text" id="customer-search" name="q" class="form-control"
                                value="{{ $search ?? '' }}" placeholder="Search by name, phone, email, or address">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search mr-1"></i> Search
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-auto mt-2 mt-md-0">
                        <a href="{{ route('customers.index') }}" class="btn btn-default btn-block">
                            <i class="fas fa-rotate-left mr-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover premium-grid-table zebra-grid">
                    <thead>
                        <tr>
                            <th><a href="{{ route('customers.index', array_merge(request()->query(), ['sort' => 'id', 'direction' => $sortDirection('id'), 'page' => 1])) }}"
                                    class="grid-sort-link"># <i class="{{ $sortIcon('id') }}"></i></a></th>
                            <th><a href="{{ route('customers.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => $sortDirection('name'), 'page' => 1])) }}"
                                    class="grid-sort-link">Nama <i class="{{ $sortIcon('name') }}"></i></a></th>
                            <th><a href="{{ route('customers.index', array_merge(request()->query(), ['sort' => 'phone', 'direction' => $sortDirection('phone'), 'page' => 1])) }}"
                                    class="grid-sort-link">Telepon <i class="{{ $sortIcon('phone') }}"></i></a></th>
                            <th><a href="{{ route('customers.index', array_merge(request()->query(), ['sort' => 'email', 'direction' => $sortDirection('email'), 'page' => 1])) }}"
                                    class="grid-sort-link">Email <i class="{{ $sortIcon('email') }}"></i></a></th>
                            <th class="text-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td>{{ $customer->id }}</td>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->phone ?: '-' }}</td>
                                <td>{{ $customer->email ?: '-' }}</td>
                                <td class="text-nowrap premium-action-cell">
                                    <a class="btn btn-xs btn-info" href="{{ route('customers.show', $customer) }}">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                    <a class="btn btn-xs btn-warning" href="{{ route('customers.edit', $customer) }}">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    <form action="{{ route('customers.destroy', $customer) }}" method="POST"
                                        class="d-inline js-premium-delete-form"
                                        data-confirm-message="Delete this customer permanently?">
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
                                <td colspan="5" class="text-center py-4">Data pelanggan kosong.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="premium-grid-summary mb-2 mb-md-0">
                    Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of
                    {{ $customers->total() }} entries
                </div>
                <div>
                    {{ $customers->links('pagination.premium-grid') }}
                </div>
            </div>
        </div>
    </div>
@stop

@extends('adminlte::page')

@section('title', 'Edit Pelanggan')

@section('content_header')
    <h1>Edit Pelanggan</h1>
@stop

@section('content')
    @include('partials.flash')

    <div class="card card-outline card-warning">
        <form action="{{ route('customers.update', $customer) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body row">
                <div class="form-group col-md-6">
                    <label>Nama</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name) }}" required>
                </div>
                <div class="form-group col-md-3">
                    <label>Telepon</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}">
                </div>
                <div class="form-group col-md-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}">
                </div>
                <div class="form-group col-md-12">
                    <label>Alamat</label>
                    <textarea name="address" class="form-control" rows="3">{{ old('address', $customer->address) }}</textarea>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-warning">Perbarui</button>
                <a href="{{ route('customers.index') }}" class="btn btn-default">Kembali</a>
            </div>
        </form>
    </div>
@stop

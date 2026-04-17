@extends('adminlte::page')

@section('title', 'Detail Pelanggan')

@section('content_header')
    <h1>Detail Pelanggan</h1>
@stop

@section('content')
    <div class="card card-outline card-info">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9">{{ $customer->id }}</dd>
                <dt class="col-sm-3">Nama</dt>
                <dd class="col-sm-9">{{ $customer->name }}</dd>
                <dt class="col-sm-3">Telepon</dt>
                <dd class="col-sm-9">{{ $customer->phone ?: '-' }}</dd>
                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $customer->email ?: '-' }}</dd>
                <dt class="col-sm-3">Alamat</dt>
                <dd class="col-sm-9">{{ $customer->address ?: '-' }}</dd>
            </dl>
        </div>
        <div class="card-footer">
            <a href="{{ route('customers.index') }}" class="btn btn-default">Kembali</a>
        </div>
    </div>
@stop

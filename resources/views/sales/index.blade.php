@extends('adminlte::page')

@section('title', 'Transaksi Penjualan')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">Transaksi Penjualan</h1>
        <a href="{{ route('sales.create') }}" class="btn btn-primary">+ Transaksi Baru</a>
    </div>
@stop

@section('content')
    @include('partials.flash')

    <div class="card card-outline card-primary">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Bayar</th>
                        <th>Kembalian</th>
                        <th style="width: 170px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr>
                            <td>{{ $sale->invoice_no }}</td>
                            <td>{{ $sale->sale_date->format('d-m-Y H:i') }}</td>
                            <td>{{ $sale->customer?->name ?? '-' }}</td>
                            <td>Rp {{ number_format((float) $sale->grand_total, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format((float) $sale->paid_amount, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format((float) $sale->change_amount, 0, ',', '.') }}</td>
                            <td>
                                <a class="btn btn-xs btn-info" href="{{ route('sales.show', $sale) }}">Detail</a>
                                <a class="btn btn-xs btn-warning" href="{{ route('sales.edit', $sale) }}">Edit</a>
                                <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Hapus transaksi ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $sales->links() }}</div>
    </div>
@stop

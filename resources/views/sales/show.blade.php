@extends('adminlte::page')

@section('title', 'Detail Transaksi')

@section('content_header')
    <h1>Detail Transaksi {{ $sale->invoice_no }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-outline card-info">
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Invoice</dt>
                        <dd class="col-sm-7">{{ $sale->invoice_no }}</dd>
                        <dt class="col-sm-5">Tanggal</dt>
                        <dd class="col-sm-7">{{ $sale->sale_date->format('d-m-Y H:i') }}</dd>
                        <dt class="col-sm-5">Kasir</dt>
                        <dd class="col-sm-7">{{ $sale->user?->name ?? '-' }}</dd>
                        <dt class="col-sm-5">Pelanggan</dt>
                        <dd class="col-sm-7">{{ $sale->customer?->name ?? '-' }}</dd>
                        <dt class="col-sm-5">Pembayaran</dt>
                        <dd class="col-sm-7">{{ strtoupper($sale->payment_method) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card card-outline card-primary">
                <div class="card-header"><h3 class="card-title">Item</h3></div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Diskon</th>
                                <th>Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale->items as $item)
                                <tr>
                                    <td>{{ $item->product?->name ?? '-' }}</td>
                                    <td>{{ $item->qty }}</td>
                                    <td>Rp {{ number_format((float) $item->price, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format((float) $item->discount, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format((float) $item->line_total, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer row">
                    <div class="col-md-6 ml-auto">
                        <table class="table table-sm mb-0">
                            <tr><th>Subtotal</th><td class="text-right">Rp {{ number_format((float) $sale->subtotal, 0, ',', '.') }}</td></tr>
                            <tr><th>Diskon</th><td class="text-right">Rp {{ number_format((float) $sale->discount_total, 0, ',', '.') }}</td></tr>
                            <tr><th>Pajak</th><td class="text-right">Rp {{ number_format((float) $sale->tax_total, 0, ',', '.') }}</td></tr>
                            <tr><th>Grand Total</th><td class="text-right font-weight-bold">Rp {{ number_format((float) $sale->grand_total, 0, ',', '.') }}</td></tr>
                            <tr><th>Bayar</th><td class="text-right">Rp {{ number_format((float) $sale->paid_amount, 0, ',', '.') }}</td></tr>
                            <tr><th>Kembalian</th><td class="text-right">Rp {{ number_format((float) $sale->change_amount, 0, ',', '.') }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('sales.index') }}" class="btn btn-default">Kembali</a>
@stop

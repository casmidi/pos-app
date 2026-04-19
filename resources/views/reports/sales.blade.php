@extends('adminlte::page')

@section('title', 'Penjualan Per Periode')

@section('css')
    <style>
        .report-filter .form-control {
            min-width: 180px;
        }

        .report-table table {
            min-width: 980px;
        }

        .report-summary > [class*='col-'] {
            display: flex;
        }

        .report-summary .small-box {
            width: 100%;
            margin-bottom: 0;
        }

        .report-summary .small-box .inner h4 {
            font-size: 2rem;
            margin-bottom: 6px;
            line-height: 1.1;
        }

        @media (max-width: 767.98px) {

            .report-filter .form-control,
            .report-filter .btn {
                width: 100%;
            }
        }
    </style>
@stop

@section('content_header')
    <h1>Penjualan Per Periode</h1>
@stop

@section('content')
    @include('partials.flash')

    {{-- Filter Form --}}
    <div class="card card-outline card-primary mb-4">
        <div class="card-header">
            <h3 class="card-title">Filter Tanggal</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.sales') }}" class="report-filter row align-items-end">
                <div class="form-group col-12 col-md-4 col-lg-3 mb-2">
                    <label>Dari</label>
                    <input type="date" name="date_from" value="{{ $dateFrom->format('Y-m-d') }}"
                        class="form-control @error('date_from') is-invalid @enderror">
                    @error('date_from')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group col-12 col-md-4 col-lg-3 mb-2">
                    <label>Sampai</label>
                    <input type="date" name="date_to" value="{{ $dateTo->format('Y-m-d') }}"
                        class="form-control @error('date_to') is-invalid @enderror">
                    @error('date_to')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group col-12 col-md-4 col-lg-3 mb-2">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                    <a href="{{ route('reports.sales') }}" class="btn btn-default mt-2 mt-md-0 ml-md-1">Reset</a>
                </div>
                <div class="form-group col-12 col-lg-auto mb-2 ml-lg-auto">
                    <a href="{{ route('reports.sales.export.excel', request()->only('date_from', 'date_to')) }}"
                        class="btn btn-success">
                        <i class="fas fa-file-excel mr-1"></i> Excel
                    </a>
                    <a href="{{ route('reports.sales.export.pdf', request()->only('date_from', 'date_to')) }}"
                        class="btn btn-danger ml-1">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-3 report-summary">
        <div class="col-lg-2 col-6">
            <div class="small-box bg-gradient-info">
                <div class="inner">
                    <h4>{{ $summary['total_transactions'] }}</h4>
                    <p>Transaksi</p>
                </div>
                <div class="icon"><i class="fas fa-receipt"></i></div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-gradient-secondary">
                <div class="inner">
                    <h4>{{ number_format($summary['subtotal'], 0, ',', '.') }}</h4>
                    <p>Subtotal</p>
                </div>
                <div class="icon"><i class="fas fa-calculator"></i></div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-gradient-warning">
                <div class="inner">
                    <h4>{{ number_format($summary['discount_total'], 0, ',', '.') }}</h4>
                    <p>Diskon</p>
                </div>
                <div class="icon"><i class="fas fa-tags"></i></div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-gradient-danger">
                <div class="inner">
                    <h4>{{ number_format($summary['tax_total'], 0, ',', '.') }}</h4>
                    <p>Pajak</p>
                </div>
                <div class="icon"><i class="fas fa-percent"></i></div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            <div class="small-box bg-gradient-success">
                <div class="inner">
                    <h4>Rp {{ number_format($summary['grand_total'], 0, ',', '.') }}</h4>
                    <p>Total Pendapatan</p>
                </div>
                <div class="icon"><i class="fas fa-coins"></i></div>
            </div>
        </div>
    </div>

    {{-- Detail Table --}}
    <div class="card card-outline card-success">
        <div class="card-header">
            <h3 class="card-title">
                Detail Transaksi
                <span class="badge badge-secondary ml-md-2 mt-2 mt-md-0">
                    {{ $dateFrom->format('d-m-Y') }} – {{ $dateTo->format('d-m-Y') }}
                </span>
            </h3>
        </div>
        <div class="card-body table-responsive p-0 report-table">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Kasir</th>
                        <th class="text-right">Subtotal</th>
                        <th class="text-right">Diskon</th>
                        <th class="text-right">Pajak</th>
                        <th class="text-right">Total</th>
                        <th>Pembayaran</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $i => $sale)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <a href="{{ route('sales.show', $sale) }}">{{ $sale->invoice_no }}</a>
                            </td>
                            <td>{{ $sale->sale_date->format('d-m-Y H:i') }}</td>
                            <td>{{ $sale->customer?->name ?? '-' }}</td>
                            <td>{{ $sale->user?->name ?? '-' }}</td>
                            <td class="text-right">{{ number_format($sale->subtotal, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($sale->discount_total, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($sale->tax_total, 0, ',', '.') }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($sale->grand_total, 0, ',', '.') }}
                            </td>
                            <td><span class="badge badge-info">{{ ucfirst($sale->payment_method) }}</span></td>
                            <td>
                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-xs btn-default">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                Tidak ada transaksi pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($sales->isNotEmpty())
                    <tfoot>
                        <tr class="table-light font-weight-bold">
                            <td colspan="5" class="text-right">TOTAL</td>
                            <td class="text-right">{{ number_format($summary['subtotal'], 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($summary['discount_total'], 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($summary['tax_total'], 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($summary['grand_total'], 0, ',', '.') }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
@stop

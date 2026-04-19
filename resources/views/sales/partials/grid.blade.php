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
            <form action="{{ route('sales.index') }}" method="GET" class="premium-toolbar row align-items-end">
                <div class="col-12 col-md-8 col-lg-6">
                    <label for="sale-search" class="mb-1">Search Transaction</label>
                    <div class="input-group">
                        <input type="text" id="sale-search" name="q" class="form-control"
                            value="{{ $search ?? '' }}"
                            placeholder="Search by invoice, customer, cashier, or payment method">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search mr-1"></i> Search
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-auto mt-2 mt-md-0">
                    <a href="{{ route('sales.index') }}" class="btn btn-default btn-block">
                        <i class="fas fa-rotate-left mr-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover premium-grid-table zebra-grid">
                <thead>
                    <tr>
                        <th><a href="{{ route('sales.index', array_merge(request()->query(), ['sort' => 'invoice_no', 'direction' => $sortDirection('invoice_no'), 'page' => 1])) }}"
                                data-sort-key="invoice_no" class="grid-sort-link">Invoice <i
                                    class="{{ $sortIcon('invoice_no') }}"></i></a></th>
                        <th><a href="{{ route('sales.index', array_merge(request()->query(), ['sort' => 'sale_date', 'direction' => $sortDirection('sale_date'), 'page' => 1])) }}"
                                data-sort-key="sale_date" class="grid-sort-link">Tanggal <i
                                    class="{{ $sortIcon('sale_date') }}"></i></a></th>
                        <th><a href="{{ route('sales.index', array_merge(request()->query(), ['sort' => 'customer', 'direction' => $sortDirection('customer'), 'page' => 1])) }}"
                                data-sort-key="customer" class="grid-sort-link">Pelanggan <i
                                    class="{{ $sortIcon('customer') }}"></i></a></th>
                        <th><a href="{{ route('sales.index', array_merge(request()->query(), ['sort' => 'grand_total', 'direction' => $sortDirection('grand_total'), 'page' => 1])) }}"
                                data-sort-key="grand_total" class="grid-sort-link">Total <i
                                    class="{{ $sortIcon('grand_total') }}"></i></a></th>
                        <th><a href="{{ route('sales.index', array_merge(request()->query(), ['sort' => 'paid_amount', 'direction' => $sortDirection('paid_amount'), 'page' => 1])) }}"
                                data-sort-key="paid_amount" class="grid-sort-link">Bayar <i
                                    class="{{ $sortIcon('paid_amount') }}"></i></a></th>
                        <th><a href="{{ route('sales.index', array_merge(request()->query(), ['sort' => 'change_amount', 'direction' => $sortDirection('change_amount'), 'page' => 1])) }}"
                                data-sort-key="change_amount" class="grid-sort-link">Kembalian <i
                                    class="{{ $sortIcon('change_amount') }}"></i></a>
                        </th>
                        <th class="text-nowrap">Action</th>
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
                            <td class="text-nowrap premium-action-cell">
                                <a class="btn btn-xs btn-info" href="{{ route('sales.show', $sale) }}">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>
                                <a class="btn btn-xs btn-warning" href="{{ route('sales.edit', $sale) }}">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                <form action="{{ route('sales.destroy', $sale) }}" method="POST"
                                    class="d-inline js-premium-delete-form"
                                    data-confirm-message="Delete this transaction permanently? Stock changes will be rolled back.">
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
                            <td colspan="7" class="text-center py-4">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="premium-grid-summary mb-2 mb-md-0">
                Showing {{ $sales->firstItem() ?? 0 }} to {{ $sales->lastItem() ?? 0 }} of {{ $sales->total() }}
                entries
            </div>
            <div>
                {{ $sales->links('pagination.premium-grid') }}
            </div>
        </div>
    </div>
</div>

@php
    $sortDirection = fn($column) => ($sort ?? '') === $column && ($direction ?? 'desc') === 'asc' ? 'desc' : 'asc';
    $sortIcon = fn($column) => ($sort ?? '') === $column
        ? (($direction ?? 'desc') === 'asc'
            ? 'fas fa-sort-up'
            : 'fas fa-sort-down')
        : 'fas fa-sort';
@endphp

<div class="premium-grid-page">
    <div class="card premium-dashboard-panel premium-grid-card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Transaksi Terbaru</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table premium-latest-table premium-grid-table zebra-grid mb-0">
                <thead>
                    <tr>
                        <th>
                            <a href="{{ route('dashboard', array_merge(request()->query(), ['sort' => 'invoice_no', 'direction' => $sortDirection('invoice_no')])) }}"
                                class="grid-sort-link">
                                Invoice <i class="{{ $sortIcon('invoice_no') }}"></i>
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('dashboard', array_merge(request()->query(), ['sort' => 'sale_date', 'direction' => $sortDirection('sale_date')])) }}"
                                class="grid-sort-link">
                                Tanggal <i class="{{ $sortIcon('sale_date') }}"></i>
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('dashboard', array_merge(request()->query(), ['sort' => 'customer', 'direction' => $sortDirection('customer')])) }}"
                                class="grid-sort-link">
                                Pelanggan <i class="{{ $sortIcon('customer') }}"></i>
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('dashboard', array_merge(request()->query(), ['sort' => 'cashier', 'direction' => $sortDirection('cashier')])) }}"
                                class="grid-sort-link">
                                Kasir <i class="{{ $sortIcon('cashier') }}"></i>
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('dashboard', array_merge(request()->query(), ['sort' => 'grand_total', 'direction' => $sortDirection('grand_total')])) }}"
                                class="grid-sort-link">
                                Total <i class="{{ $sortIcon('grand_total') }}"></i>
                            </a>
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($latestSales as $sale)
                        <tr>
                            <td>{{ $sale->invoice_no }}</td>
                            <td>{{ $sale->sale_date->format('d-m-Y H:i') }}</td>
                            <td>{{ $sale->customer?->name ?? '-' }}</td>
                            <td>{{ $sale->user?->name ?? '-' }}</td>
                            <td>Rp {{ number_format((float) $sale->grand_total, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('sales.show', $sale) }}"
                                    class="btn btn-xs premium-btn-info">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

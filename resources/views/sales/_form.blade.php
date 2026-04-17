@php
    $selectedItems = old('items', $sale?->items?->map(function ($item) {
        return [
            'product_id' => $item->product_id,
            'qty' => $item->qty,
            'price' => (float) $item->price,
            'discount' => (float) $item->discount,
        ];
    })->toArray() ?? []);
@endphp

<div class="card-body row">
    <div class="form-group col-md-4">
        <label>Tanggal Transaksi</label>
        <input type="datetime-local" name="sale_date" class="form-control" value="{{ old('sale_date', isset($sale) ? $sale->sale_date->format('Y-m-d\\TH:i') : now()->format('Y-m-d\\TH:i')) }}" required>
    </div>
    <div class="form-group col-md-4">
        <label>Pelanggan</label>
        <select name="customer_id" class="form-control">
            <option value="">Umum</option>
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}" @selected(old('customer_id', $sale->customer_id ?? null) == $customer->id)>{{ $customer->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-4">
        <label>Metode Pembayaran</label>
        <select name="payment_method" class="form-control" required>
            @foreach (['cash', 'transfer', 'qris', 'debit', 'credit'] as $method)
                <option value="{{ $method }}" @selected(old('payment_method', $sale->payment_method ?? 'cash') === $method)>{{ strtoupper($method) }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 mb-2">
        <h5 class="mb-2">Item Penjualan</h5>
        <table class="table table-bordered" id="sale-items-table">
            <thead>
                <tr>
                    <th style="width: 34%">Produk</th>
                    <th style="width: 14%">Qty</th>
                    <th style="width: 18%">Harga</th>
                    <th style="width: 18%">Diskon</th>
                    <th style="width: 16%"></th>
                </tr>
            </thead>
            <tbody id="sale-items-body"></tbody>
        </table>
        <button type="button" id="add-row" class="btn btn-sm btn-success">+ Tambah Baris</button>
    </div>

    <div class="form-group col-md-4">
        <label>Pajak</label>
        <input type="number" step="0.01" min="0" name="tax_total" id="tax_total" class="form-control" value="{{ old('tax_total', (float) ($sale->tax_total ?? 0)) }}">
    </div>
    <div class="form-group col-md-4">
        <label>Total Bayar</label>
        <input type="number" step="0.01" min="0" name="paid_amount" id="paid_amount" class="form-control" value="{{ old('paid_amount', (float) ($sale->paid_amount ?? 0)) }}" required>
    </div>
    <div class="form-group col-md-4">
        <label>Estimasi Total</label>
        <input type="text" id="grand_total_preview" class="form-control" value="0" readonly>
    </div>
    <div class="form-group col-md-12">
        <label>Catatan</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $sale->notes ?? '') }}</textarea>
    </div>
</div>

@push('js')
<script>
    const products = @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'price' => (float) $p->sell_price, 'stock' => $p->stock])->values());
    const selectedItems = @json($selectedItems);
    const body = document.getElementById('sale-items-body');
    const addBtn = document.getElementById('add-row');
    const taxInput = document.getElementById('tax_total');
    const previewInput = document.getElementById('grand_total_preview');

    function numberFormat(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value || 0);
    }

    function productOptions(selected) {
        let opts = '<option value="">Pilih produk</option>';
        products.forEach((product) => {
            const isSelected = Number(selected) === Number(product.id) ? 'selected' : '';
            opts += `<option value="${product.id}" data-price="${product.price}" data-stock="${product.stock}" ${isSelected}>${product.name} (stok: ${product.stock})</option>`;
        });

        return opts;
    }

    function rowTemplate(index, item = {}) {
        return `<tr>
            <td><select class="form-control" name="items[${index}][product_id]" required>${productOptions(item.product_id)}</select></td>
            <td><input type="number" min="1" class="form-control qty" name="items[${index}][qty]" value="${item.qty || 1}" required></td>
            <td><input type="number" min="0" step="0.01" class="form-control price" name="items[${index}][price]" value="${item.price || 0}" required></td>
            <td><input type="number" min="0" step="0.01" class="form-control discount" name="items[${index}][discount]" value="${item.discount || 0}"></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button></td>
        </tr>`;
    }

    function refreshTotals() {
        let subtotal = 0;
        let discountTotal = 0;

        body.querySelectorAll('tr').forEach((row) => {
            const qty = Number(row.querySelector('.qty')?.value || 0);
            const price = Number(row.querySelector('.price')?.value || 0);
            const discount = Number(row.querySelector('.discount')?.value || 0);
            subtotal += qty * price;
            discountTotal += discount;
        });

        const tax = Number(taxInput.value || 0);
        const grand = (subtotal - discountTotal) + tax;
        previewInput.value = numberFormat(grand);
    }

    function addRow(item = {}) {
        const idx = body.querySelectorAll('tr').length;
        body.insertAdjacentHTML('beforeend', rowTemplate(idx, item));
        refreshTotals();
    }

    addBtn.addEventListener('click', () => addRow());

    body.addEventListener('change', (event) => {
        if (event.target.matches('select[name$="[product_id]"]')) {
            const selected = event.target.options[event.target.selectedIndex];
            const row = event.target.closest('tr');
            const priceInput = row.querySelector('.price');
            if (selected && selected.dataset.price) {
                priceInput.value = selected.dataset.price;
            }
        }
        refreshTotals();
    });

    body.addEventListener('input', refreshTotals);

    body.addEventListener('click', (event) => {
        if (event.target.classList.contains('remove-row')) {
            event.target.closest('tr').remove();
            refreshTotals();
        }
    });

    taxInput.addEventListener('input', refreshTotals);

    if (selectedItems.length > 0) {
        selectedItems.forEach((item) => addRow(item));
    } else {
        addRow();
    }
</script>
@endpush

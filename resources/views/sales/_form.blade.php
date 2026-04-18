@php
    $sale = $sale ?? null;

    $productsForJs = $products
        ->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->sell_price,
                'stock' => $product->stock,
            ];
        })
        ->values();

    $selectedItems = old(
        'items',
        $sale?->items
            ?->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'qty' => $item->qty,
                    'price' => (float) $item->price,
                    'discount' => (float) $item->discount,
                ];
            })
            ->toArray() ?? [],
    );
@endphp

@push('css')
    <style>
        /* POS display panel */
        .pos-display {
            background: #0d1b2e;
            border-radius: 8px;
            padding: 16px 20px;
            color: #fff;
            height: 100%;
            min-height: 220px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .pos-display .pos-label {
            font-size: 11px;
            color: #8fa8c8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .pos-display .pos-grand {
            font-size: 2.4rem;
            font-weight: 700;
            color: #4fc3f7;
            line-height: 1.1;
            word-break: break-all;
        }

        .pos-display .pos-divider {
            border-color: #2a4060;
            margin: 10px 0;
        }

        .pos-display .pos-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 4px;
        }

        .pos-display .pos-row .pos-val {
            font-weight: 600;
        }

        .pos-display .pos-change {
            font-size: 1.15rem;
            font-weight: 700;
            color: #69f0ae;
        }

        .pos-display .pos-change.minus {
            color: #ff5252;
        }

        /* Indonesian formatted paid input */
        .paid-display {
            font-size: 1.05rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        @media (max-width: 767.98px) {
            .pos-display .pos-grand {
                font-size: 1.8rem;
            }

            .pos-display {
                min-height: unset;
                margin-bottom: 16px;
            }
        }
    </style>
@endpush

<div class="card-body">
    <div class="row">

        {{-- LEFT: Form fields --}}
        <div class="col-md-8">
            <div class="row">
                <div class="form-group col-12 col-sm-4">
                    <label>Tanggal Transaksi</label>
                    <input type="datetime-local" name="sale_date" class="form-control"
                        value="{{ old('sale_date', $sale?->sale_date?->format('Y-m-d\\TH:i') ?? now()->format('Y-m-d\\TH:i')) }}"
                        required>
                </div>
                <div class="form-group col-12 col-sm-4">
                    <label>Pelanggan</label>
                    <select name="customer_id" class="form-control">
                        <option value="">Umum</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(old('customer_id', $sale?->customer_id) == $customer->id)>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-sm-4">
                    <label>Metode Pembayaran</label>
                    <select name="payment_method" id="payment_method" class="form-control" required>
                        @foreach (['cash', 'transfer', 'qris', 'debit', 'credit'] as $method)
                            <option value="{{ $method }}" @selected(old('payment_method', $sale?->payment_method ?? 'cash') === $method)>
                                {{ strtoupper($method) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- RIGHT: POS Total Display --}}
        <div class="col-md-4">
            <div class="pos-display">
                <div class="pos-label">Grand Total</div>
                <div class="pos-grand" id="pos-grand-display">Rp 0</div>
                <hr class="pos-divider">
                <div class="pos-row">
                    <span class="pos-label">Subtotal</span>
                    <span class="pos-val" id="pos-subtotal">Rp 0</span>
                </div>
                <div class="pos-row">
                    <span class="pos-label">Diskon</span>
                    <span class="pos-val" id="pos-discount">Rp 0</span>
                </div>
                <div class="pos-row">
                    <span class="pos-label">Pajak</span>
                    <span class="pos-val" id="pos-tax">Rp 0</span>
                </div>
                <hr class="pos-divider">
                <div class="pos-row">
                    <span class="pos-label">Bayar</span>
                    <span class="pos-val" id="pos-paid">Rp 0</span>
                </div>
                <div class="pos-row">
                    <span class="pos-label">Kembalian</span>
                    <span class="pos-change" id="pos-change">Rp 0</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Items table --}}
    <div class="row mt-3">
        <div class="col-12">
            <h5 class="mb-2">Item Penjualan</h5>
            <div class="table-responsive">
                <table class="table table-bordered" id="sale-items-table" style="min-width:560px">
                    <thead>
                        <tr>
                            <th style="width:34%">Produk</th>
                            <th style="width:12%">Qty</th>
                            <th style="width:20%">Harga (Rp)</th>
                            <th style="width:20%">Diskon (Rp)</th>
                            <th style="width:14%"></th>
                        </tr>
                    </thead>
                    <tbody id="sale-items-body"></tbody>
                </table>
            </div>
            <button type="button" id="add-row" class="btn btn-sm btn-success mt-1">
                <i class="fas fa-plus mr-1"></i> Tambah Baris
            </button>
        </div>
    </div>

    {{-- Payment fields --}}
    <div class="row mt-3">
        <div class="form-group col-12 col-sm-4">
            <label>Pajak (Rp)</label>
            <input type="number" step="0.01" min="0" name="tax_total" id="tax_total" class="form-control"
                value="{{ old('tax_total', (float) ($sale?->tax_total ?? 0)) }}">
        </div>
        <div class="form-group col-12 col-sm-4">
            <label>Total Bayar (Rp)</label>
            {{-- Display formatted input --}}
            <input type="text" id="paid_display" class="form-control paid-display" placeholder="0"
                autocomplete="off">
            {{-- Actual value for form submission --}}
            <input type="hidden" name="paid_amount" id="paid_amount"
                value="{{ old('paid_amount', (float) ($sale?->paid_amount ?? 0)) }}">
        </div>
        <div class="form-group col-12 col-sm-4">
            <label>Catatan</label>
            <textarea name="notes" class="form-control" rows="2">{{ old('notes', $sale?->notes ?? '') }}</textarea>
        </div>
    </div>
</div>

@push('js')
    <script>
        const products = @json($productsForJs);
        const selectedItems = @json($selectedItems);
        const body = document.getElementById('sale-items-body');
        const addBtn = document.getElementById('add-row');
        const taxInput = document.getElementById('tax_total');
        const paidDisplay = document.getElementById('paid_display');
        const paidHidden = document.getElementById('paid_amount');

        // --- Number helpers ---
        function fmt(value) {
            const n = Math.round(Number(value) || 0);
            return n.toLocaleString('id-ID'); // e.g. 1.250.000
        }

        function fmtRp(value) {
            return 'Rp ' + fmt(value);
        }

        // Strip non-numeric chars then format
        function formatPaidInput(raw) {
            const numeric = raw.replace(/[^\d]/g, '');
            const n = Number(numeric) || 0;
            return n === 0 ? '' : n.toLocaleString('id-ID');
        }

        // --- Product select options ---
        function productOptions(selected) {
            let opts = '<option value="">Pilih produk</option>';
            products.forEach(p => {
                const sel = Number(selected) === Number(p.id) ? 'selected' : '';
                opts +=
                    `<option value="${p.id}" data-price="${p.price}" data-stock="${p.stock}" ${sel}>${p.name} (stok: ${p.stock})</option>`;
            });
            return opts;
        }

        // --- Row template ---
        function rowTemplate(index, item = {}) {
            return `<tr>
            <td><select class="form-control form-control-sm" name="items[${index}][product_id]" required>${productOptions(item.product_id)}</select></td>
            <td><input type="number" min="1" class="form-control form-control-sm qty" name="items[${index}][qty]" value="${item.qty || 1}" required></td>
            <td>
                <input type="text" class="form-control form-control-sm price-display" placeholder="0" autocomplete="off">
                <input type="hidden" class="price" name="items[${index}][price]" value="${item.price || 0}">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm discount-display" placeholder="0" autocomplete="off">
                <input type="hidden" class="discount" name="items[${index}][discount]" value="${item.discount || 0}">
            </td>
            <td><button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button></td>
        </tr>`;
        }

        // --- Refresh all totals + POS display ---
        function refreshTotals() {
            let subtotal = 0;
            let discountTotal = 0;

            body.querySelectorAll('tr').forEach(row => {
                const qty = Number(row.querySelector('.qty')?.value || 0);
                const price = Number(row.querySelector('.price')?.value || 0);
                const discount = Number(row.querySelector('.discount')?.value || 0);
                subtotal += qty * price;
                discountTotal += discount;
            });

            const tax = Number(taxInput.value || 0);
            const grand = (subtotal - discountTotal) + tax;
            const paid = Number(paidHidden.value || 0);
            const change = paid - grand;

            // Update POS display
            document.getElementById('pos-grand-display').textContent = fmtRp(grand);
            document.getElementById('pos-subtotal').textContent = fmtRp(subtotal);
            document.getElementById('pos-discount').textContent = fmtRp(discountTotal);
            document.getElementById('pos-tax').textContent = fmtRp(tax);
            document.getElementById('pos-paid').textContent = fmtRp(paid);

            const changeEl = document.getElementById('pos-change');
            changeEl.textContent = fmtRp(Math.abs(change));
            changeEl.classList.toggle('minus', change < 0);
        }

        // --- Paid display auto-format ---
        paidDisplay.addEventListener('input', () => {
            const raw = paidDisplay.value;
            const numeric = raw.replace(/[^\d]/g, '');
            paidHidden.value = numeric || '0';
            // reformat display (cursor jumps to end — acceptable for POS)
            paidDisplay.value = Number(numeric) > 0 ? Number(numeric).toLocaleString('id-ID') : '';
            refreshTotals();
        });

        // Initialise paid display with existing value
        const initPaid = Number(paidHidden.value || 0);
        if (initPaid > 0) {
            paidDisplay.value = initPaid.toLocaleString('id-ID');
        }

        // --- Format display input (Harga / Diskon) ---
        function initRowDisplays(row, priceVal, discountVal) {
            const pd = row.querySelector('.price-display');
            const dd = row.querySelector('.discount-display');
            if (pd && Number(priceVal) > 0) pd.value = Number(priceVal).toLocaleString('id-ID');
            if (dd && Number(discountVal) > 0) dd.value = Number(discountVal).toLocaleString('id-ID');
        }

        // --- Row add ---
        function addRow(item = {}) {
            const idx = body.querySelectorAll('tr').length;
            body.insertAdjacentHTML('beforeend', rowTemplate(idx, item));
            const lastRow = body.querySelector('tr:last-child');
            initRowDisplays(lastRow, item.price || 0, item.discount || 0);
            refreshTotals();
        }

        addBtn.addEventListener('click', () => addRow());

        // Product select → auto-fill price
        body.addEventListener('change', event => {
            if (event.target.matches('select[name$="[product_id]"]')) {
                const opt = event.target.options[event.target.selectedIndex];
                if (opt?.dataset.price) {
                    const row = event.target.closest('tr');
                    const p = Number(opt.dataset.price);
                    row.querySelector('.price').value = p;
                    row.querySelector('.price-display').value = p > 0 ? p.toLocaleString('id-ID') : '';
                }
            }
            refreshTotals();
        });

        // Auto-format Harga & Diskon inputs
        body.addEventListener('input', event => {
            const el = event.target;
            if (el.classList.contains('price-display') || el.classList.contains('discount-display')) {
                const raw = el.value.replace(/[^\d]/g, '');
                const n = Number(raw) || 0;
                const hiddenSel = el.classList.contains('price-display') ? '.price' : '.discount';
                el.closest('tr').querySelector(hiddenSel).value = n;
                el.value = n > 0 ? n.toLocaleString('id-ID') : '';
            }
            refreshTotals();
        });

        body.addEventListener('click', event => {
            if (event.target.classList.contains('remove-row')) {
                event.target.closest('tr').remove();
                refreshTotals();
            }
        });

        taxInput.addEventListener('input', refreshTotals);

        // Bootstrap rows
        if (selectedItems.length > 0) {
            selectedItems.forEach(item => addRow(item));
        } else {
            addRow();
        }
    </script>
@endpush

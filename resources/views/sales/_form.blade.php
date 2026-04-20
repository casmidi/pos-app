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

        /* ── Payment method tabs ── */
        .pm-tabs {
            display: flex;
            gap: 6px;
            margin-bottom: 10px;
        }

        .pm-tab {
            flex: 1;
            padding: 8px 4px;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            background: #fff;
            font-size: 0.8rem;
            font-weight: 700;
            color: #6c757d;
            cursor: pointer;
            text-align: center;
            transition: all 0.15s;
            line-height: 1.2;
        }

        .pm-tab:hover {
            border-color: #b45309;
            color: #b45309;
        }

        .pm-tab.active {
            background: #b45309;
            border-color: #b45309;
            color: #fff;
        }

        /* ── Paid amount display ── */
        .numpad-display-wrap {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .numpad-display-label {
            font-size: 0.78rem;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }

        .numpad-display-val {
            font-size: 1.4rem;
            font-weight: 800;
            color: #1a2236;
            text-align: right;
            flex: 1;
            letter-spacing: -0.02em;
        }

        .numpad-display-val.empty {
            color: #d1d5db;
        }

        /* ── Numpad grid ── */
        .numpad-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 7px;
        }

        .numpad-btn {
            background: #fff;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1.15rem;
            font-weight: 700;
            color: #1a2236;
            padding: 14px 8px;
            cursor: pointer;
            text-align: center;
            transition: all 0.12s;
            user-select: none;
            -webkit-user-select: none;
        }

        .numpad-btn:hover {
            background: #f3f4f6;
            border-color: #b45309;
            color: #b45309;
        }

        .numpad-btn:active {
            transform: scale(0.95);
            background: #fffbeb;
        }

        .numpad-btn.numpad-zero {
            grid-column: span 2;
        }

        .numpad-btn.numpad-del {
            background: #fef2f2;
            border-color: #fecaca;
            color: #ef4444;
            font-size: 1rem;
        }

        .numpad-btn.numpad-del:hover {
            background: #fee2e2;
        }

        .numpad-btn.numpad-clear {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #16a34a;
            font-size: 0.85rem;
            font-weight: 800;
        }

        /* ── Exact / quick-pay shortcuts ── */
        .quickpay-row {
            display: flex;
            gap: 6px;
            margin-bottom: 8px;
            flex-wrap: wrap;
        }

        .quickpay-btn {
            flex: 1 1 0;
            min-width: 60px;
            padding: 6px 4px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            font-size: 0.78rem;
            font-weight: 700;
            color: #374151;
            cursor: pointer;
            text-align: center;
            transition: all 0.12s;
        }

        .quickpay-btn:hover {
            border-color: #b45309;
            color: #b45309;
            background: #fffbeb;
        }

        .quickpay-btn.exact-btn {
            background: #fffbeb;
            border-color: #fbbf24;
            color: #b45309;
        }
    </style>
@endpush

<div class="card-body">
    <div class="row">

        {{-- LEFT: Form fields --}}
        <div class="col-12 col-md-8">
            <div class="row">
                <div class="form-group col-12 col-sm-4">
                    <label>Tanggal Transaksi</label>
                    <input type="text" name="sale_date" class="form-control"
                        value="{{ old('sale_date', $sale?->sale_date?->format('d-m-Y') ?? now()->format('d-m-Y')) }}"
                        placeholder="dd-MM-yyyy" pattern="\d{2}-\d{2}-\d{4}" title="Gunakan format dd-MM-yyyy" required>
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

                <div class="col-12 mt-2">
                    <h5 class="mb-2">Item Penjualan</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="sale-items-table">
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
        </div>

        {{-- RIGHT: POS Total Display --}}
        <div class="col-12 col-md-4">
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

    {{-- Payment fields --}}
    <div class="row mt-3">

        {{-- Pajak + Notes --}}
        <div class="col-12 col-sm-4">
            <div class="form-group">
                <label>Pajak (Rp)</label>
                <input type="number" step="0.01" min="0" name="tax_total" id="tax_total" class="form-control"
                    value="{{ old('tax_total', (float) ($sale?->tax_total ?? 0)) }}">
            </div>
            <div class="form-group">
                <label>Catatan</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $sale?->notes ?? '') }}</textarea>
            </div>
        </div>

        {{-- On-screen Numeric Keypad --}}
        <div class="col-12 col-sm-8">

            {{-- Payment method tabs --}}
            <div class="pm-tabs" id="pm-tabs">
                @foreach (['cash' => 'Cash', 'transfer' => 'Transfer', 'qris' => 'QRIS', 'debit' => 'Debit', 'credit' => 'Credit'] as $val => $label)
                    <button type="button"
                        class="pm-tab {{ old('payment_method', $sale?->payment_method ?? 'cash') === $val ? 'active' : '' }}"
                        data-method="{{ $val }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <input type="hidden" name="payment_method" id="payment_method"
                value="{{ old('payment_method', $sale?->payment_method ?? 'cash') }}">

            {{-- Amount display --}}
            <div class="numpad-display-wrap">
                <span class="numpad-display-label">Bayar</span>
                <div class="numpad-display-val empty" id="numpad-display">0</div>
                <input type="hidden" name="paid_amount" id="paid_amount"
                    value="{{ old('paid_amount', (float) ($sale?->paid_amount ?? 0)) }}">
            </div>

            {{-- Quick-pay shortcuts --}}
            <div class="quickpay-row" id="quickpay-row">
                <button type="button" class="quickpay-btn exact-btn" id="btn-exact">Exact</button>
                <button type="button" class="quickpay-btn" data-add="5000">+5rb</button>
                <button type="button" class="quickpay-btn" data-add="10000">+10rb</button>
                <button type="button" class="quickpay-btn" data-add="20000">+20rb</button>
                <button type="button" class="quickpay-btn" data-add="50000">+50rb</button>
                <button type="button" class="quickpay-btn" data-add="100000">+100rb</button>
            </div>

            {{-- Numeric Keypad --}}
            <div class="numpad-grid" id="numpad-grid">
                <button type="button" class="numpad-btn" data-digit="1">1</button>
                <button type="button" class="numpad-btn" data-digit="2">2</button>
                <button type="button" class="numpad-btn" data-digit="3">3</button>
                <button type="button" class="numpad-btn" data-digit="4">4</button>
                <button type="button" class="numpad-btn" data-digit="5">5</button>
                <button type="button" class="numpad-btn" data-digit="6">6</button>
                <button type="button" class="numpad-btn" data-digit="7">7</button>
                <button type="button" class="numpad-btn" data-digit="8">8</button>
                <button type="button" class="numpad-btn" data-digit="9">9</button>
                <button type="button" class="numpad-btn numpad-clear" id="numpad-clear">CLR</button>
                <button type="button" class="numpad-btn numpad-zero" data-digit="0">0</button>
                <button type="button" class="numpad-btn numpad-del" id="numpad-del">
                    <i class="fas fa-backspace"></i>
                </button>
            </div>

        </div>{{-- /col --}}
    </div>{{-- /row --}}
</div>

@push('js')
    <script>
        const products = @json($productsForJs);
        const selectedItems = @json($selectedItems);
        const body = document.getElementById('sale-items-body');
        const addBtn = document.getElementById('add-row');
        const taxInput = document.getElementById('tax_total');
        const paidHidden = document.getElementById('paid_amount');
        const numpadDisplay = document.getElementById('numpad-display');

        // ── Number helpers ──
        function fmt(v) { return Math.round(Number(v) || 0).toLocaleString('id-ID'); }
        function fmtRp(v) { return 'Rp ' + fmt(v); }

        // ── Numpad state ──
        let numpadRaw = String(Number(paidHidden.value || 0) || '');

        function numpadSync() {
            const n = Number(numpadRaw) || 0;
            paidHidden.value = n;
            if (n > 0) {
                numpadDisplay.textContent = n.toLocaleString('id-ID');
                numpadDisplay.classList.remove('empty');
            } else {
                numpadDisplay.textContent = '0';
                numpadDisplay.classList.add('empty');
            }
            refreshTotals();
        }

        // Init from old value
        if (Number(paidHidden.value) > 0) {
            numpadRaw = paidHidden.value;
            numpadSync();
        }

        // Digit buttons
        document.getElementById('numpad-grid').addEventListener('click', function(e) {
            const btn = e.target.closest('.numpad-btn');
            if (!btn) return;
            if (btn.dataset.digit !== undefined) {
                if (numpadRaw === '0' || numpadRaw === '') {
                    numpadRaw = btn.dataset.digit;
                } else {
                    if (numpadRaw.replace(/^-/, '').length < 12) {
                        numpadRaw += btn.dataset.digit;
                    }
                }
                numpadSync();
            }
        });

        // Delete (backspace)
        document.getElementById('numpad-del').addEventListener('click', () => {
            numpadRaw = numpadRaw.slice(0, -1) || '0';
            numpadSync();
        });

        // Clear
        document.getElementById('numpad-clear').addEventListener('click', () => {
            numpadRaw = '0';
            numpadSync();
        });

        // Exact button (set paid = grand total)
        document.getElementById('btn-exact').addEventListener('click', () => {
            const grand = getCurrentGrand();
            numpadRaw = String(grand);
            numpadSync();
        });

        // Quick-pay shortcuts (add amount)
        document.getElementById('quickpay-row').addEventListener('click', function(e) {
            const btn = e.target.closest('.quickpay-btn[data-add]');
            if (!btn) return;
            const add = Number(btn.dataset.add);
            const cur = Number(numpadRaw) || 0;
            numpadRaw = String(cur + add);
            numpadSync();
        });

        // ── Payment method tabs ──
        document.getElementById('pm-tabs').addEventListener('click', function(e) {
            const tab = e.target.closest('.pm-tab');
            if (!tab) return;
            this.querySelectorAll('.pm-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            document.getElementById('payment_method').value = tab.dataset.method;
        });

        // ── Product select options ──
        function productOptions(selected) {
            let opts = '<option value="">Pilih produk</option>';
            products.forEach(p => {
                const sel = Number(selected) === Number(p.id) ? 'selected' : '';
                opts += `<option value="${p.id}" data-price="${p.price}" data-stock="${p.stock}" ${sel}>${p.name} (stok: ${p.stock})</option>`;
            });
            return opts;
        }

        // ── Row template ──
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

        // ── Totals ──
        function getCurrentGrand() {
            let subtotal = 0, discountTotal = 0;
            body.querySelectorAll('tr').forEach(row => {
                subtotal      += Number(row.querySelector('.qty')?.value || 0) * Number(row.querySelector('.price')?.value || 0);
                discountTotal += Number(row.querySelector('.discount')?.value || 0);
            });
            return (subtotal - discountTotal) + Number(taxInput.value || 0);
        }

        function refreshTotals() {
            let subtotal = 0, discountTotal = 0;
            body.querySelectorAll('tr').forEach(row => {
                const qty      = Number(row.querySelector('.qty')?.value || 0);
                const price    = Number(row.querySelector('.price')?.value || 0);
                const discount = Number(row.querySelector('.discount')?.value || 0);
                subtotal      += qty * price;
                discountTotal += discount;
            });
            const tax    = Number(taxInput.value || 0);
            const grand  = (subtotal - discountTotal) + tax;
            const paid   = Number(paidHidden.value || 0);
            const change = paid - grand;

            document.getElementById('pos-grand-display').textContent = fmtRp(grand);
            document.getElementById('pos-subtotal').textContent      = fmtRp(subtotal);
            document.getElementById('pos-discount').textContent      = fmtRp(discountTotal);
            document.getElementById('pos-tax').textContent           = fmtRp(tax);
            document.getElementById('pos-paid').textContent          = fmtRp(paid);

            const changeEl = document.getElementById('pos-change');
            changeEl.textContent = fmtRp(Math.abs(change));
            changeEl.classList.toggle('minus', change < 0);
        }

        // ── Row add ──
        function initRowDisplays(row, priceVal, discountVal) {
            const pd = row.querySelector('.price-display');
            const dd = row.querySelector('.discount-display');
            if (pd && Number(priceVal) > 0) pd.value = Number(priceVal).toLocaleString('id-ID');
            if (dd && Number(discountVal) > 0) dd.value = Number(discountVal).toLocaleString('id-ID');
        }

        function addRow(item = {}) {
            const idx = body.querySelectorAll('tr').length;
            body.insertAdjacentHTML('beforeend', rowTemplate(idx, item));
            const lastRow = body.querySelector('tr:last-child');
            initRowDisplays(lastRow, item.price || 0, item.discount || 0);
            refreshTotals();
        }

        addBtn.addEventListener('click', () => addRow());

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

        // ── Bootstrap rows ──
        if (selectedItems.length > 0) {
            selectedItems.forEach(item => addRow(item));
        } else {
            addRow();
        }
    </script>
@endpush

<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): View|JsonResponse|Response
    {
        $search = trim((string) $request->string('q'));
        $allowedSorts = [
            'invoice_no' => 'invoice_no',
            'sale_date' => 'sale_date',
            'customer' => 'customer',
            'grand_total' => 'grand_total',
            'paid_amount' => 'paid_amount',
            'change_amount' => 'change_amount',
        ];
        $sort = $request->string('sort')->toString();
        $direction = strtolower($request->string('direction')->toString()) === 'asc' ? 'asc' : 'desc';

        if (! array_key_exists($sort, $allowedSorts)) {
            $sort = 'sale_date';
            $direction = 'desc';
        }

        $amountSorts = ['grand_total', 'paid_amount', 'change_amount'];

        $sales = Sale::query()
            ->with(['customer', 'user'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nestedQuery) use ($search) {
                    $nestedQuery->where('invoice_no', 'like', "%{$search}%")
                        ->orWhere('payment_method', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($sort === 'customer', function ($query) use ($direction) {
                $query->leftJoin('customers as sort_customers', 'sort_customers.id', '=', 'sales.customer_id')
                    ->select('sales.*')
                    ->orderBy('sort_customers.name', $direction)
                    ->orderBy('sales.id', 'desc');
            }, function ($query) use ($sort, $direction, $amountSorts) {
                if (in_array($sort, $amountSorts, true)) {
                    $query->orderBy('sales.' . $sort, $direction)
                        ->orderBy('sales.id', 'desc');

                    return;
                }

                if ($sort === 'sale_date') {
                    $query->orderBy('sales.sale_date', $direction)
                        ->orderBy('sales.id', 'desc');

                    return;
                }

                if ($sort === 'invoice_no') {
                    $query->orderBy('sales.invoice_no', $direction)
                        ->orderBy('sales.id', 'desc');

                    return;
                }

                $query->orderBy('sales.' . $sort, $direction)
                    ->orderBy('sales.id', 'desc');
            })
            ->paginate(10)
            ->withQueryString();

        $viewData = compact('sales', 'search', 'sort', 'direction');

        if ($this->isGridAsyncRequest($request)) {
            return response()->view('sales.partials.grid', $viewData);
        }

        return $this->respond($request, 'sales.index', $viewData, $sales);
    }

    public function create(): View
    {
        $products = Product::query()->where('is_active', true)->orderBy('name')->get();
        $customers = Customer::query()->orderBy('name')->get();

        return view('sales.create', compact('products', 'customers'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $this->validateSaleRequest($request);

        $sale = DB::transaction(function () use ($validated): Sale {
            $totals = $this->buildTotals($validated['items'], (float) ($validated['tax_total'] ?? 0));

            $sale = Sale::query()->create([
                'invoice_no' => $this->generateInvoiceNo(),
                'sale_date' => $validated['sale_date'],
                'user_id' => $this->resolveCashierUserId(),
                'customer_id' => $validated['customer_id'] ?? null,
                'subtotal' => $totals['subtotal'],
                'discount_total' => $totals['discount_total'],
                'tax_total' => $totals['tax_total'],
                'grand_total' => $totals['grand_total'],
                'paid_amount' => $validated['paid_amount'],
                'change_amount' => max(0, (float) $validated['paid_amount'] - $totals['grand_total']),
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $this->syncSaleItemsAndStock($sale, $validated['items'], false);

            return $sale;
        });

        return $this->respondAfterMutation(
            $request,
            'sales.index',
            'Transaksi berhasil dibuat.',
            $sale->load(['customer', 'user', 'items.product']),
            201,
        );
    }

    public function show(Request $request, Sale $sale): View|JsonResponse
    {
        $sale->load(['customer', 'user', 'items.product']);

        return $this->respond($request, 'sales.show', compact('sale'), $sale);
    }

    public function edit(Sale $sale): View
    {
        $sale->load('items');
        $products = Product::query()->where('is_active', true)->orderBy('name')->get();
        $customers = Customer::query()->orderBy('name')->get();

        return view('sales.edit', compact('sale', 'products', 'customers'));
    }

    public function update(Request $request, Sale $sale): RedirectResponse|JsonResponse
    {
        $validated = $this->validateSaleRequest($request);

        DB::transaction(function () use ($sale, $validated): void {
            $totals = $this->buildTotals($validated['items'], (float) ($validated['tax_total'] ?? 0));

            $sale->update([
                'sale_date' => $validated['sale_date'],
                'customer_id' => $validated['customer_id'] ?? null,
                'subtotal' => $totals['subtotal'],
                'discount_total' => $totals['discount_total'],
                'tax_total' => $totals['tax_total'],
                'grand_total' => $totals['grand_total'],
                'paid_amount' => $validated['paid_amount'],
                'change_amount' => max(0, (float) $validated['paid_amount'] - $totals['grand_total']),
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $this->syncSaleItemsAndStock($sale, $validated['items'], true);
        });

        return $this->respondAfterMutation(
            $request,
            'sales.index',
            'Transaksi berhasil diperbarui.',
            $sale->fresh()->load(['customer', 'user', 'items.product']),
        );
    }

    public function destroy(Request $request, Sale $sale): RedirectResponse|JsonResponse
    {
        DB::transaction(function () use ($sale): void {
            $sale->load('items.product');

            foreach ($sale->items as $item) {
                $product = $item->product;
                if (! $product) {
                    continue;
                }

                $qtyBefore = $product->stock;
                $qtyAfter = $qtyBefore + $item->qty;

                $product->update(['stock' => $qtyAfter]);

                StockMovement::query()->create([
                    'product_id' => $product->id,
                    'type' => 'sale_deleted',
                    'qty_before' => $qtyBefore,
                    'qty_change' => $item->qty,
                    'qty_after' => $qtyAfter,
                    'reference_type' => 'sale',
                    'reference_id' => $sale->id,
                    'notes' => 'Stok dikembalikan karena transaksi dihapus',
                    'moved_at' => now(),
                ]);
            }

            $sale->delete();
        });

        return $this->respondAfterMutation(
            $request,
            'sales.index',
            'Transaksi berhasil dihapus.',
            ['message' => 'Sale deleted'],
        );
    }

    private function validateSaleRequest(Request $request): array
    {
        return $request->validate([
            'sale_date' => ['required', 'date'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'payment_method' => ['required', 'in:cash,transfer,qris,debit,credit'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'tax_total' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
        ]);
    }

    private function buildTotals(array $items, float $taxTotal): array
    {
        $subtotal = 0;
        $discountTotal = 0;

        foreach ($items as $item) {
            $qty = (int) $item['qty'];
            $price = (float) $item['price'];
            $discount = (float) ($item['discount'] ?? 0);
            $lineSubtotal = $qty * $price;

            $subtotal += $lineSubtotal;
            $discountTotal += $discount;
        }

        $grandTotal = ($subtotal - $discountTotal) + $taxTotal;

        return [
            'subtotal' => $subtotal,
            'discount_total' => $discountTotal,
            'tax_total' => $taxTotal,
            'grand_total' => $grandTotal,
        ];
    }

    private function syncSaleItemsAndStock(Sale $sale, array $items, bool $isUpdate): void
    {
        $sale->load('items.product');

        if ($isUpdate) {
            foreach ($sale->items as $existing) {
                $product = $existing->product;
                if (! $product) {
                    continue;
                }

                $qtyBefore = $product->stock;
                $qtyAfter = $qtyBefore + $existing->qty;

                $product->update(['stock' => $qtyAfter]);

                StockMovement::query()->create([
                    'product_id' => $product->id,
                    'type' => 'sale_updated_revert',
                    'qty_before' => $qtyBefore,
                    'qty_change' => $existing->qty,
                    'qty_after' => $qtyAfter,
                    'reference_type' => 'sale',
                    'reference_id' => $sale->id,
                    'notes' => 'Rollback stok sebelum update transaksi',
                    'moved_at' => now(),
                ]);
            }

            $sale->items()->delete();
        }

        foreach ($items as $item) {
            $product = Product::query()->lockForUpdate()->findOrFail($item['product_id']);
            $qty = (int) $item['qty'];
            $price = (float) $item['price'];
            $discount = (float) ($item['discount'] ?? 0);

            if ($product->stock < $qty) {
                abort(422, 'Stok produk ' . $product->name . ' tidak cukup.');
            }

            $lineTotal = ($qty * $price) - $discount;

            $sale->items()->create([
                'product_id' => $product->id,
                'qty' => $qty,
                'price' => $price,
                'discount' => $discount,
                'line_total' => $lineTotal,
            ]);

            $qtyBefore = $product->stock;
            $qtyAfter = $qtyBefore - $qty;

            $product->update(['stock' => $qtyAfter]);

            StockMovement::query()->create([
                'product_id' => $product->id,
                'type' => $isUpdate ? 'sale_updated' : 'sale_created',
                'qty_before' => $qtyBefore,
                'qty_change' => -$qty,
                'qty_after' => $qtyAfter,
                'reference_type' => 'sale',
                'reference_id' => $sale->id,
                'notes' => 'Pengurangan stok dari transaksi penjualan',
                'moved_at' => now(),
            ]);
        }
    }

    private function resolveCashierUserId(): int
    {
        $user = User::query()->first();

        if (! $user) {
            $user = User::query()->create([
                'name' => 'Admin POS',
                'email' => 'admin@pos.local',
                'password' => bcrypt('password'),
            ]);
        }

        return $user->id;
    }

    private function generateInvoiceNo(): string
    {
        return 'INV-' . now()->format('Ymd-His') . '-' . random_int(100, 999);
    }
}

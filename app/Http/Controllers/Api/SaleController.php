<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::query()->with(['customer', 'user', 'items.product'])->latest('sale_date')->paginate(10);

        return response()->json($sales);
    }

    public function store(Request $request)
    {
        $validated = $this->validateSaleRequest($request);

        $sale = DB::transaction(function () use ($validated) {
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

        return response()->json($sale->load(['customer', 'user', 'items.product']), 201);
    }

    public function show(Sale $sale)
    {
        return response()->json($sale->load(['customer', 'user', 'items.product']));
    }

    public function update(Request $request, Sale $sale)
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

        return response()->json($sale->fresh()->load(['customer', 'user', 'items.product']));
    }

    public function destroy(Sale $sale)
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

        return response()->json(['message' => 'Sale deleted']);
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

            $subtotal += $qty * $price;
            $discountTotal += $discount;
        }

        return [
            'subtotal' => $subtotal,
            'discount_total' => $discountTotal,
            'tax_total' => $taxTotal,
            'grand_total' => ($subtotal - $discountTotal) + $taxTotal,
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

            $sale->items()->create([
                'product_id' => $product->id,
                'qty' => $qty,
                'price' => $price,
                'discount' => $discount,
                'line_total' => ($qty * $price) - $discount,
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

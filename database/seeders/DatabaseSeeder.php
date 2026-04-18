<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $users = collect();

            $users->push(User::updateOrCreate(
                ['email' => 'admin@pos.local'],
                [
                    'name' => 'Administrator',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            ));

            for ($i = 1; $i <= 9; $i++) {
                $users->push(User::updateOrCreate(
                    ['email' => "kasir{$i}@pos.local"],
                    [
                        'name' => "Kasir {$i}",
                        'password' => Hash::make('password'),
                        'email_verified_at' => now(),
                    ]
                ));
            }

            $categorySeed = [
                ['name' => 'Makanan Ringan', 'description' => 'Snack kemasan dan makanan ringan'],
                ['name' => 'Minuman', 'description' => 'Minuman botol, kaleng, dan sachet'],
                ['name' => 'Sembako', 'description' => 'Kebutuhan pokok harian'],
                ['name' => 'Perawatan Diri', 'description' => 'Produk kebersihan dan perawatan tubuh'],
                ['name' => 'Kebersihan Rumah', 'description' => 'Pembersih rumah tangga'],
                ['name' => 'ATK', 'description' => 'Alat tulis kantor dan sekolah'],
                ['name' => 'Frozen Food', 'description' => 'Produk makanan beku'],
                ['name' => 'Bumbu Dapur', 'description' => 'Bumbu masak dan pelengkap dapur'],
                ['name' => 'Kopi dan Teh', 'description' => 'Minuman kopi dan teh'],
                ['name' => 'Lainnya', 'description' => 'Kategori campuran untuk kebutuhan umum'],
            ];

            $categories = collect();
            foreach ($categorySeed as $category) {
                $categories->push(Category::updateOrCreate(
                    ['name' => $category['name']],
                    ['description' => $category['description']]
                ));
            }

            $productSeed = [
                ['sku' => 'PRD0001', 'name' => 'Keripik Singkong Balado', 'category' => 'Makanan Ringan', 'cost' => 6500, 'sell' => 9000, 'stock' => 140],
                ['sku' => 'PRD0002', 'name' => 'Biskuit Cokelat', 'category' => 'Makanan Ringan', 'cost' => 7000, 'sell' => 10000, 'stock' => 130],
                ['sku' => 'PRD0003', 'name' => 'Air Mineral 600ml', 'category' => 'Minuman', 'cost' => 2500, 'sell' => 4000, 'stock' => 200],
                ['sku' => 'PRD0004', 'name' => 'Teh Botol 450ml', 'category' => 'Minuman', 'cost' => 4200, 'sell' => 6500, 'stock' => 180],
                ['sku' => 'PRD0005', 'name' => 'Beras Premium 5kg', 'category' => 'Sembako', 'cost' => 64000, 'sell' => 72000, 'stock' => 60],
                ['sku' => 'PRD0006', 'name' => 'Gula Pasir 1kg', 'category' => 'Sembako', 'cost' => 14500, 'sell' => 17000, 'stock' => 120],
                ['sku' => 'PRD0007', 'name' => 'Sabun Cuci Piring 800ml', 'category' => 'Kebersihan Rumah', 'cost' => 13500, 'sell' => 17500, 'stock' => 95],
                ['sku' => 'PRD0008', 'name' => 'Shampoo 170ml', 'category' => 'Perawatan Diri', 'cost' => 18500, 'sell' => 24000, 'stock' => 100],
                ['sku' => 'PRD0009', 'name' => 'Pulpen Gel Hitam', 'category' => 'ATK', 'cost' => 2500, 'sell' => 5000, 'stock' => 220],
                ['sku' => 'PRD0010', 'name' => 'Kopi Sachet Premium', 'category' => 'Kopi dan Teh', 'cost' => 1800, 'sell' => 3000, 'stock' => 260],
            ];

            $products = collect();
            foreach ($productSeed as $productData) {
                $category = $categories->firstWhere('name', $productData['category']);

                $products->push(Product::updateOrCreate(
                    ['sku' => $productData['sku']],
                    [
                        'name' => $productData['name'],
                        'category_id' => $category?->id,
                        'cost_price' => $productData['cost'],
                        'sell_price' => $productData['sell'],
                        'stock' => $productData['stock'],
                        'is_active' => true,
                        'description' => 'Produk seed untuk simulasi laporan bulanan',
                    ]
                ));
            }

            $customerNames = [
                'Andi Saputra',
                'Budi Santoso',
                'Citra Lestari',
                'Dewi Anggraini',
                'Eka Pratama',
                'Fajar Ramadhan',
                'Gita Maharani',
                'Hadi Wijaya',
                'Intan Permata',
                'Joko Nugroho',
            ];

            $customers = collect();
            for ($i = 1; $i <= 10; $i++) {
                $customers->push(Customer::updateOrCreate(
                    ['email' => "customer{$i}@pos.local"],
                    [
                        'name' => $customerNames[$i - 1],
                        'phone' => '08' . str_pad((string) (1100000000 + $i), 10, '0', STR_PAD_LEFT),
                        'address' => "Jl. Contoh No. {$i}, Kota Bandung",
                    ]
                ));
            }

            $monthsBack = 6;
            $salesPerMonth = 10;

            for ($monthOffset = $monthsBack - 1; $monthOffset >= 0; $monthOffset--) {
                $period = Carbon::now()->startOfMonth()->subMonths($monthOffset);

                for ($seq = 1; $seq <= $salesPerMonth; $seq++) {
                    $saleDate = $period->copy()->day(min($seq * 2, (int) $period->copy()->endOfMonth()->format('d')))
                        ->setTime(9 + ($seq % 9), 10 + (($seq * 5) % 40));

                    $user = $users[($seq + $monthOffset) % $users->count()];
                    $customer = $customers[($seq + $monthOffset) % $customers->count()];
                    $paymentMethod = $seq % 3 === 0 ? 'qris' : ($seq % 2 === 0 ? 'transfer' : 'cash');

                    $selectedProducts = $products->shuffle()->take(2 + ($seq % 2))->values();

                    $subtotal = 0;
                    $discountTotal = 0;
                    $itemPayloads = [];

                    foreach ($selectedProducts as $index => $selectedProduct) {
                        $product = $selectedProduct->fresh();
                        $qty = 1 + (($seq + $index + $monthOffset) % 3);
                        $price = (float) $product->sell_price;
                        $itemSubtotal = $qty * $price;
                        $itemDiscount = (($seq + $index) % 4 === 0) ? round($itemSubtotal * 0.05, 2) : 0;
                        $lineTotal = $itemSubtotal - $itemDiscount;

                        $subtotal += $itemSubtotal;
                        $discountTotal += $itemDiscount;

                        $itemPayloads[] = [
                            'product' => $product,
                            'qty' => $qty,
                            'price' => $price,
                            'discount' => $itemDiscount,
                            'line_total' => $lineTotal,
                        ];
                    }

                    $taxTotal = round(($subtotal - $discountTotal) * 0.11, 2);
                    $grandTotal = ($subtotal - $discountTotal) + $taxTotal;
                    $paidAmount = $paymentMethod === 'cash'
                        ? ceil($grandTotal / 1000) * 1000
                        : $grandTotal;
                    $changeAmount = $paidAmount - $grandTotal;

                    $invoiceNo = sprintf('INV%s%03d', $period->format('Ym'), $seq);

                    $sale = Sale::updateOrCreate(
                        ['invoice_no' => $invoiceNo],
                        [
                            'sale_date' => $saleDate,
                            'user_id' => $user->id,
                            'customer_id' => $customer->id,
                            'subtotal' => $subtotal,
                            'discount_total' => $discountTotal,
                            'tax_total' => $taxTotal,
                            'grand_total' => $grandTotal,
                            'paid_amount' => $paidAmount,
                            'change_amount' => $changeAmount,
                            'payment_method' => $paymentMethod,
                            'notes' => 'Transaksi seed untuk laporan penjualan bulanan',
                        ]
                    );

                    foreach ($itemPayloads as $item) {
                        SaleItem::updateOrCreate(
                            [
                                'sale_id' => $sale->id,
                                'product_id' => $item['product']->id,
                            ],
                            [
                                'qty' => $item['qty'],
                                'price' => $item['price'],
                                'discount' => $item['discount'],
                                'line_total' => $item['line_total'],
                            ]
                        );

                        $freshProduct = $item['product']->fresh();
                        $qtyBefore = (int) $freshProduct->stock;
                        $qtyAfter = max(0, $qtyBefore - $item['qty']);
                        $freshProduct->update(['stock' => $qtyAfter]);

                        StockMovement::updateOrCreate(
                            [
                                'product_id' => $freshProduct->id,
                                'type' => 'sale',
                                'reference_type' => Sale::class,
                                'reference_id' => $sale->id,
                            ],
                            [
                                'qty_before' => $qtyBefore,
                                'qty_change' => -$item['qty'],
                                'qty_after' => $qtyAfter,
                                'notes' => "Pengurangan stok dari {$sale->invoice_no}",
                                'moved_at' => $saleDate,
                            ]
                        );
                    }
                }
            }
        });
    }
}

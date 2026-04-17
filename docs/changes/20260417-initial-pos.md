# Dokumentasi Perubahan 2026-04-17

## Ringkasan
Inisialisasi aplikasi Point of Sales berbasis Laravel dengan database PostgreSQL, CRUD Web + API, serta antarmuka AdminLTE.

## Perubahan Utama
- Menambahkan dependensi AdminLTE: jeroennoten/laravel-adminlte.
- Menambahkan skema database POS:
  - categories
  - products
  - customers
  - sales
  - sale_items
  - stock_movements
- Menambahkan model Eloquent beserta relasi:
  - Category, Product, Customer, Sale, SaleItem, StockMovement
  - Relasi sales pada model User
- Menambahkan controller Web:
  - DashboardController
  - CategoryController
  - ProductController
  - CustomerController
  - SaleController
- Menambahkan controller API:
  - Api/CategoryController
  - Api/ProductController
  - Api/CustomerController
  - Api/SaleController
- Menambahkan route Web resource dan route API resource.
- Menambahkan tampilan AdminLTE untuk dashboard dan seluruh halaman CRUD.
- Menambahkan logika transaksi penjualan:
  - hitung subtotal, diskon, pajak, grand total
  - simpan item transaksi
  - update stok produk
  - catat stock movement
- Menambahkan guard pada dashboard agar aman saat tabel belum tersedia.
- Memperbaiki test feature dengan RefreshDatabase.

## Endpoint API
- GET/POST/PUT/DELETE /api/categories
- GET/POST/PUT/DELETE /api/products
- GET/POST/PUT/DELETE /api/customers
- GET/POST/PUT/DELETE /api/sales

## Catatan
- Migrasi ke PostgreSQL membutuhkan ekstensi php_pgsql aktif di environment.
- Pengujian lokal menggunakan sqlite memory pada phpunit.xml.

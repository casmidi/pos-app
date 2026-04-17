# Dokumentasi Perbaikan Menu Web 2026-04-17

## Ringkasan
Perbaikan error semua menu web POS yang sebelumnya mengarah ke endpoint API (JSON) alih-alih halaman web CRUD.

## Akar Masalah
- Nama route API dan route web bentrok karena keduanya menggunakan nama yang sama.
- Contoh bentrok: `sales.index`, `products.index`, `categories.index`, `customers.index`.
- Akibatnya, helper route di menu bisa mengarah ke `/api/...`.

## Perubahan
- Menambahkan prefix nama route API menjadi `api.*` di `routes/api.php`.
- Contoh hasil baru:
  - `api.sales.index`
  - `api.products.index`
  - `api.categories.index`
  - `api.customers.index`
- Route web tetap menggunakan nama standar:
  - `sales.index`
  - `products.index`
  - `categories.index`
  - `customers.index`

## Hasil
- Menu sidebar kembali mengarah ke halaman web CRUD yang benar.
- Endpoint API tetap tersedia tanpa mengganggu route web.

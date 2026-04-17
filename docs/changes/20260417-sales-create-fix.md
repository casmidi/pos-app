# Dokumentasi Perbaikan Sales Create 2026-04-17

## Ringkasan
Perbaikan error 500 pada halaman tambah transaksi (`/sales/create`).

## Akar Masalah
- Blade parse error pada serialisasi data produk ke JavaScript.
- Variabel `$sale` dipakai langsung pada mode create sehingga memicu undefined variable/property access.

## Perubahan
- Menyiapkan payload produk untuk JavaScript di blok PHP (`$productsForJs`) lalu dirender dengan `@json($productsForJs)`.
- Menormalkan variabel `$sale` di partial form (`$sale = $sale ?? null`).
- Mengganti akses properti `$sale` menjadi null-safe (`$sale?->...`) untuk mode create/edit yang memakai partial sama.

## Hasil Verifikasi
- `GET /sales/create` sekarang mengembalikan HTTP 200.
- Test aplikasi tetap lulus.

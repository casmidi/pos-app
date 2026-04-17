# Dokumentasi Perubahan Port 2026-04-17

## Ringkasan
Penyesuaian port aplikasi karena port 8000 sudah digunakan.

## Perubahan
- Backend Laravel (`php artisan serve`) diubah ke `127.0.0.1:8001`.
- Frontend Vite dev server diubah ke `127.0.0.1:8002`.
- `APP_URL` pada environment lokal diubah ke `http://127.0.0.1:8001`.

## Dampak
- Akses aplikasi web utama: `http://127.0.0.1:8001`
- Akses asset/HMR Vite saat development: `http://127.0.0.1:8002`

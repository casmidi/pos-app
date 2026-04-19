# Laravel Autostart di Windows (Quantum + POS)

Dokumentasi ini menjelaskan dua mode autostart agar dua aplikasi Laravel berikut otomatis hidup:

- Quantum: D:\laravel\quantum pada port 8001
- POS App: D:\laravel\pos-app pada port 8002

## Mode yang Tersedia

### 1) Startup Folder Mode (User Login)

- Berjalan setelah user login.
- Tidak butuh hak Administrator.
- Menggunakan file BAT di Startup folder.

### 2) Windows Service Mode (Direkomendasikan)

- Berjalan sebagai service Windows (`StartType: Automatic`).
- Membutuhkan hak Administrator saat instalasi/uninstall service.
- Tidak tergantung Startup folder user.

## Ringkasan Arsitektur Startup Folder

Autostart menggunakan mekanisme Startup Folder Windows:

1. Windows login menjalankan file BAT di Startup folder.
2. File BAT memanggil script PowerShell utama.
3. Script PowerShell:
   - validasi executable PHP
   - membersihkan proses lama per port
   - membersihkan listener lama per port
   - menjalankan `php artisan serve` untuk kedua aplikasi

## File Startup Folder Mode

- Script utama: D:\laravel\start-laravel-apps.ps1
- Startup launcher: C:\Users\midip\AppData\Roaming\Microsoft\Windows\Start Menu\Programs\Startup\laravel-apps-autostart.bat

## File Windows Service Mode

- Runner Quantum: D:\laravel\services\quantum-service.ps1
- Runner POS: D:\laravel\services\pos-service.ps1
- Installer service: D:\laravel\services\install-laravel-services.ps1
- Uninstaller service: D:\laravel\services\uninstall-laravel-services.ps1

Fallback tunnel (non-admin) yang sudah disiapkan:

- Ensure tunnel script: D:\laravel\services\ensure-cloudflared-tunnel.ps1
- Startup launcher tunnel: C:\Users\midip\AppData\Roaming\Microsoft\Windows\Start Menu\Programs\Startup\cloudflared-tunnel-autostart.bat

Service name yang digunakan:

- LaravelQuantumService
- LaravelPosService

## Isi Script Utama

Script utama menjalankan:

- Quantum: `php artisan serve --host=127.0.0.1 --port=8001`
- POS App: `php artisan serve --host=127.0.0.1 --port=8002`

Jika `C:\xampp\php\php.exe` tidak ditemukan, script akan fallback ke `php` dari PATH.

## Instalasi Windows Service Mode

Jalankan PowerShell sebagai Administrator, lalu:

```powershell
& 'D:\laravel\services\install-laravel-services.ps1'
```

Verifikasi service:

```powershell
Get-Service -Name LaravelQuantumService,LaravelPosService | Select-Object Name,Status,StartType
```

## Uninstall Windows Service Mode

Jalankan PowerShell sebagai Administrator, lalu:

```powershell
& 'D:\laravel\services\uninstall-laravel-services.ps1'
```

## Verifikasi Runtime Manual

Jalankan command berikut di PowerShell:

```powershell
& 'D:\laravel\start-laravel-apps.ps1'
Get-NetTCPConnection -State Listen -ErrorAction SilentlyContinue |
  Where-Object { $_.LocalPort -in 8001,8002 } |
  Select-Object LocalAddress,LocalPort,OwningProcess,State
```

Jika sukses, output akan menampilkan listener:

- 127.0.0.1:8001
- 127.0.0.1:8002

## Operasional Harian

- Tidak perlu lagi menjalankan manual `php artisan serve` setelah restart.
- Jika memakai Service Mode, proses akan dikelola oleh Service Control Manager.
- Jika memakai Startup Folder Mode, proses berjalan saat user login.
- Cloudflared tetap mengarah ke localhost sesuai konfigurasi tunnel Anda.

## Troubleshooting

### 1) Salah satu port tidak listen

Jalankan ulang script:

```powershell
& 'D:\laravel\start-laravel-apps.ps1'
```

Lalu cek listener lagi dengan command verifikasi.

### 1b) Domain publik error 530 / 1033

Gejala umum:

- `quantum.or.id` atau `pos.quantum.or.id` mengembalikan status 530.
- Body dari Cloudflare menampilkan `error code: 1033`.

Penyebab umum:

- Service `Cloudflared` berjalan tanpa argumen config tunnel yang benar.

Recovery cepat (non-admin):

- Jalankan script: `D:\laravel\services\ensure-cloudflared-tunnel.ps1`

Perbaikan permanen (admin):

- Ubah `ImagePath` service `Cloudflared` agar memakai:
  - `"C:\cloudflared\cloudflared.exe" --config "C:\cloudflared\config.yml" tunnel run quantum-tunnel`

### 2) PHP tidak ditemukan

Pastikan salah satu kondisi berikut terpenuhi:

- File `C:\xampp\php\php.exe` ada.
- Atau command `php` tersedia di PATH.

### 3) Ingin menonaktifkan autostart

Untuk Startup Folder Mode, hapus file berikut:

- C:\Users\midip\AppData\Roaming\Microsoft\Windows\Start Menu\Programs\Startup\laravel-apps-autostart.bat

Untuk Windows Service Mode, jalankan:

```powershell
& 'D:\laravel\services\uninstall-laravel-services.ps1'
```

### 4) Ingin ubah port

Edit file `D:\laravel\start-laravel-apps.ps1`, bagian array `$apps`, lalu sesuaikan nilai `Port`.

## Catatan

- Service Mode membutuhkan PowerShell Administrator saat instalasi/uninstall.
- Jika akun saat ini bukan Administrator, installer service akan berhenti dengan pesan: "Run this script as Administrator."

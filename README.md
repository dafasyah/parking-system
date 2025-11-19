<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Parking System (Laravel 11)

Sistem parkir dengan fitur masuk & keluar kendaraan, perhitungan biaya otomatis, QR Code, laporan (Excel/PDF), dashboard analitik, audit trail, role & permission, dan 2FA.

### Fitur Utama
- Input kendaraan masuk: plat & jenis (motor/mobil) -> generate kode unik `PARK-YYYYMMDD-XXXX` + QR Code.
- Kendaraan keluar: scan QR atau input kode -> hitung durasi & biaya (Motor Rp2.000/jam, Mobil Rp5.000/jam, dibulatkan ke atas min 1 jam) + email struk via queue.
- Role: Admin & User (Spatie Permission).
- Audit log: Spatie Activity Log.
- 2FA wajib Admin, opsional User (Google2FA).
- Export laporan: Excel (Maatwebsite) & PDF (DomPDF) via queue (low priority).
- Dashboard: Chart.js (line 7 hari revenue, pie motor vs mobil, bar transaksi per jam) + cache 5 menit.
- Rate limiting: login 5/menit, transaksi 10/menit, export 3/5 menit.
- Sanitasi input & validasi regex plat: `/^[A-Z]{1,2}\s\d{1,4}\s[A-Z]{1,3}$/`.
- Cegah double entry plat masih status parked.
- Activity viewer untuk Admin.

### Teknologi
PHP 8.3+, Laravel 11, MySQL 8, Redis (queue + cache), Tailwind + Bootstrap (opsional), Chart.js, html5-qrcode, SweetAlert2.

### Instalasi
```bash
git clone <repo>
cd parking-system
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan migrate --seed
```

Tambahkan di `.env` (contoh):
```env
APP_URL=http://localhost
DB_DATABASE=parking
DB_USERNAME=root
DB_PASSWORD=
QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_user
MAIL_PASSWORD=your_pass
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=no-reply@parking.com
MAIL_FROM_NAME="Parking System"
```

### Menjalankan
```bash
redis-server # atau service start redis
php artisan serve
php artisan queue:work --queue=high,default,low
npm run dev
```

### Supervisor (Production Contoh)
```
[program:parking_queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/parking-system/artisan queue:work redis --queue=high,default,low --sleep=3 --tries=3 --timeout=90
autostart=true
autorestart=true
numprocs=3
redirect_stderr=true
stdout_logfile=/var/www/parking-system/storage/logs/worker.log
```

### Struktur Bisnis
Services: `ParkingCodeGenerator`, `ParkingFeeCalculator`, `QRCodeService` memisahkan logika dari controller. Jobs menangani email & export. Export besar diproses asynchronous.

### Two-Factor Auth
Admin login diarahkan setup 2FA jika belum aktif. Menggunakan Google Authenticator (library `pragmarx/google2fa`). Secret disimpan terenkripsi.

### Keamanan
- Form Request + regex plat + uppercase normalisasi.
- Rate limiter custom di `AppServiceProvider`.
- Eager loading disarankan untuk halaman listing lanjut.
- Activity log setiap perubahan penting (User & ParkingTransaction).
- XSS: gunakan Blade escaping (`{{ }}`) & validasi input.

### Migrations Tambahan
- `users`: phone_number, is_2fa_enabled, two_factor_secret.
- `parking_rates`: motor, mobil.
- `parking_transactions`: indeks pada kode unik, plat, status, waktu masuk/keluar, jenis.

### Export
Route export akan dispatch job `ExportParkingReport` (PDF / Excel) dan mengirim email notifikasi.

### Pengembangan Lanjut
- Tambah mailable khusus notifikasi export.
- Implement halaman scan QR (html5-qrcode) mengisi form otomatis.
- Tambah repository layer bila kompleksitas query bertambah.

### Lisensi
MIT.

### Catatan
Pastikan menjalankan `composer update` jika dependency belum ter-install penuh (jaringan timeout). Setelah itu jalankan kembali migrasi & seeder jika diperlukan.

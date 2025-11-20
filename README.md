# 🚗 Parking Management System

Sistem manajemen parkir berbasis web dengan fitur entry/exit kendaraan, perhitungan biaya otomatis, QR Code, laporan Excel/PDF, dashboard analitik, audit trail, role management, dan Two-Factor Authentication (2FA).

---

## 📋 Tech Stack & Versions

### Core Framework
- **PHP** `8.2+`
- **Laravel** `11.x`
- **MySQL** `8.0+`
- **Node.js** `20.x+`
- **NPM** `10.x+`

### Backend Packages (Composer)
```json
"laravel/framework": "^11.0"
"spatie/laravel-permission": "^6.0"         // Role & Permission Management
"spatie/laravel-activitylog": "^4.8"        // Audit Trail
"pragmarx/google2fa": "^8.0"                // Two-Factor Authentication
"maatwebsite/excel": "^3.1"                 // Excel Export
"barryvdh/laravel-dompdf": "^2.0"           // PDF Generation
"simplesoftwareio/simple-qrcode": "^4.2"    // QR Code Generator
"bacon/bacon-qr-code": "^2.0"               // QR Code Rendering
```

### Frontend Packages (NPM)
```json
"chart.js": "^4.4.0"                        // Dashboard Charts
"alpinejs": "^3.13.0"                       // Reactive UI Components
"tailwindcss": "^3.4.0"                     // CSS Framework
"@fortawesome/fontawesome-free": "^6.5.0"   // Icons
"sweetalert2": "^11.10.0"                   // Alert Modals
```

### Database & Cache
- **MySQL 8.0+** - Primary database
- **Redis (Optional)** - Queue & Cache driver

---

## 🎯 Fungsi-Fungsi Dasar

### 1. **Parking Entry (Kendaraan Masuk)**
- Input nomor plat kendaraan dan jenis (Motor/Mobil)
- Generate kode unik format: `PARK-YYYYMMDD-XXXX`
- Generate QR Code otomatis untuk tiket parkir
- Validasi plat ganda (cegah double entry)
- Print/Download tiket dalam format PDF

### 2. **Parking Exit (Kendaraan Keluar)**
- Scan QR Code atau input kode manual
- Hitung durasi parkir otomatis
- Hitung biaya parkir:
  - Motor: Rp 2.000/jam
  - Mobil: Rp 5.000/jam
  - Pembulatan ke atas (minimal 1 jam)
- Print/Download struk pembayaran PDF

### 3. **Dashboard Analitik**
- Total kendaraan yang sedang parkir
- Pendapatan hari ini & bulan ini
- Total transaksi selesai
- Chart interaktif:
  - Line chart: Revenue 7 hari terakhir
  - Pie chart: Komposisi motor vs mobil
  - Bar chart: Transaksi per jam
- Filter berdasarkan tanggal, jenis kendaraan, status
- Table sortable untuk transaksi terbaru

### 4. **Laporan (Reports)**
- Export laporan ke Excel (XLSX)
- Export laporan ke PDF
- Filter laporan berdasarkan:
  - Rentang tanggal
  - Jenis kendaraan (Motor/Mobil)
  - Status (Parkir/Selesai)
- Ringkasan: Total transaksi, total pendapatan, rata-rata

### 5. **User Management (Admin Only)**
- Tambah, edit, hapus user
- Assign role: Admin atau User
- Kelola status aktif/non-aktif user
- Role-based access control

### 6. **Activity Log (Audit Trail)**
- Log semua aktivitas penting:
  - Login/Logout user
  - Entry/Exit kendaraan
  - Perubahan data user
  - Export laporan
- Filter berdasarkan user, tanggal, tipe log
- Tracking IP address

### 7. **Two-Factor Authentication (2FA)**
- Setup 2FA dengan Google Authenticator
- Wajib untuk Admin, opsional untuk User
- Generate recovery codes (8 kode)
- Regenerate recovery codes
- Disable 2FA

### 8. **Parking Rate Settings**
- Atur tarif parkir Motor dan Mobil
- Update tarif per jam
- History perubahan tarif

### 9. **Security Features**
- Rate limiting:
  - Login: 5 attempt per menit
  - Transaksi: 10 per menit
  - Export: 3 per 5 menit
- Input sanitasi & validasi
- Regex validasi plat nomor
- Password hashing (Bcrypt)
- CSRF protection
- XSS protection

---

## 🖥️ Instalasi di Laragon (Local Development)

### Prerequisites
**WAJIB menggunakan Laragon** sebagai local server dengan spesifikasi:
- Laragon Full `6.0+`
- PHP `8.2` atau `8.3` (sudah include di Laragon)
- MySQL `8.0+` (sudah include di Laragon)
- Composer `2.6+` (sudah include di Laragon)
- Node.js `20.x+` (install manual jika belum ada)

#### ⚠️ PHP Extensions yang Diperlukan
Pastikan extension PHP berikut sudah **diaktifkan** di Laragon:

**WAJIB (Required):**
- ✅ `gd` - Untuk QR Code generation & image processing
- ✅ `zip` - Untuk Excel export & file compression
- ✅ `pdo_mysql` - Database connection
- ✅ `mbstring` - String handling
- ✅ `openssl` - Encryption & security
- ✅ `intl` - Internationalization

**Cara Mengaktifkan Extension di Laragon:**
1. Klik kanan **Laragon** → **PHP** → **php.ini**
2. Cari baris extension yang masih dicomment (`;extension=...`)
3. Hapus tanda `;` (semicolon) di depan extension:
   ```ini
   extension=gd
   extension=zip
   extension=pdo_mysql
   extension=mbstring
   extension=openssl
   extension=intl
   ```
4. **Save** file `php.ini`
5. **Restart Laragon** (Stop → Start)

**Verifikasi Extension:**
```bash
# Cek extension yang aktif
php -m | findstr "gd zip"

# Atau buat file info.php di public/
<?php phpinfo(); ?>
# Akses: http://localhost/parking-system/public/info.php
```

**Jika extension `gd` atau `zip` tidak tersedia:**
- Download PHP versi yang sama dari [windows.php.net](https://windows.php.net/download/)
- Extract dan copy file `php_gd.dll` + `php_zip.dll` ke folder `C:\laragon\bin\php\php-8.x\ext\`
- Aktifkan di `php.ini` seperti langkah di atas

### Langkah Instalasi

#### 1. Extract Project
```bash
# Extract file parking-system.zip ke folder:
C:\laragon\www\parking-system
```

#### 2. Buka Laragon Terminal
Klik kanan pada Laragon → **Terminal**

#### 3. Install Dependencies
```bash
cd C:\laragon\www\parking-system

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

#### 4. Konfigurasi Environment
```bash
# Copy file .env.example menjadi .env
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### 5. Setup Database
- Buka **HeidiSQL** (dari Laragon)
- Buat database baru: `parking_system`
- Edit file `.env`:
```env
APP_NAME="Parking System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://parking-system.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=parking_system
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
QUEUE_CONNECTION=sync
CACHE_DRIVER=file
```

#### 6. Jalankan Migrasi & Seeder
```bash
# Run migrations
php artisan migrate

# Seed database dengan data awal
php artisan db:seed
```

**Default User yang dibuat:**
- **Admin**
  - Email: `admin@parkir.com`
  - Password: `password`
  - Role: Admin

- **User**
  - Email: `user@parkir.com`
  - Password: `password`
  - Role: User

#### 7. Build Frontend Assets
```bash
npm run dev
```

#### 8. Setup Virtual Host di Laragon
- Klik **Menu** → **Preferences** → **General**
- Pastikan **Auto create virtual hosts** dicentang
- Restart Laragon

#### 9. Akses Aplikasi
Buka browser dan akses:
```
http://parking-system.test
```

atau

```
http://localhost/parking-system/public
```

---

## 🚀 Running di Development Mode

### Opsi 1: Menggunakan Laravel Serve (Recommended)
```bash
# Terminal 1 - Laravel Server
php artisan serve

# Terminal 2 - Vite Dev Server (untuk hot reload)
npm run dev
```
Akses: `http://localhost:8000`

### Opsi 2: Menggunakan Laragon Apache
```bash
# Jalankan build sekali saja
npm run build

# Akses via Laragon virtual host
# http://parking-system.test
```

---

## 📁 Struktur Project

```
parking-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # All controllers
│   │   ├── Middleware/         # Custom middleware (2FA)
│   │   └── Requests/           # Form validation
│   ├── Models/                 # Eloquent models
│   ├── Services/               # Business logic
│   │   ├── ParkingCodeGenerator.php
│   │   ├── ParkingFeeCalculator.php
│   │   └── QRCodeService.php
│   └── Jobs/                   # Queue jobs (Email, Export)
├── database/
│   ├── migrations/             # Database schema
│   └── seeders/                # Data seeders
├── resources/
│   ├── views/                  # Blade templates
│   ├── js/                     # JavaScript files
│   └── css/                    # Stylesheets
├── routes/
│   └── web.php                 # Application routes
├── public/                     # Public assets
└── storage/                    # Generated files (PDF, QR)
```

---

## 📝 Catatan Penting

### Tarif Parkir Default
- Motor: **Rp 2.000/jam**
- Mobil: **Rp 5.000/jam**
- Minimal durasi: **1 jam** (pembulatan ke atas)

### Perhitungan Biaya
Contoh:
- Masuk: 08:00
- Keluar: 09:45
- Durasi: 1 jam 45 menit → **Dibulatkan menjadi 2 jam**
- Motor: 2 jam × Rp 2.000 = **Rp 4.000**
- Mobil: 2 jam × Rp 5.000 = **Rp 10.000**

### Format Plat Nomor
Valid: `B 1234 ABC`, `AB 123 CD`, `D 5678 EF`
Regex: `/^[A-Z]{1,2}\s\d{1,4}\s[A-Z]{1,3}$/`

### Queue & Email (Optional)
Jika ingin menggunakan email notification & background jobs:
1. Setup mail driver di `.env` (Mailtrap, Gmail, dll)
2. Ubah `QUEUE_CONNECTION=database`
3. Jalankan: `php artisan queue:work`

### Redis (Optional)
Untuk performance lebih baik:
1. Install Redis di Laragon
2. Edit `.env`:
```env
QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

---

## 🔐 Security Best Practices

1. **Production Environment:**
   - Set `APP_DEBUG=false`
   - Generate strong `APP_KEY`
   - Gunakan HTTPS
   - Setup rate limiting sesuai kebutuhan

2. **Database:**
   - Gunakan password database yang kuat
   - Jangan expose database ke public

3. **2FA:**
   - Wajibkan 2FA untuk semua Admin
   - Simpan recovery codes dengan aman

4. **Backup:**
   - Backup database secara berkala
   - Backup file uploads di `storage/`

---

## 🆘 Troubleshooting

### Error: "No application encryption key has been specified"
```bash
php artisan key:generate
```

### Error: "SQLSTATE[HY000] [1049] Unknown database"
Buat database `parking_system` di HeidiSQL/MySQL

### Error: "Class 'Imagick' not found" atau "GD extension not loaded"
**Penyebab:** Extension `gd` belum aktif di PHP

**Solusi:**
1. Buka `php.ini` (Laragon → Klik Kanan → PHP → php.ini)
2. Cari dan hapus `;` di depan:
   ```ini
   ;extension=gd
   ```
   Menjadi:
   ```ini
   extension=gd
   ```
3. Restart Laragon
4. Verifikasi: `php -m | findstr gd`

### Error: "ZipArchive class not found" atau Excel export gagal
**Penyebab:** Extension `zip` belum aktif

**Solusi:**
1. Buka `php.ini`
2. Aktifkan extension:
   ```ini
   extension=zip
   ```
3. Restart Laragon
4. Verifikasi: `php -m | findstr zip`

### Error: "Call to undefined function imagecreatefromstring()"
**Penyebab:** Extension `gd` tidak ter-install atau tidak aktif

**Solusi:**
1. Cek apakah file `php_gd.dll` ada di `C:\laragon\bin\php\php-8.x\ext\`
2. Jika tidak ada, download PHP versi yang sama dari [windows.php.net](https://windows.php.net/download/)
3. Extract dan copy `php_gd.dll` ke folder `ext\`
4. Aktifkan di `php.ini`: `extension=gd`
5. Restart Laragon

### Error: Permission denied (storage/logs)
```bash
chmod -R 775 storage bootstrap/cache
```

### Error: Mix manifest not found
```bash
npm install
npm run build
```

### Halaman CSS/JS tidak load
```bash
# Clear cache
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# Rebuild assets
npm run build
```

### QR Code tidak muncul di PDF
**Penyebab:** Extension `gd` tidak aktif atau package `simplesoftwareio/simple-qrcode` error

**Solusi:**
1. Pastikan `gd` extension aktif: `php -m | findstr gd`
2. Clear cache: `php artisan cache:clear`
3. Regenerate QR: Hapus cache di `storage/framework/cache/`
4. Test QR generation:
   ```php
   use SimpleSoftwareIO\QrCode\Facades\QrCode;
   QrCode::size(200)->generate('TEST');
   ```

### QR Scanner tidak muncul / Camera permission denied di Chrome
**Penyebab:** Chrome memblokir akses kamera di localhost (HTTP) karena alasan keamanan

**Solusi untuk Development (Localhost):**

1. **Buka Chrome Flags:**
   ```
   chrome://flags/#unsafely-treat-insecure-origin-as-secure
   ```

2. **Enable Insecure Origins:**
   - Di kolom "Insecure origins treated as secure", masukkan:
     ```
     http://localhost:8000,http://parking-system.test,http://127.0.0.1:8000
     ```
   - Atau sesuaikan dengan URL local Anda

3. **Set dropdown** menjadi **"Enabled"**

4. **Relaunch Chrome** (akan muncul tombol "Relaunch" di kanan bawah)

5. **Refresh halaman** dan klik tombol scan QR, Chrome akan request camera permission

**Alternatif Solusi:**
- Gunakan **HTTPS** di local (Laravel Valet, Laragon SSL)
- Test di **Firefox** atau **Edge** (lebih permisif untuk localhost)
- Deploy ke **production** dengan HTTPS (otomatis work)

**Referensi:**
- [Stack Overflow: Chrome Camera Access on Localhost](https://stackoverflow.com/questions/16835421/how-to-allow-chrome-to-access-my-camera-on-localhost)
- [Chrome Developers: getUserMedia on Insecure Origins](https://sites.google.com/a/chromium.org/dev/Home/chromium-security/deprecating-powerful-features-on-insecure-origins)

**Note untuk Production:**
Pastikan menggunakan **HTTPS** agar QR scanner berfungsi tanpa perlu konfigurasi tambahan.

### Composer install error: "ext-gd required"
```bash
# Aktifkan gd di php.ini terlebih dahulu
# Lalu jalankan:
composer install --ignore-platform-reqs

# Atau update composer.json:
{
    "config": {
        "platform": {
            "ext-gd": "8.2"
        }
    }
}
```

---

## 📄 License

MIT License - Free to use for personal and commercial projects.

---

## 🤖 About This Documentation

**This README was compiled using GitHub Copilot** - AI-powered code assistance tool.

Documentation generated with AI assistance to ensure comprehensive coverage of all features, installation steps, and best practices for the Parking Management System.

---

**Version:** 1.0.0  
**Last Updated:** November 2025  
**Developed with:** Laravel 11 + Alpine.js + Tailwind CSS

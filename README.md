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
"spatie/laravel-permission": "^6.0"          // Role & Permission Management
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

# 🧪 Quick Testing Guide - Adjustment Round 2

## ✅ Test Checklist

### 1. Print PDF Tiket Masuk

**Steps:**
1. Buat transaksi parkir baru (entry)
2. Di halaman success, klik **"Download PDF"** (tombol ungu)
3. PDF akan langsung terdownload

**Expected Result:**
- ✅ File `tiket-masuk-{CODE}.pdf` terdownload
- ✅ QR Code muncul di PDF
- ✅ Format thermal receipt (80mm)
- ✅ Semua detail vehicle tercetak
- ✅ Bisa dibuka dan print dari PDF reader

**Screenshot Test:**
- Open PDF di Adobe Reader / Browser
- Verify QR code visible
- Print test jika ada thermal printer

---

### 2. Print PDF Struk Keluar

**Steps:**
1. Proses exit kendaraan yang sudah parkir
2. Di halaman exit success, klik **"Download PDF"** (tombol ungu)
3. PDF struk pembayaran langsung terdownload

**Expected Result:**
- ✅ File `struk-keluar-{CODE}.pdf` terdownload
- ✅ Detail waktu masuk & keluar tampil
- ✅ Perhitungan durasi & biaya benar
- ✅ Stamp "LUNAS" muncul
- ✅ Format thermal receipt professional

**Test Cases:**
- Motor parkir 2 jam
- Mobil parkir 5 jam
- Parkir dengan overtime

---

### 3. Export Laporan (Direct Download)

**Excel Export:**
1. Buka menu **Reports**
2. Set filter (optional): date range, vehicle type
3. Klik **"Export Excel"**
4. **JANGAN tunggu alert** - File langsung download!

**Expected Result:**
- ✅ File `laporan-parkir-{TIMESTAMP}.xlsx` langsung download
- ✅ Tidak ada popup "sedang diproses"
- ✅ Data sesuai filter
- ✅ Max 5000 records
- ✅ Format Excel rapi dengan headers

**PDF Export:**
1. Di Reports, klik **"Export PDF"**
2. File langsung download (no waiting)

**Expected Result:**
- ✅ File `laporan-parkir-{TIMESTAMP}.pdf` langsung download
- ✅ Format landscape A4
- ✅ Table dengan borders
- ✅ Summary section di bawah
- ✅ Data sesuai filter

**Important:** 
- ❌ TIDAK perlu `php artisan queue:work` lagi
- ✅ Export langsung synchronous download
- ✅ Instant feedback untuk user

---

### 4. Login Validation Specifik

**Test Case 1: Email Tidak Terdaftar**
```
Email: notexist@test.com
Password: password123
```
**Expected:** Error merah di field email: *"Email tidak terdaftar dalam sistem."*

**Test Case 2: Format Email Salah**
```
Email: invalidemail
Password: password123
```
**Expected:** Error: *"Format email tidak valid"*

**Test Case 3: Email Benar, Password Salah**
```
Email: admin@test.com (yang benar)
Password: wrongpassword
```
**Expected:** Error merah di field password: *"Password yang Anda masukkan salah."*

**Test Case 4: Password Terlalu Pendek**
```
Email: admin@test.com
Password: 123
```
**Expected:** Error: *"Password minimal 6 karakter"*

**Test Case 5: Login Berhasil**
```
Email: admin@test.com
Password: password (benar)
```
**Expected:** 
- ✅ Redirect ke dashboard
- ✅ Alert hijau: *"Selamat datang, Admin!"* (dengan nama user)
- ✅ Session created

**Check Activity Log:**
- Menu: Activity Log
- Verify entries untuk setiap failed login dengan reason:
  - `email_not_found`
  - `wrong_password`
  - `login_success`

---

### 5. 2FA Verification Speed

**Setup:**
1. Enable 2FA untuk satu user
2. Logout
3. Login lagi dengan email & password

**Test OTP Entry:**
1. Buka Google Authenticator / Authy
2. Copy 6-digit code
3. Paste di form 2FA verification
4. Submit

**Expected:**
- ✅ Verification completes dalam **< 1 detik**
- ✅ Tidak ada delay/loading lama
- ✅ Langsung redirect ke dashboard
- ✅ Jika code salah, error muncul instant

**Test with Old Code:**
1. Tunggu code berganti (setelah 30 detik)
2. Gunakan code LAMA yang baru expired
3. Submit

**Expected:**
- ✅ Masih diterima (karena window = 2)
- ✅ Atau error jelas: *"Kode OTP tidak valid atau expired"*

**Performance Check:**
- Open browser DevTools > Network
- Submit OTP
- Check response time: should be < 1000ms

---

## 🔍 Manual Verification Points

### PDF QR Code Verification
```bash
# Check QR code file exists
ls storage/app/public/qrcodes/

# Check PDF generated
# Open downloaded PDF
# Scan QR with phone - should work
```

### Export File Check
```bash
# Check download folder
# Verify Excel/PDF can be opened
# Check data accuracy vs dashboard
```

### Login Activity Logs
```sql
-- Check activity log table
SELECT * FROM activity_log 
WHERE description LIKE '%Login%' 
ORDER BY created_at DESC 
LIMIT 10;
```

### 2FA Performance Log
```bash
# Check Laravel log for slow queries
tail -f storage/logs/laravel.log

# Look for:
# - No "Google2FA" slow queries
# - Verification < 500ms
```

---

## 🐛 Common Issues & Solutions

### Issue: PDF QR Code Tidak Muncul
**Solution:**
```bash
# Check file exists
ls storage/app/public/qrcodes/{CODE}.png

# Regenerate QR if needed
php artisan storage:link
```

### Issue: Export Masih Muncul Alert
**Cause:** Browser cache
**Solution:**
1. Hard refresh: `Ctrl+F5`
2. Clear browser cache
3. Check network tab - should see direct download

### Issue: 2FA Masih Lambat
**Check:**
```php
// In TwoFactorController verify method
// Make sure window parameter exists:
$valid = $this->google2fa->verifyKey($secret, $request->otp, 2);
//                                                            ^^^ Window = 2
```

### Issue: Login Error Tidak Spesifik
**Clear cache:**
```bash
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

---

## 📊 Performance Benchmarks

### Expected Response Times

| Action | Expected Time |
|--------|---------------|
| PDF Entry Ticket | < 2 seconds |
| PDF Exit Receipt | < 2 seconds |
| Excel Export (1000 rows) | < 3 seconds |
| PDF Export (1000 rows) | < 5 seconds |
| 2FA Verification | < 1 second |
| Login Validation | < 500ms |

### Memory Usage

| Operation | Memory |
|-----------|--------|
| PDF Generation | ~15-20 MB |
| Excel Export (1000) | ~25 MB |
| Excel Export (5000) | ~100 MB |

---

## ✅ Final Verification Checklist

Before marking as DONE:

**Print PDFs:**
- [ ] Entry ticket PDF downloads
- [ ] Exit receipt PDF downloads
- [ ] QR codes visible in PDFs
- [ ] Thermal format looks good

**Export:**
- [ ] Excel direct download works
- [ ] PDF direct download works
- [ ] No "processing" alert shows
- [ ] Filters apply correctly
- [ ] File names have timestamp

**Login:**
- [ ] Email not found error specific
- [ ] Wrong password error specific
- [ ] Success message personalized
- [ ] Activity log records reasons

**2FA:**
- [ ] Verification < 1 second
- [ ] No unnecessary retries
- [ ] Window = 2 works
- [ ] Error messages clear

**General:**
- [ ] All caches cleared
- [ ] No console errors
- [ ] Routes accessible
- [ ] Database logs correct

---

## 🎯 Quick Test Script

Run all tests in sequence:

```bash
# 1. Clear all caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 2. Check storage link
php artisan storage:link

# 3. Test route list
php artisan route:list | grep parking

# 4. Open browser tests:
# - http://parking.test/parking/entry
# - Create entry → Download PDF
# - Process exit → Download PDF
# - Reports → Export Excel
# - Reports → Export PDF
# - Logout → Login with wrong email
# - Login with wrong password
# - Login correct → Enable 2FA → Test verification
```

---

## 📝 Notes

1. **No Queue Worker Needed**
   - Export sekarang synchronous
   - File langsung download
   - Lebih simple untuk user

2. **PDF Thermal Format**
   - Tested untuk thermal printer 80mm
   - Bisa print dari PDF reader biasa juga
   - QR code scannable from printed receipts

3. **2FA Speed**
   - Window = 2 adalah standar industri
   - Recommended by RFC 6238
   - Balance antara security & UX

4. **Login Security**
   - Specific errors = better UX
   - Activity logs untuk audit
   - IP tracking untuk security

---

**Status:** 🟢 Ready for Testing

**Last Updated:** November 19, 2025

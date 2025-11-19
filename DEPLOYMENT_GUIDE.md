# Deployment Guide - Parking Management System

## Git Repository Setup ✅

Repository sudah diinisialisasi dan commit pertama sudah dibuat.

### Push ke GitHub (Private Repository)

1. **Buat repository baru di GitHub:**
   - Buka https://github.com/new
   - Nama repository: `parking-system`
   - **PENTING:** Pilih **Private** repository
   - Jangan centang "Initialize with README" (karena sudah ada)
   - Klik "Create repository"

2. **Push ke GitHub:**
   ```bash
   cd c:\laragon\www\parking-system
   git remote add origin https://github.com/dafasyah/parking-system.git
   git branch -M main
   git push -u origin main
   ```

3. **Jika diminta login:**
   - Username: `dafasyah`
   - Password: Gunakan **Personal Access Token** (bukan password akun)
   - Cara buat token: Settings → Developer settings → Personal access tokens → Tokens (classic) → Generate new token

---

## Free Deployment Options

### Option 1: Railway.app (RECOMMENDED) ⭐

**Keunggulan:**
- ✅ Free tier: 500 hours/month (~21 hari)
- ✅ PostgreSQL database included
- ✅ Auto-deploy dari GitHub
- ✅ Custom domain support
- ✅ Environment variables management
- ✅ Persistent storage
- ✅ Easy Laravel setup

**Setup Steps:**

1. **Buat akun di Railway:**
   - Kunjungi https://railway.app
   - Sign up with GitHub
   - Connect GitHub account

2. **Deploy project:**
   ```bash
   # Install Railway CLI (opsional)
   npm install -g @railway/cli
   
   # Login
   railway login
   
   # Deploy
   railway init
   railway up
   ```

3. **Atau via Dashboard:**
   - Klik "New Project" → "Deploy from GitHub repo"
   - Pilih repository `parking-system`
   - Railway akan auto-detect Laravel

4. **Konfigurasi Environment:**
   - Tambahkan PostgreSQL database (klik "+ New" → "Database" → "PostgreSQL")
   - Set environment variables:
     ```
     APP_ENV=production
     APP_DEBUG=false
     APP_KEY=base64:... (generate dengan: php artisan key:generate --show)
     APP_URL=https://your-app.railway.app
     
     DB_CONNECTION=pgsql
     DB_HOST=${{PGHOST}}
     DB_PORT=${{PGPORT}}
     DB_DATABASE=${{PGDATABASE}}
     DB_USERNAME=${{PGUSER}}
     DB_PASSWORD=${{PGPASSWORD}}
     ```

5. **Build & Deploy Commands:**
   - Build: `composer install --optimize-autoloader --no-dev && php artisan config:cache && php artisan route:cache && php artisan view:cache`
   - Start: `php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT`

6. **Nixpacks Config (create `nixpacks.toml`):**
   ```toml
   [phases.setup]
   nixPkgs = ['php82', 'php82Packages.composer']
   
   [phases.build]
   cmds = ['composer install --optimize-autoloader --no-dev']
   
   [start]
   cmd = 'php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT'
   ```

---

### Option 2: Render.com (Alternative)

**Keunggulan:**
- ✅ Free tier available
- ✅ PostgreSQL free tier (90 days, then expires)
- ✅ Auto-deploy from GitHub
- ✅ Custom domains

**Setup:**

1. Sign up di https://render.com
2. New → Web Service → Connect GitHub repo
3. Environment: Docker or Native
4. Build Command: `composer install && php artisan migrate --force`
5. Start Command: `php artisan serve --host=0.0.0.0 --port=$PORT`

---

### Option 3: Heroku (Classic Option)

**Note:** Heroku free tier sudah dihapus sejak 2022, tapi masih ada tier murah ($5/bulan)

---

## Production Checklist

### Before Deployment:

- [x] Git repository initialized
- [x] Initial commit created
- [x] AI comments removed
- [ ] Push to GitHub private repository
- [ ] Environment variables configured
- [ ] Database credentials set

### After Deployment:

- [ ] Run migrations: `php artisan migrate --force`
- [ ] Create admin user:
  ```bash
  php artisan tinker
  $user = User::create([
      'name' => 'Admin',
      'email' => 'admin@parkir.com',
      'password' => Hash::make('password123'),
      'role' => 'admin',
      'is_active' => true
  ]);
  ```
- [ ] Seed parking rates:
  ```bash
  ParkingRate::create(['vehicle_type' => 'motor', 'rate' => 2000]);
  ParkingRate::create(['vehicle_type' => 'mobil', 'rate' => 5000]);
  ```
- [ ] Test login
- [ ] Test parking entry
- [ ] Test parking exit
- [ ] Test reports export
- [ ] Test 2FA setup

### Security Checklist:

- [ ] `APP_DEBUG=false` in production
- [ ] Strong `APP_KEY` generated
- [ ] Database credentials secure
- [ ] `.env` file NOT in repository
- [ ] HTTPS enabled (Railway provides this automatically)
- [ ] CSRF protection enabled
- [ ] Rate limiting configured

---

## Maintenance Commands

### Update deployment:
```bash
git add .
git commit -m "Update: description"
git push origin main
```

### View logs (Railway):
```bash
railway logs
```

### Run artisan commands (Railway):
```bash
railway run php artisan migrate
railway run php artisan cache:clear
railway run php artisan config:clear
```

---

## Troubleshooting

### Storage permissions:
```bash
chmod -R 775 storage bootstrap/cache
```

### Clear cache:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Database issues:
```bash
php artisan migrate:fresh --seed
```

---

## Cost Estimate (Free Tier)

**Railway.app:**
- Free: 500 hours/month ($5 credit)
- Database: Included in free tier
- **Recommendation:** Deploy dan monitor usage, biasanya cukup untuk demo/portfolio

**Render.com:**
- Free: Web service unlimited
- Database: 90 days free trial
- After trial: $7/month for PostgreSQL

**Total:** **$0/month** untuk 500 jam Railway, atau switch ke Render setelah 21 hari.

---

## Support

Jika ada masalah deployment, cek:
1. Railway logs: `railway logs --tail`
2. Laravel logs: `storage/logs/laravel.log`
3. GitHub Actions (jika setup CI/CD)

---

**Status:** Repository ready for GitHub push → Choose deployment platform → Deploy 🚀

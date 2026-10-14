# Panduan Deploy Bagimu Extra ke Production

Aplikasi ini **siap deploy**: tidak butuh cron, tidak butuh queue worker,
tidak butuh Redis. Cukup **PHP + database + web server**.

## 1. Prasyarat server

| Kebutuhan | Minimal |
|---|---|
| PHP | 8.2+ dengan ekstensi: `mbstring fileinfo openssl pdo pdo_mysql/pdo_sqlite tokenizer xml ctype json curl zip gd` |
| Database | SQLite (termudah) **atau** MySQL 8+ |
| Web server | Apache (`.htaccess` sudah tersedia di `public/`) atau Nginx |
| SSL | Wajib (sertifikat gratis via Let's Encrypt / bawaan hosting) |
| Node.js | Hanya saat build, bisa build di laptop lalu upload hasilnya |

Cek otomatis kapan saja di server: `php artisan deploy:check` (exit 0 = siap).

## 2. Deploy pertama kali (fresh)

### Opsi A — VPS (Ubuntu, Nginx/Apache) — disarankan

```bash
cd /var/www && git clone <repo> bagimu && cd bagimu
cp .env.production.example .env
nano .env            # isi APP_URL, DB_*, MAIL_*, dsb.
php artisan key:generate
composer install --no-dev --optimize-autoloader
npm ci && npm run build        # atau upload public/build dari laptop
touch database/database.sqlite # bila pakai SQLite
php artisan migrate --force
php artisan storage:link
php artisan app:create-admin   # buat admin (JANGAN db:seed di production!)
php artisan optimize           # config:cache + route:cache + view:cache
php artisan deploy:check       # harus exit 0
```

Arahkan document-root web server ke `<path>/public`, lalu buka `https://domain/up`
(harus 200 OK) dan login sebagai admin.

> Ada script otomatis: `bash deploy.sh` (untuk update berikutnya).

### Opsi B — Shared hosting / cPanel (tanpa SSH penuh)

1. Di laptop: `composer install --no-dev`, `npm run build`, lalu zip **isi proyek**
   (tanpa `node_modules`; sertakan `vendor/` + `public/build/` bila hosting tanpa SSH).
2. Upload & ekstrak ke hosting, mis. `/home/user/bagimu`. **Jangan** taruh di
   `public_html` langsung — yang dipublikasikan hanya isi `public/`:
   - Pindahkan **isi** folder `public/` ke `public_html/` (atau arahkan domain ke
     `bagimu/public` bila panel mendukung "document root").
   - Sesuaikan `public_html/index.php`: `$app = require __DIR__.'/../bagimu/bootstrap/app.php';`
     dan path `storage` bila perlu. (Paling aman: gunakan fitur "Addon Domain →
     Document Root = bagimu/public" bila tersedia.)
3. Buat `.env` dari `.env.production.example`, generate APP_KEY:
   bila ada SSH: `php artisan key:generate`. Bila tidak ada SSH: generate di laptop
   (`php artisan key:generate --show`), tempel hasilnya.
4. Database:
   - SQLite: upload `database/database.sqlite` (kosong), pastikan writable (644/664).
     Jalankan migrasi via SSH (`php artisan migrate --force`) atau via fitur
     "Cron Job sekali jalan" bila SSH tidak tersedia.
   - MySQL: buat DB + user di cPanel, isi `DB_*` di `.env`.
5. Buat symlink `public_html/storage → ../bagimu/storage/app/public`
   (atau via SSH: `php artisan storage:link`).
6. Buat admin: via SSH `php artisan app:create-admin`.
7. Buka `https://domain/up` lalu login.

## 3. Update versi berikutnya

```bash
php artisan down
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm ci && npm run build        # bila ada perubahan frontend
php artisan optimize
php artisan deploy:check
php artisan up
```

atau cukup `bash deploy.sh`.

## 4. Yang TIDAK perlu disiapkan

- **Cron/scheduler** — aplikasi tidak punya jadwal otomatis.
- **Queue worker / Supervisor** — `QUEUE_CONNECTION=sync`, email dikirim sinkron.
- **Redis/Memcached** — session & cache memakai file/DB.

## 5. Backup (jadwalkan di level server/hosting)

- File DB: `database/database.sqlite` **atau** dump MySQL harian.
- Folder: `storage/app/public` (bukti izin) + file `.env`.
- Uji restore minimal 1x sebelum go-live.

## 6. Checklist go-live (H-0)

- [ ] `php artisan deploy:check` exit 0 di server
- [ ] `APP_ENV=production`, `APP_DEBUG=false`
- [ ] `APP_URL=https://domain-asli`, `APP_FORCE_HTTPS=true`, SSL aktif
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] Admin dibuat via `app:create-admin` (password kuat, min. 8)
- [ ] Seeder demo **tidak** dijalankan di production (diblokir otomatis)
- [ ] Login dicoba untuk 3 peran: admin, pembina, siswa (NIS+token)
- [ ] Impor CSV/XLSX dicoba 1 file kecil
- [ ] Bukti izin (upload) bisa dibuka (tanda `storage:link` benar)
- [ ] QR pada kartu & sertifikat mengarah ke domain production
- [ ] Halaman `/up` mengembalikan 200
- [ ] Backup pertama sudah ada

## 7. Rollback darurat

```bash
php artisan down
git reset --hard <commit-sebelumnya>   # atau restore folder backup
composer install --no-dev --optimize-autoloader
php artisan migrate --force            # hanya bila migrasi baru bermasalah, jika perlu rollback manual
php artisan optimize
php artisan up
```

Untuk insiden data: restore file SQLite/dump MySQL + `storage/app/public` dari backup.

## 8. Troubleshooting production

| Gejala | Penyebab umum → solusi |
|---|---|
| 500 setelah deploy | permission `storage/` & `bootstrap/cache` (harus writable); `APP_KEY` kosong; lupa `migrate`; cek `storage/logs/laravel.log` |
| Halaman tanpa CSS/JS | `public/build` tidak ter-upload → `npm run build` + upload |
| Selalu logout / sesi hilang | `SESSION_DOMAIN` salah; cookie `secure` tanpa HTTPS; cek `APP_URL` |
| Upload bukti gagal | `upload_max_filesize`/`post_max_size` php.ini kekecilan; `public/storage` link rusak |
| Import `.xlsx` gagal | `extension=zip` mati di php.ini hosting → aktifkan |
| QR/link verifikasi menunjuk localhost | `APP_URL` belum diganti ke domain production |
| Email reset password tak sampai | `MAIL_MAILER=log` → isi SMTP production |
| 419 Page Expired | cache lama / `APP_KEY` berubah setelah sesi dibuat → refresh, `optimize:clear` lalu `optimize` |

# Cara Menjalankan Bagimu Extra di Laragon 6 (sekali setting, tahan restart)

## Pilihan A — via Laragon Apache (utama): `http://bagimuextra.test`

Sudah disiapkan permanen (tersimpan di file, tidak hilang saat restart):

1. Virtual-host `C:\laragon\etc\apache2\sites-enabled\auto.bagimuextra.test.conf`
   menunjuk ke `C:\DavidYehudaSurbakti\Bagimu_Extra\public`.
2. Saat Laragon Start, Laragon otomatis menambahkan `127.0.0.1 bagimuextra.test`
   ke file `hosts` (butuh jalankan Laragon sebagai Administrator minimal 1x).

Langkah tiap menyalakan laptop:

1. Buka Laragon → **Start All** → tunggu Apache hijau.
2. Buka `http://bagimuextra.test`.

> Jalankan `perbaiki-laragon.bat` cukup **1 kali** (atau tiap ada error aneh):
> mengaktifkan `extension=zip`, membersihkan cache basi, dan migrate database.

## Pilihan B — tanpa Laragon (paling anti-error): `jalan.bat`

Klik 2x `jalan.bat` di folder proyek → buka `http://localhost:8000`.
Tidak butuh Apache/MySQL Laragon sama sekali (database memakai SQLite).

## Yang sudah diperbaiki (akar masalah error tiap-start)

| Gejala | Penyebab | Perbaikan |
|---|---|---|
| Error/500 tiap Laragon dinyalakan, sembuh setelah "dibenerin" | Cache view basi di `storage/framework/views` (masih mereferensi route lama `admin.announcements.*` yang sudah dihapus) | `view:clear` + `view:cache` ulang (sudah dilakukan). Ke depan, tiap ganti route/view jalankan `php artisan optimize:clear`. |
| Import Excel `.xlsx` selalu "berkas kosong / tidak bisa" | `extension=zip` **mati** di `php.ini` Laragon (PHP 8.3.33) → PhpSpreadsheet gagal baca xlsx | Diaktifkan permanen di `C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.ini` baris `extension=zip`. Restart Laragon agar berlaku untuk Apache. |
| Proyek tidak muncul sebagai `*.test` / harus setting ulang vhost | Proyek di luar `C:\laragon\www`, jadi tidak dapat pretty-URL otomatis | Dibuatkan `auto.bagimuextra.test.conf` permanen. |
| Import CSV/Excel/PDF gagal tanpa pesan jelas (500 / diam) | Exception tidak ditangkap; CSV `;` (Excel Indonesia) tidak terdeteksi; PDF scan tak ada teks; NISN duplikat meledak | `StudentFileReader` ditulis ulang: auto-deteksi delimiter, pesan error ramah per kasus, controller menangkap semua error + lapor baris gagal. Ada **template CSV/XLSX** di halaman impor. |
| Laragon versi ganda (PHP 8.1 & 8.3, Apache 2 versi, MySQL 8.0 & 8.4) | Ganti versi → `php.ini`/data tidak cocok → service gagal start | **Jangan ganti-ganti versi.** Tetap di PHP 8.3.33 + Apache 2.4.68 + MySQL bebas (tidak dipakai; database = SQLite). PHP 8.1 tidak punya `php.ini` (rusak) — jangan dipilih. |

## Troubleshooting cepat

- **Apache merah / port 80 dipakai**: Skype/IIS/XAMPP menempati port 80/443.
  Tutup aplikasi itu, atau di Laragon: Menu → Preferences → Ports → ganti Apache ke 8080.
- **MySQL merah**: tidak masalah untuk aplikasi ini (pakai SQLite). Boleh dibiarkan mati.
- **Halaman error setelah update kode**: jalankan `php artisan optimize:clear`.
- **Import masih gagal**: unduh template di halaman Impor, isi sesuai contoh,
  simpan sebagai CSV (`;`) atau `.xlsx`, lalu impor ulang. PDF harus yang berisi
  teks (bukan hasil scan/foto).

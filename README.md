# Bagimu Extra

Sistem informasi dan manajemen pendaftaran ekstrakurikuler berbasis web untuk institusi pendidikan, dirancang untuk menyederhanakan alur pendaftaran, distribusi kuota, presensi kegiatan, hingga penilaian akhir siswa secara transparan, akuntabel, dan efisien.

---

## Ringkasan Sistem

Bagimu Extra menyediakan portal terintegrasi multi-peran yang menghubungkan Siswa, Wali Kelas, Pembina Ekstrakurikuler, dan Administrator Kesiswaan ke dalam satu sistem terpadu. Sistem ini menerapkan arsitektur *white-label ready*, memungkinkan seluruh identitas instansi (nama sekolah, kontak, logo, motto, dan alamat) dikonfigurasi secara dinamis langsung melalui panel kontrol.

Antarmuka dibangun dengan pendekatan minimalis modern menggunakan palet warna fungsional (*slate*, *blue*, *emerald*, *amber*, dan *rose*) serta pustaka ikon vektor terpadu tanpa ketergantungan pada pustaka pihak ketiga yang membebani kinerja peramban.

---

## Fitur Berdasarkan Peran

### 1. Portal Siswa
- **Autentikasi Token Aman:** Akses masuk menggunakan kombinasi Nomor Induk Siswa (NIS) dan Token Login sekali pakai yang diterbitkan oleh pihak sekolah.
- **Pemilihan Ekstrakurikuler Dinamis:** Pemantauan kuota berjalan (*real-time*), deskripsi kegiatan, dan jadwal latihan mingguan sebelum menetapkan pilihan langsung tanpa proses seleksi manual yang berbelit.
- **Portal Cek Status & Cetak Ulang Mandiri:** Siswa yang telah mendaftar dan tokennya telah terpakai dapat memeriksa status kepesertaan resmi, informasi pembina pengampu, jadwal mingguan, serta mencetak atau menyimpan ulang Kartu ID-1 langsung melalui NIS tanpa kendala sesi keluar otomatis.
- **Kartu Peserta Digital ID-1:** Penerbitan kartu tanda anggota resmi berdimensi presisi (85,6 mm x 53,98 mm) lengkap dengan kode QR verifikasi keabsahan, identitas siswa, dan latar dinamis instansi.
- **Proteksi Data Final:** Pilihan langsung terkunci setelah konfirmasi untuk mencegah manipulasi data pendaftaran.

### 2. Panel Wali Kelas
- **Direktori Rombel:** Pemantauan daftar siswa aktif dalam rombel kelas perwalian beserta status kepemilikan ekstrakurikuler.
- **Pengingat WhatsApp 1-Klik:** Tombol salin format pesan pengingat siap tempel ke grup WhatsApp kelas berisi daftar siswa yang belum menentukan pilihan eskul.
- **Ekspor Rekap Nilai e-Rapor (.xls):** Fasilitas unduh lembar nilai ekstrakurikuler rombel perwalian ke format berkas Excel yang selaras dengan kebutuhan buku nilai e-Rapor.
- **Manajemen Kelas (+ Kelas Saya):** Kemudahan mengelompokkan siswa ke dalam rombel perwalian melalui modal pencarian dan filter jenjang/status penempatan.
- **Penyesuaian Penempatan:** Fitur pembatalan atau pengeluaran siswa dari rombel perwalian baik per individu maupun checklist massal (*bulk action*).

### 3. Panel Pembina Ekstrakurikuler
- **Antarmuka 4 Tab Modern:** Tata letak responsif ramah layar ponsel yang membagi modul kegiatan secara rapi ke dalam tab Anggota, Presensi Pertemuan, Nilai Rapor, serta Jadwal & Izin.
- **Kelola Jadwal Latihan:** Pengaturan hari, rentang jam, dan lokasi latihan mingguan.
- **Presensi Pertemuan & Pemindai QR Kartu Pelajar:**
  - Pemindaian instan kode QR kartu pelajar menggunakan kamera peramban.
  - Fitur sekali klik "Tandai Semua Hadir" untuk kecepatan presensi lapangan tanpa klik satu per satu.
  - Pengisian presensi manual yang dikelompokkan teratur berdasarkan rombel kelas (Hadir, Izin, Sakit, Alpa).
- **Pengesahan Sesi:** Fitur penguncian data absensi per tanggal pertemuan untuk menjamin validitas rekapitulasi.
- **Verifikasi Izin & Sakit:** Peninjauan permohonan ketidakhadiran siswa beserta pratinjau dokumen atau foto bukti lampiran.
- **Pendaftaran Siswa Susulan (+ Tambah Anggota):** Fitur manual untuk mendaftarkan siswa yang terkendala akses perangkat atau kuota internet.
- **Evaluasi & Preset Nilai Cepat:** Input skor capaian (skala 0-100) dilengkapi pilihan preset nilai standar (85, 80, 78, 75) untuk pengisian otomatis seluruh baris yang masih kosong, konversi predikat otomatis (A, B, C, D), dan catatan pembina.
- **Riwayat & Arsip Multi-Periode:** Pembina dapat memilih dan memantau riwayat pendaftaran, kehadiran, dan penilaian anggota eskul dari tahun ajaran lampau dalam mode arsip aman (*read-only*).
- **Ekspor Laporan:** Pengunduhan rekapitulasi kehadiran dan data anggota per periode dalam format Excel dan CSV.

### 4. Panel Administrator & Kesiswaan
- **WhatsApp Blast Anti-Banned & Proteksi Spam:** Pengiriman pesan massal terkelola dengan batas gelombang aman (25–50 siswa per gelombang), jeda waktu manusiawi (1,5 detik per nomor), serta pembatasan tegas maksimal 1 kali pengiriman per siswa per periode pendaftaran guna menjaga reputasi nomor WhatsApp instansi dari pemblokiran Meta.
- **Pengaturan Identitas Instansi:** Kustomisasi nama instansi, logo (penyimpanan mandiri tanpa dependensi symlink), nomor kontak pengaduan, slogan, dan alamat.
- **Periode & Jendela Pendaftaran Terpadu:** Pengaturan tahun ajaran aktif dan arsip lampau menggunakan dropdown pemilih interaktif satu pintu, kontrol buka/tutup jendela pendaftaran instan, form penjadwalan otomatis, kartu metrik partisipasi kesiswaan, rincian detail periode langsung di tempat tanpa navigasi terpisah, serta tabel riwayat komparasi seluruh periode.
- **Rekapitulasi & Riwayat Multi-Periode:** Pemantauan data historis lintas tahun ajaran yang menyajikan informasi nama pembina pengampu, jumlah pendaftar per eskul, sebaran asal rombel kelas siswa, rincian siswa, serta ekspor rekapitulasi menyeluruh ke format Excel.
- **Direktori Siswa & Pelacakan Status:** Pelacakan status pemilihan eskul beserta penanda visual siklus hidup token login (Aktif, Sudah Dipakai, Dicabut, Belum Diterbitkan), filter instan tanpa reload, serta ekspor data multi-format.
- **Distribusi Kredensial WhatsApp:** Integrasi gateway WhatsApp (Fonnte) untuk penerbitan dan pengiriman kredensial login (NIS dan Token) secara mandiri tanpa tautan otomatis yang berisiko.
- **Pengelolaan Akses Pengguna:** Tata kelola akun staf, pembina eskul, wali kelas, operator, dan pimpinan sekolah.

---

## Tumpukan Teknologi

| Komponen | Teknologi |
| :--- | :--- |
| Framework Backend | Laravel 12 (PHP 8.2+) |
| Templating & UI | Blade Templates, Alpine.js |
| CSS Framework | Tailwind CSS v4 |
| Pustaka Ikon | Komponen Ikon Vektor Lucide (SVG Standar) |
| Basis Data | PostgreSQL / MySQL / SQLite |
| Gateway Notifikasi | Fonnte WhatsApp API Client |
| Pengelolaan Sesi & Autentikasi | Laravel Multi-Guard Session & Stateful Token |

---

## Struktur Direktori Utama

```text
bagimu-extra-laravel/
|-- app/
|   |-- Enums/                  Definisi nilai enum (Status, Peran, Jurusan, dll)
|   |-- Http/
|   |   |-- Controllers/
|   |   |   |-- Admin/          Pengendali modul kesiswaan, periode, dan pengaturan
|   |   |   |-- Pembina/        Pengendali modul presensi, jadwal, dan penilaian eskul
|   |   |   |-- Siswa/          Pengendali alur autentikasi dan pendaftaran peserta
|   |   |   `-- WaliKelas/      Pengendali alur perwalian dan penempatan rombel
|   |   `-- Middleware/         Pemeriksaan hak akses, pemeliharaan, dan peran
|   |-- Models/                 Definisi model Eloquent dan relasi basis data
|   `-- Support/                Kelas pembantu (format teks, ekspor file, nomor WhatsApp)
|-- database/
|   |-- migrations/             Struktur skema tabel basis data
|   `-- seeders/                Data awal pengujian dan inisialisasi akun
|-- public/
|   `-- uploads/                Direktori penyimpanan logo dan berkas statis
|-- resources/
|   `-- views/
|       |-- admin/              Halaman antarmuka kesiswaan dan administrator
|       |-- components/         Komponen Blade (ikon SVG, kartu, logo dinamis)
|       |-- pembina/            Halaman antarmuka pembina ekstrakurikuler
|       |-- siswa/              Halaman antarmuka pemilihan dan kartu peserta
|       `-- wali-kelas/         Halaman antarmuka penempatan siswa rombel
`-- routes/
    |-- admin.php               Daftar rute modul kesiswaan
    |-- pembina.php             Daftar rute modul pembina
    |-- wali-kelas.php          Daftar rute modul wali kelas
    `-- web.php                 Rute autentikasi publik dan portal siswa
```

---

## Panduan Instalasi Lokal

### Prasyarat Sistem
- PHP versi 8.2 atau lebih tinggi
- Composer versi 2.x
- Node.js versi 18 atau lebih tinggi & NPM
- Ekstensi PHP: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`

### Langkah Pemasangan

1. Kloning repositori proyek:
   ```bash
   git clone https://github.com/davidyehuda45-byte/Bagimu_Extra.git
   cd Bagimu_Extra
   ```

2. Pasang dependensi PHP dan pustaka aset:
   ```bash
   composer install
   npm install
   ```

3. Konfigurasi file lingkungan:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Sesuaikan parameter koneksi basis data pada file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=bagimu_extra
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Jalankan migrasi skema tabel beserta data awal:
   ```bash
   php artisan migrate --seed
   ```

6. Kompilasi aset antarmuka dan jalankan server pengembangan:
   ```bash
   npm run build
   php artisan serve
   ```

Aplikasi dapat diakses melalui peramban pada alamat `http://127.0.0.1:8000`.

---

## Standar Kode & Antarmuka

Proyek ini menerapkan standar teknis ketat untuk menjaga kualitas dan integritas sistem:
- **Bebas Emoji Teks:** Seluruh elemen visual antarmuka wajib menggunakan komponen ikon resmi (`<x-icon name="..." />`). Penggunaan simbol emoji teks dilarang.
- **Desain Fungsional & Bebas AI Slop:** Menghindari warna ungu, efek neon berlebih, atau gradasi multi-warna ekstrem. Menjaga palet warna korporat yang bersih, kontras tinggi, dan nyaman untuk penggunaan durasi panjang.
- **Portabilitas Deployment:** Mengutamakan direktori penyimpanan berkas publik yang tidak membutuhkan tautan simbolik (*symlink*) untuk mendukung kemudahan implementasi di lingkungan shared hosting cPanel.

---

## Lisensi

Proyek ini dilisensikan di bawah ketentuan [MIT License](LICENSE).

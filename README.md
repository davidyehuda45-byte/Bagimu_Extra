# Bagimu Extra

**Sistem Informasi & Manajemen Pendaftaran Ekstrakurikuler Digital**  
*SMK Bagimu Negeriku Semarang*

---

## 📌 Ringkasan Sistem
**Bagimu Extra** adalah platform berbasis web modern untuk menyederhanakan proses pendaftaran, manajemen kuota, presensi kehadiran, dan pelaporan kegiatan ekstrakurikuler sekolah. Sistem ini dirancang dengan antarmuka yang bersih, cepat, serta ramah pengguna bagi siswa maupun pembina non-IT.

---

## ✨ Fitur Utama

- **Pendaftaran Siswa Satu Arah (One-Shot Flow):**
  - Siswa masuk menggunakan kombinasi **NIS** dan **Token Pendaftaran** 8-karakter unik.
  - Pemilihan ekstrakurikuler dengan pemantauan kuota dan jadwal latihan interaktif.
  - Token bersifat sekali pakai (*burned on submit*); data terkunci permanen untuk mencegah manipulasi.
  - Auto-logout otomatis dengan hitung mundur 5 detik demi keamanan sesi.
- **Bukti Pendaftaran Digital & Cetak PDF:**
  - Siswa memperoleh bukti resmi pendaftaran yang dapat langsung dicetak atau disimpan sebagai PDF.
- **Portal Pembina & Presensi Pertemuan:**
  - Pintu masuk khusus pembina dengan tab berdampingan di halaman login.
  - Pencatatan kehadiran dan perizinan (*Hadir, Izin, Sakit, Alpa*) per sesi pertemuan.
- **Panel Administrator & Tata Usaha (TU):**
  - Manajemen master data siswa, penerbitan token login, kenaikan kelas, dan pembukaan jendela pendaftaran.
  - Verifikasi keabsahan sertifikat dan rekapitulasi nilai akhir.

---

## 🛠️ Tumpukan Teknologi (Tech Stack)

- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Blade Templates, React 19 (Inertia.js), Tailwind CSS, Alpine.js
- **Database:** PostgreSQL / SQLite
- **Security:** Throttle Rate Limiting, MFA / 2FA Support, Token Hashing

---

## 📄 Lisensi & Hak Cipta

Proyek ini dilindungi di bawah lisensi [MIT License](LICENSE).

```text
Copyright (c) 2026 David Yehuda Surbakti & Vines Fernando Siritoitet
SMK Bagimu Negeriku Semarang. Seluruh hak cipta dilindungi.
```

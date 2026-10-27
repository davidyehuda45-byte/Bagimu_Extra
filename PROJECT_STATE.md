# PROJECT_STATE

## Catatan Aturan Penting Pengguna (Targeted & Efficient Reading)
- **HANYA BACA FILE TERKAIT**: Jangan menganalisis file-file yang tidak ada hubungannya dengan instruksi pengguna. Jika instruksi meminta perbaikan di area tertentu (misal: kartu pendaftaran, pengaturan admin, dsb.), fokus HANYA pada file spesifik tersebut beserta file yang terhubung langsung.
- **HEMAT TOKEN & WAKTU**: Selalu langsung ke sasaran tanpa penjelajahan repo yang berlebihan.
- **DESIGN SYSTEM**: Warna aksen utama menggunakan palet **Biru** (`#1e3a8a`, `#2563eb`, blue-600/700/800/900).
- **GIT STATUS**: Tidak melakukan git commit/push tanpa instruksi eksplisit dari pengguna.

## Status Pekerjaan Terkini
1. **Periode & Jendela**: Telah disatukan menjadi satu halaman dan menu sidebar `Periode & Jendela`.
2. **Tombol Aksi**: Teks "Detail", "Ubah/Edit", "Hapus" telah diganti dengan tombol icon modern (`eye`, `pencil`, `trash`) ber-tooltip.
3. **Validasi Formulir Anti-Error Hosting**: Frontend form-validation telah aktif mencegah submit jika kolom wajib belum diisi (dengan toast peringatan + shake + border merah), dan backend dilengkapi exception guard.
4. **Kartu Bukti Pendaftaran Siswa & Tampilan Alur Siswa**: Telah selesai diimplementasikan (kartu standar ID-1 8,5 x 5,4 cm presisi dengan CSS scoped mandiri, tahun ajaran + kode periode miring, countdown 15 detik + pengingat screenshot tanpa tombol jeda, penghapusan frasa 'secara mandiri', serta penyeragaman simbol & menjadi / di seluruh halaman siswa).
5. **Modernisasi Pustaka Ikon**: Seluruh SVG ikon di `resources/views/components/icon.blade.php` dimodernisasi ke standar Lucide 2026 dengan `stroke-width="1.75"` tajam dan ramping.
6. **Pembersihan Emoji & Refactoring Kotak Catatan (Soft Blue)**: Seluruh emoji dan simbol teks kuno di seluruh halaman telah diganti dengan SVG ikon, slogan moto 4B diperbarui dengan ikon `sparkles`, dan seluruh kotak catatan/peringatan kuning dirombak ke tema Soft Blue & Slate yang sejuk dan nyaman di mata.


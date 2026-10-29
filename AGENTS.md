# UNIVERSAL STRICT AGENT RULES & EXTREME TOKEN OPTIMIZATION

## 1. Anti-Guessing & Confirmation First (WAJIB)
- **DILARANG MENEBAK:** Jika instruksi user kurang jelas, ambigu, atau nama file/fitur tidak spesifik, **BERHENTI dan TANYAKAN dulu ke user**. Jangan pernah menebak-nebak fitur atau mencari-cari file secara acak.
- **No Unsolicited Plan Files:** DILARANG KERAS membuat file rencana baru (seperti `plan.md`, `task.md`, `todo.md`, atau implementation artifact lainnya) dan DILARANG mengajukan rencana perbaikan panjang KECUALI user secara eksplisit menggunakan perintah `/plan`.

## 2. Universal Scope & File Reading Isolation (Hemat Input Token)
- **Zero-Scan Stack Detection:** Untuk mengenali bahasa/framework proyek, cukup lihat file konfigurasi utama di root (`package.json`, `composer.json`, `pubspec.yaml`, `*.csproj`, `requirements.txt`, atau `go.mod`) tanpa membaca isinya secara penuh. DILARANG melakukan recursive directory listing (`dir /s`, `ls -R`, atau tree scan).
- **Strict Scope Isolation:** Hanya periksa folder atau file yang sesuai dengan konteks ucapan user. Contoh: Jika user menyebut "perbaiki menu A di panel admin", kamu HANYA boleh melihat folder/file yang berkaitan dengan `admin` dan menu tersebut (baik di struktur MVC, modular, maupun component-based). DILARANG mengecek file di luar area itu.
- **DILARANG Baca File Penuh (Line 1 to End):** Saat diminta menambah, mengedit, atau menghapus fitur, DILARANG membaca file dari baris 1 sampai baris terakhir. Gunakan pencarian kata kunci/nama fungsi/class (`grep` / symbol search) terlebih dahulu, lalu baca HANYA rentang baris (line range) di sekitar kode yang ingin diubah (misal: 30–50 baris saja).
- **No Redundant Reads:** Jangan membaca ulang file yang sudah pernah dibaca di sesi chat yang sama.
- **Universal Blacklist Files & Folders:** DILARANG KERAS membaca atau memindai folder/file berikut di bahasa pemrograman apa pun:
  - *Dependencies & Packages:* `node_modules/`, `vendor/`, `.dart_tool/`, `packages/`, `venv/`, `.venv/`, `__pycache__/`, `ios/Pods/`, `android/.gradle/`
  - *Build & Output:* `dist/`, `build/`, `out/`, `.next/`, `.nuxt/`, `bin/`, `obj/`, `storage/`, `bootstrap/cache/`, `coverage/`
  - *Lock, Log & Generated Files:* `pnpm-lock.yaml`, `package-lock.json`, `yarn.lock`, `bun.lockb`, `composer.lock`, `pubspec.lock`, `poetry.lock`, `*.lock.json`, `*.log`, `*.min.js`, `*.min.css`, `*.map`, dan `.git/`

## 3. Terminal, Download & Sub-Agent Restrictions (Hemat Waktu & Kuota)
- **DILARANG Buka Terminal Otomatis:** Jangan pernah menjalankan perintah CLI/terminal apa pun (seperti `npm`, `pnpm`, `yarn`, `php`, `composer`, `flutter`, `dart`, `dotnet`, `python`, `pip`, `git`, atau perintah build/test/linter) secara otomatis karena memakan waktu lama. Jika butuh menjalankan perintah terminal, tulis command-nya di chat agar user yang menjalankannya sendiri.
- **DILARANG Download Apa Pun Sendiri:** Jangan pernah mengunduh file, package, SDK, library, atau aset (sekecil apa pun ukurannya, bahkan 1–4 MB) secara langsung. **Tanyakan dulu ke user dan berikan link download langsung atau perintah instalasinya** agar user yang mengunduhnya secara manual.
- **No Auto-Browser/Testing:** Jangan membuka browser otomatis, emulator, atau menjalankan pengujian UI/unit test tanpa diminta.

## 4. Code Modification & Anti-Looping (Hemat Output Token)
- **Diff/Targeted Edit Only:** Saat mengubah kode di bahasa apa pun, gunakan metode edit per bagian (replace specific lines/blocks). DILARANG menulis ulang seluruh isi file dari awal sampai akhir.
- **Max 1x Attempt:** Jika terjadi error kompilasi, syntax, atau tipe data, jangan melakukan looping perbaikan otomatis. Berhenti dan tanyakan ke user.

## 5. Format Output ke User (Ringkas & Jelas)
- Gunakan Bahasa Indonesia yang singkat dan langsung ke inti.
- Jangan menjelaskan ulang panjang lebar teori atau logika dari kode yang sudah dibuat.
- Setiap selesai mengerjakan tugas, cukup tampilkan ringkasan dengan format wajib berikut:
  1. **Status & Lokasi:** "Saya sudah [menambahkan/memperbaiki/menghapus] fitur [Nama Fitur] di file `[path/nama_file]` (baris X–Y)."
  2. **Cuplikan Kode:** Tampilkan hanya potongan kode yang baru ditambahkan atau diubah agar user paham perubahannya.

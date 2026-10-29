@extends('layouts.staff')

@section('title', 'Pengaturan')

@php use SimpleSoftwareIO\QrCode\Facades\QrCode as SimpleQr; @endphp

@section('content')
<h1 class="text-xl font-bold text-slate-900 mb-4">Pengaturan</h1>

{{-- Pengguna staf --}}
<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm mb-4">
    <h2 class="font-bold text-slate-900 text-sm mb-3">Akun Administrator & Operator</h2>
    <div class="overflow-x-auto mb-4">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b text-left text-slate-500">
                    <th class="py-2 px-3">Nama</th>
                    <th class="py-2 px-3">Email</th>
                    <th class="py-2 px-3">Username</th>
                    <th class="py-2 px-3">Peran</th>
                    <th class="py-2 px-3">MFA</th>
                    <th class="py-2 px-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $u)
                    <tr class="border-b hover:bg-slate-50">
                        <td class="py-2 px-3 font-medium">{{ $u->name }}</td>
                        <td class="py-2 px-3">{{ $u->email }}</td>
                        <td class="py-2 px-3">{{ $u->username ?? '-' }}</td>
                        <td class="py-2 px-3">{{ $u->role->label() }}</td>
                        <td class="py-2 px-3">{{ $u->totp_enabled ? 'Aktif' : '-' }}</td>
                        <td class="py-2 px-3">
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('admin.users.role', $u) }}" class="flex items-center gap-1">
                                    @csrf
                                    @method('PUT')
                                    <select name="role" class="rounded-lg border-slate-300 py-1 text-xs">
                                        @foreach (['ADMIN' => 'Admin', 'OPERATOR' => 'Operator', 'KEPALA_SEKOLAH' => 'Kepala Sekolah'] as $val => $label)
                                            <option value="{{ $val }}" @selected($u->role->value === $val)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="inline-flex items-center justify-center h-7 w-7 rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 transition shadow-2xs" title="Simpan Perubahan Role">
                                        <x-icon name="check" class="h-3.5 w-3.5" />
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Hapus akun {{ $u->email }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center h-7 w-7 rounded-lg border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-100 hover:border-rose-300 transition shadow-2xs cursor-pointer" title="Hapus Akun">
                                        <x-icon name="trash" class="h-3.5 w-3.5" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-4 text-center text-slate-500">Belum ada akun staf.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mb-4">{{ $users->links() }}</div>
    <h3 class="font-bold text-sm text-slate-900 mb-2">Tambah Akun Admin/Operator</h3>
    <form method="POST" action="{{ route('admin.users.store') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
        @csrf
        <div><label class="block font-medium text-slate-700 mb-1">Nama</label><input name="name" required maxlength="100" class="w-full border-slate-300 rounded-xl"></div>
        <div><label class="block font-medium text-slate-700 mb-1">Email</label><input name="email" type="email" required class="w-full border-slate-300 rounded-xl"></div>
        <div><label class="block font-medium text-slate-700 mb-1">Username (opsional)</label><input name="username" maxlength="30" class="w-full border-slate-300 rounded-xl"></div>
        <div><label class="block font-medium text-slate-700 mb-1">Password (min. 8)</label><input name="password" type="text" required minlength="8" class="w-full border-slate-300 rounded-xl"></div>
        <div><label class="block font-medium text-slate-700 mb-1">Peran</label>
            <select name="role" class="w-full border-slate-300 rounded-xl">
                <option value="ADMIN">Admin</option>
                <option value="OPERATOR">Operator</option>
                <option value="KEPALA_SEKOLAH">Kepala Sekolah</option>
            </select>
        </div>
        <div class="flex items-end"><button class="px-4 py-2 rounded-xl bg-blue-700 text-white font-bold hover:bg-blue-800">Buat Akun</button></div>
    </form>
    <p class="mt-2 text-xs text-slate-500">Catatan: Pengelolaan dan penugasan akun Pembina dilakukan di menu <a href="{{ route('admin.users.index') }}" class="text-blue-600 font-semibold hover:underline">Kelola Pembina</a>.</p>
</div>

{{-- Identitas --}}
<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm mb-4">
    <div class="mb-4">
        <h2 class="font-bold text-slate-900 text-sm">Identitas & Branding Sekolah</h2>
        <p class="text-xs text-slate-500">Sesuaikan nama sekolah, kontak WhatsApp, alamat, slogan 4B, serta logo agar aplikasi siap digunakan dan dipersonalisasi sesuai kebutuhan instansi.</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-4 text-sm w-full">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="school_name" class="block font-medium text-slate-700 mb-1">Nama Sekolah / Instansi</label>
                <input type="text" name="school_name" id="school_name" value="{{ old('school_name', $settings['school.name'] ?? '') }}" placeholder="Contoh: SMK Bagimu Negeriku" class="w-full border-slate-300 rounded-xl text-sm">
            </div>

            <div>
                <label for="school_contact" class="block font-medium text-slate-700 mb-1">No. WhatsApp / Kontak Narahubung</label>
                <input type="text" name="school_contact" id="school_contact" value="{{ old('school_contact', $settings['school.contact'] ?? '') }}" placeholder="Contoh: 081234567890 / 6281234567890" class="w-full border-slate-300 rounded-xl text-sm">
            </div>

            <div class="sm:col-span-2">
                <label for="school_motto" class="block font-medium text-slate-700 mb-1">Slogan / Motto Sekolah</label>
                <input type="text" name="school_motto" id="school_motto" value="{{ old('school_motto', $settings['school.motto'] ?? '') }}" placeholder="Contoh: 4B Berbudi · Berkualitas · Berdaya · Berhasil" class="w-full border-slate-300 rounded-xl text-sm">
                <p class="text-[11px] text-slate-400 mt-0.5">Slogan ini ditampilkan pada layar login siswa, splash screen, dan banner portal.</p>
            </div>

            <div class="sm:col-span-2">
                <label for="school_address" class="block font-medium text-slate-700 mb-1">Alamat Lengkap Sekolah</label>
                <textarea name="school_address" id="school_address" rows="2" placeholder="Contoh: Jl. Palir Raya No. 66, Podorejo, Ngaliyan, Kota Semarang" class="w-full border-slate-300 rounded-xl text-sm">{{ old('school_address', $settings['school.address'] ?? '') }}</textarea>
            </div>
        </div>

        {{-- Logo Sekolah --}}
        <div class="pt-2 border-t border-slate-100">
            <label class="block font-medium text-slate-700 mb-2">Logo Sekolah</label>
            @php
                $currentLogo = $settings['school.logo'] ?? '';
                $currentLogoUrl = $currentLogo ? (str_starts_with($currentLogo, 'http') ? $currentLogo : asset(ltrim($currentLogo, '/'))) : asset('logo.png');
            @endphp
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-3.5 bg-slate-50/80 rounded-2xl border border-slate-200/80 mb-3">
                <div class="h-16 w-16 rounded-xl bg-white p-1 border border-slate-200 flex items-center justify-center overflow-hidden shrink-0 shadow-2xs">
                    <img src="{{ $currentLogoUrl }}" alt="Logo Saat Ini" class="max-h-full max-w-full object-contain" onerror="this.onerror=null; this.src='{{ asset('logo.png') }}';">
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-800">Logo Aktif Saat Ini</p>
                    <p class="text-[11px] text-slate-500 truncate">{{ $currentLogo ?: 'Default bawaan (/logo.png)' }}</p>
                    @if ($currentLogo)
                        <label class="inline-flex items-center gap-1.5 mt-2 text-xs text-rose-600 font-medium cursor-pointer hover:underline">
                            <input type="checkbox" name="reset_logo" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                            <span>Hapus logo kustom & kembalikan ke default bawaan</span>
                        </label>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Unggah Berkas Logo Baru <span class="text-slate-400 font-normal">(PNG, JPG, SVG, WEBP maks. 2MB)</span></label>
                    <input type="file" name="school_logo_file" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-slate-300 rounded-xl bg-white">
                </div>
                <div>
                    <label for="school_logo" class="block text-xs font-semibold text-slate-600 mb-1">Atau Masukkan Path / URL Logo <span class="text-slate-400 font-normal">(Opsional)</span></label>
                    <input type="text" name="school_logo" id="school_logo" value="{{ old('school_logo', $settings['school.logo'] ?? '') }}" placeholder="Contoh: /logo.png atau https://..." class="w-full border-slate-300 rounded-xl text-xs sm:text-sm">
                </div>
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-700 text-white text-sm font-bold hover:bg-blue-800 transition shadow-sm cursor-pointer">
                Simpan Identitas & Slogan
            </button>
        </div>
    </form>
</div>

{{-- Syarat eskul --}}
<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm mb-4">
    <h2 class="font-bold text-slate-900 text-sm mb-1">Syarat Ikut Eskul</h2>
    <p class="text-xs text-slate-500 mb-3">Kelas 11 semester 2 sedang magang — batasi agar hanya semester 1 yang bisa daftar.</p>
    <form method="POST" action="{{ route('admin.settings.eligibility') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm w-full">
        @csrf
        <div>
            <label class="block font-medium text-slate-700 mb-1">Tingkat boleh (cth: 10,11)</label>
            <input type="text" name="kelas_allowed" value="{{ old('kelas_allowed', $elig['kelas']) }}" required class="w-full border-slate-300 rounded-xl">
        </div>
        <div>
            <label class="block font-medium text-slate-700 mb-1">Semester aktif</label>
            <select name="semester_aktif" class="w-full border-slate-300 rounded-xl">
                <option value="1" @selected(old('semester_aktif', $elig['semester']) === '1')>Semester 1</option>
                <option value="2" @selected(old('semester_aktif', $elig['semester']) === '2')>Semester 2</option>
            </select>
        </div>
        <div class="flex items-end">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="kelas11_smt1_only" value="1" @checked(old('kelas11_smt1_only', $elig['kelas11_smt1'])) class="rounded border-slate-300 text-blue-700 focus:ring-blue-500">
                <span class="text-slate-700">Kelas 11 hanya semester 1</span>
            </label>
        </div>
        <div class="sm:col-span-3"><button class="px-4 py-2 rounded-xl bg-blue-700 text-white text-sm font-bold hover:bg-blue-800">Simpan Syarat</button></div>
    </form>
</div>

{{-- Pemeliharaan --}}
<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm mb-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
        <div>
            <h2 class="font-bold text-slate-900 text-sm">Mode Pemeliharaan (Maintenance)</h2>
            <p class="text-xs text-slate-500">Saat aktif, siswa yang mengakses sistem akan melihat halaman khusus perawatan. Admin tetap dapat login dan mengakses sistem secara normal.</p>
        </div>
        <div>
            @if ($maintenance)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700 ring-1 ring-inset ring-rose-600/20">
                    <span class="h-2 w-2 rounded-full bg-rose-600 animate-pulse"></span>
                    AKTIF
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                    <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                    Nonaktif
                </span>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.maintenance') }}" class="space-y-4 w-full text-sm">
        @csrf
        <div>
            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox" name="aktif" value="1" @checked(old('aktif', $maintenance)) class="rounded border-slate-300 text-blue-700 focus:ring-blue-500 h-4 w-4">
                <span class="font-semibold text-slate-800">Aktifkan mode pemeliharaan sekarang</span>
            </label>
        </div>

        <div>
            <label class="block font-medium text-slate-700 mb-1">Pesan Penjelasan untuk Siswa</label>
            <textarea name="pesan" rows="2" maxlength="500" placeholder="Contoh: Sistem sedang dalam pemeliharaan berkala untuk peningkatan performa pendaftaran eskul." class="w-full border-slate-300 rounded-xl text-sm">{{ old('pesan', $maintenanceMsg) }}</textarea>
            <p class="text-[11px] text-slate-400 mt-0.5">Pesan ini akan ditampilkan secara langsung di halaman pemeliharaan yang dilihat siswa.</p>
        </div>

        <div>
            <label class="block font-medium text-slate-700 mb-1">Estimasi Waktu Selesai <span class="text-slate-400 font-normal">(Opsional)</span></label>
            <input type="text" name="eta" value="{{ old('eta', $maintenanceEta) }}" maxlength="100" placeholder="Contoh: 13 Oktober 2026, 17:00 WIB atau Sekitar 30 Menit" class="w-full border-slate-300 rounded-xl text-sm">
        </div>

        <button class="px-5 py-2.5 rounded-xl bg-blue-700 text-white text-sm font-bold hover:bg-blue-800 transition shadow-sm">
            Simpan Pengaturan Pemeliharaan
        </button>
    </form>
</div>

{{-- MFA saya --}}
<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <h2 class="font-bold text-slate-900 text-sm mb-1">MFA Saya (Authenticator)</h2>
    @if ($me->totp_enabled)
        <p class="text-sm text-slate-600">MFA <b class="text-emerald-600">aktif</b> untuk akun Anda.</p>
        <form method="POST" action="{{ route('admin.settings.mfa.disable') }}" class="mt-3 flex flex-wrap gap-3 items-end text-sm">
            @csrf
            <div>
                <label class="block font-medium text-slate-700 mb-1">Password (konfirmasi)</label>
                <input type="password" name="password" required autocomplete="current-password" class="border-slate-300 rounded-xl">
            </div>
            <button class="px-4 py-2 rounded-xl border border-slate-300 font-semibold hover:bg-slate-50" onclick="return confirm('Nonaktifkan MFA?')">Nonaktifkan</button>
        </form>
    @elseif ($qrUrl)
        <p class="text-sm text-slate-600">Pindai QR dengan aplikasi authenticator, lalu masukkan 6 digit kode.</p>
        <div class="mt-2 bg-white inline-block border border-slate-200 rounded-xl p-2">{!! SimpleQr::size(180)->generate($qrUrl) !!}</div>
        <form method="POST" action="{{ route('admin.settings.mfa.confirm') }}" class="mt-3 flex flex-wrap gap-3 items-end text-sm">
            @csrf
            <div>
                <label class="block font-medium text-slate-700 mb-1">Kode 6 digit</label>
                <input type="text" name="code" required inputmode="numeric" maxlength="6" class="border-slate-300 rounded-xl font-mono tracking-widest text-center">
            </div>
            <button class="px-4 py-2 rounded-xl bg-blue-700 text-white font-bold hover:bg-blue-800">Konfirmasi</button>
        </form>
    @else
        <p class="text-sm text-slate-600">Belum aktif. Aktifkan untuk keamanan ganda saat login.</p>
        <form method="POST" action="{{ route('admin.settings.mfa') }}" class="mt-3">
            @csrf
            <button class="px-4 py-2 rounded-xl bg-blue-700 text-white text-sm font-bold hover:bg-blue-800">Aktifkan MFA</button>
        </form>
    @endif
</div>
@endsection

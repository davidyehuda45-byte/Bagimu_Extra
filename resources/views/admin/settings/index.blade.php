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
                                    <button class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold hover:bg-slate-50">Ubah</button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Hapus akun {{ $u->email }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg px-2 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50">Hapus</button>
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
    <h2 class="font-bold text-slate-900 text-sm mb-3">Identitas Sekolah</h2>
    <form method="POST" action="{{ route('admin.settings.update') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm w-full">
        @csrf
        @method('PUT')
        @foreach ($labels as $key => $label)
            <div class="{{ $key === 'school.address' ? 'sm:col-span-2' : '' }}">
                <label for="{{ str_replace('.', '_', $key) }}" class="block font-medium text-slate-700 mb-1">{{ $label }}</label>
                @if ($key === 'school.address')
                    <textarea name="{{ str_replace('.', '_', $key) }}" id="{{ str_replace('.', '_', $key) }}" rows="2" class="w-full border-slate-300 rounded-xl">{{ old(str_replace('.', '_', $key), $settings[$key] ?? '') }}</textarea>
                @else
                    <input type="text" name="{{ str_replace('.', '_', $key) }}" id="{{ str_replace('.', '_', $key) }}" value="{{ old(str_replace('.', '_', $key), $settings[$key] ?? '') }}" class="w-full border-slate-300 rounded-xl">
                @endif
            </div>
        @endforeach
        <div class="sm:col-span-2"><button class="px-4 py-2 rounded-xl bg-blue-700 text-white text-sm font-bold hover:bg-blue-800">Simpan Identitas</button></div>
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

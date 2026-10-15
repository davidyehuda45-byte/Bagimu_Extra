@extends('layouts.siswa')

@section('title', 'Profil Saya')

@section('content')
<div class="space-y-4">
    <h1 class="text-lg font-bold text-slate-900">Profil Akun</h1>

    {{-- Identitas --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-100 text-lg font-bold text-blue-800">
                {{ strtoupper(substr($user->name ?? '?', 0, 1)) }}
            </span>
            <div class="min-w-0">
                <p class="truncate font-bold text-slate-900">{{ $user->name }}</p>
                <p class="truncate text-xs text-slate-500">NIS {{ $user->student?->nis ?? $user->username }} • Akun siswa</p>
            </div>
        </div>
        <div class="mt-3 flex flex-wrap gap-1.5">
            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold whitespace-nowrap bg-slate-100 text-slate-700 ring-1 ring-inset ring-slate-500/20">@ {{ $user->username }}</span>
            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold whitespace-nowrap {{ ($user->student_status?->value ?? 'AKTIF') === 'AKTIF' ? 'bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-600/20' : 'bg-amber-100 text-amber-800 ring-1 ring-inset ring-amber-600/20' }}">{{ $user->student_status?->label() ?? 'Aktif' }}</span>
            @if ($user->email_verified_at)
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold whitespace-nowrap bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-600/20">Email terverifikasi</span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold whitespace-nowrap bg-slate-100 text-slate-700 ring-1 ring-inset ring-slate-500/20">Akun NIS</span>
            @endif
        </div>
        <dl class="mt-3 space-y-1 text-sm">
            <div class="flex justify-between"><dt class="text-slate-500">NIS</dt><dd class="font-semibold text-slate-900">{{ $user->student?->nis ?? $user->username }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Kelas</dt><dd class="font-semibold text-slate-900">{{ $terakhir?->kelas ?? ($user->student?->kelas ?? '—') }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Jurusan</dt><dd class="font-semibold text-slate-900">{{ ($terakhir?->jurusan ?? $user->student?->jurusan)?->label() ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">WhatsApp</dt><dd class="font-semibold text-slate-900">{{ $terakhir?->whatsapp ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Wali</dt><dd class="font-semibold text-slate-900">{{ $user->wali_nama ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Kontak Wali</dt><dd class="font-semibold text-slate-900">{{ $user->wali_kontak ?? '—' }}</dd></div>
        </dl>
    </div>

    {{-- Form profil --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <h2 class="font-bold text-slate-900">Ubah Profil</h2>
        <form method="POST" action="{{ route('siswa.profil.update') }}" class="mt-3 space-y-3">
            @csrf
            @method('PUT')
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required minlength="2" maxlength="100"
                       class="mt-1 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="wali_nama" class="block text-sm font-medium text-slate-700">Nama Wali</label>
                    <input id="wali_nama" name="wali_nama" type="text" value="{{ old('wali_nama', $user->wali_nama) }}" maxlength="100"
                           class="mt-1 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="wali_kontak" class="block text-sm font-medium text-slate-700">Kontak Wali</label>
                    <input id="wali_kontak" name="wali_kontak" type="text" value="{{ old('wali_kontak', $user->wali_kontak) }}" maxlength="20" placeholder="08…"
                           class="mt-1 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label for="whatsapp" class="block text-sm font-medium text-slate-700">No. WhatsApp Saya</label>
                <input id="whatsapp" name="whatsapp" type="text" value="{{ old('whatsapp', $terakhir?->whatsapp) }}" maxlength="20"
                       class="mt-1 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="space-y-2 text-sm">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="notify_wa" value="1" @checked(old('notify_wa', $user->notify_wa)) class="rounded border-slate-300 text-blue-700 focus:ring-blue-500">
                    <span class="text-slate-700">Kirim notifikasi via WhatsApp</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="notify_email" value="1" @checked(old('notify_email', $user->notify_email)) class="rounded border-slate-300 text-blue-700 focus:ring-blue-500">
                    <span class="text-slate-700">Kirim notifikasi via Email</span>
                </label>
                <p class="text-[11px] text-slate-400">Notifikasi penting tetap muncul di aplikasi walau kanal dimatikan.</p>
            </div>
            <button class="w-full rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-blue-800">Simpan Profil</button>
        </form>
    </div>

    {{-- Password --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <h2 class="font-bold text-slate-900">{{ $user->password_set_at ? 'Ganti Password' : 'Atur Password' }}</h2>
        @if (!$user->password_set_at)
            <p class="mt-1 text-sm text-slate-500">Belum punya password. Atur di sini untuk login alternatif pakai NIS + password.</p>
        @endif
        <form method="POST" action="{{ route('siswa.profil.password') }}" class="mt-3 space-y-3">
            @csrf
            @if ($user->password_set_at)
                <div>
                    <label for="current_password" class="block text-sm font-medium text-slate-700">Password Lama</label>
                    <input id="current_password" name="current_password" type="password" required autocomplete="current-password"
                           class="mt-1 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            @endif
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Password Baru (min. 8)</label>
                <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password"
                       class="mt-1 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi Password Baru</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                       class="mt-1 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <button class="w-full rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-blue-800">
                {{ $user->password_set_at ? 'Ganti Password' : 'Simpan Password' }}
            </button>
        </form>
    </div>

    {{-- Notifikasi --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <h2 class="font-bold text-slate-900">Notifikasi Dalam Aplikasi</h2>
        @if ($notifikasi->isEmpty())
            <p class="mt-1 text-sm text-slate-500">Belum ada notifikasi.</p>
        @else
            <ul class="mt-2 divide-y divide-slate-100">
                @foreach ($notifikasi as $n)
                    <li class="py-2">
                        <p class="text-sm font-semibold text-slate-900">{{ $n->judul }}</p>
                        <p class="text-xs text-slate-600">{{ $n->isi }}</p>
                        <p class="text-[11px] text-slate-400">{{ $n->created_at->format('d M Y H:i') }}</p>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Aktivitas login --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <h2 class="font-bold text-slate-900">Aktivitas Login Terakhir</h2>
        @if ($logins->isEmpty())
            <p class="mt-1 text-sm text-slate-500">Belum ada riwayat.</p>
        @else
            <ul class="mt-2 divide-y divide-slate-100">
                @foreach ($logins as $l)
                    <li class="flex items-center gap-2 py-2 text-xs">
                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 font-semibold {{ $l->success ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">{{ $l->success ? 'Berhasil' : 'Gagal' }}</span>
                        <span class="text-slate-600">{{ method_exists($l->method, 'label') ? $l->method->label() : $l->method->value }}@if ($l->reason) • {{ $l->reason }}@endif</span>
                        <span class="ms-auto shrink-0 text-slate-400">{{ $l->created_at->format('d M H:i') }}</span>
                    </li>
                @endforeach
            </ul>
            <p class="mt-2 text-[11px] text-slate-400">Ada aktivitas mencurigakan? Segera lapor ke Admin/TU.</p>
        @endif
    </div>
</div>
@endsection

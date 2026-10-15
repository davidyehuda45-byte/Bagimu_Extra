@extends('layouts.staff')

@section('title', 'Detail Pendaftaran')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-bold text-slate-900">Detail Pendaftaran — {{ $registration->user?->name ?? '#' }}</h1>
    <a href="{{ route('admin.registrations.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-sm bg-white">Kembali</a>
</div>

{{-- Data --}}
<div class="rounded-2xl border border-slate-200 bg-white p-5 mb-4 text-sm shadow-sm">
    <div class="flex flex-wrap items-center gap-2 mb-3">
        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $registration->status->value === 'APPROVED' ? 'bg-emerald-100 text-emerald-800' : ($registration->status->value === 'PENDING' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">{{ $registration->status->label() }}</span>
        <span class="font-mono text-xs text-slate-500">{{ $registration->kode() }}</span>
        @if ($registration->certificate)
            <span class="font-mono text-xs text-slate-500">Sertifikat {{ $registration->certificate->nomor }} (v{{ $registration->certificate->doc_version }})</span>
        @endif
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-1">
        <p><span class="text-slate-500">Nama:</span> {{ $registration->user?->name ?? '-' }}</p>
        <p><span class="text-slate-500">Email:</span> {{ $registration->user?->email ?? '-' }}</p>
        <p><span class="text-slate-500">NIS:</span> {{ $registration->nis }}</p>
        <p><span class="text-slate-500">Periode:</span> {{ $registration->periode }}</p>
        <p><span class="text-slate-500">Kelas:</span> {{ $registration->kelas ?? '-' }}</p>
        <p><span class="text-slate-500">No. Absen:</span> {{ $registration->no_absen ?? '-' }}</p>
        <p><span class="text-slate-500">Wali Kelas:</span> {{ $registration->nama_wali_kelas ?? '-' }}</p>
        <p><span class="text-slate-500">Jurusan:</span> {{ $registration->jurusan?->label() ?? $registration->jurusan }}</p>
        <p><span class="text-slate-500">Usia:</span> {{ $registration->usia }}</p>
        <p><span class="text-slate-500">Jenis Kelamin:</span> {{ $registration->gender?->label() ?? '-' }}</p>
        <p><span class="text-slate-500">WhatsApp:</span>
            @if ($registration->whatsapp && \App\Support\Whatsapp::link($registration->whatsapp))
                <a href="{{ \App\Support\Whatsapp::link($registration->whatsapp) }}" target="_blank" rel="noopener" class="font-medium text-emerald-600 hover:text-emerald-700">{{ $registration->whatsapp }}</a>
            @else
                -
            @endif
        </p>
        <p><span class="text-slate-500">Tgl Daftar/Ubah:</span> {{ $registration->created_at?->format('d/m/Y H:i') }} / {{ $registration->updated_at?->format('d/m/Y H:i') }}</p>
        <p><span class="text-slate-500">Eskul Pilihan:</span> {{ $registration->eskul_utama }}</p>
        <p class="sm:col-span-2"><span class="text-slate-500">Alasan:</span> {{ $registration->alasan ?? '-' }}</p>
        @if ($registration->alasan_penolakan)
            <p class="sm:col-span-2 rounded-xl bg-rose-50 border border-rose-200 p-3 text-rose-800"><b>Alasan Penolakan:</b> {{ $registration->alasan_penolakan }}</p>
        @endif
    </div>
    <div class="mt-3 border-t border-slate-100 pt-3">
        <p><span class="text-slate-500">Nilai:</span> {{ $registration->nilai ?? '-' }} · Predikat: {{ $registration->predikat ?? '-' }} · Catatan: {{ $registration->catatan_nilai ?? '-' }}</p>
        <p class="mt-1 text-slate-600">Kehadiran: total {{ $kehadiranTotal }} · Hadir: {{ $registration->kehadiran->where('status', \App\Enums\KehadiranStatus::HADIR)->count() }}</p>
    </div>
</div>

{{-- Persetujuan dihapus: pendaftaran langsung tercatat tanpa antrean. --}}

{{-- Edit data --}}
<div class="rounded-2xl border border-slate-200 bg-white p-5 mb-4 shadow-sm">
    <h2 class="font-bold text-sm text-slate-900 mb-3">Edit Data Pendaftaran</h2>
    <form method="POST" action="{{ route('admin.registrations.update', $registration) }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
        @csrf
        @method('PUT')
        <div><label class="block font-medium text-slate-700 mb-1">Kelas</label><input name="kelas" value="{{ old('kelas', $registration->kelas) }}" required class="w-full border-slate-300 rounded-xl"></div>
        <div><label class="block font-medium text-slate-700 mb-1">Jurusan</label>
            <select name="jurusan" class="w-full border-slate-300 rounded-xl">
                @foreach (['RPL', 'KULINER', 'DKV', 'TP', 'TKP'] as $j)
                    <option value="{{ $j }}" @selected(old('jurusan', $registration->jurusan?->value) === $j)>{{ $j }}</option>
                @endforeach
            </select>
        </div>
        <div><label class="block font-medium text-slate-700 mb-1">Usia</label><input name="usia" type="number" min="13" max="20" value="{{ old('usia', $registration->usia) }}" required class="w-full border-slate-300 rounded-xl"></div>
        <div><label class="block font-medium text-slate-700 mb-1">WhatsApp</label><input name="whatsapp" value="{{ old('whatsapp', $registration->whatsapp) }}" class="w-full border-slate-300 rounded-xl"></div>
        <div><label class="block font-medium text-slate-700 mb-1">No. Absen</label><input name="no_absen" type="number" min="1" max="99" value="{{ old('no_absen', $registration->no_absen) }}" required class="w-full border-slate-300 rounded-xl"></div>
        <div><label class="block font-medium text-slate-700 mb-1">Wali Kelas</label><input name="nama_wali_kelas" value="{{ old('nama_wali_kelas', $registration->nama_wali_kelas) }}" class="w-full border-slate-300 rounded-xl"></div>
        <div><label class="block font-medium text-slate-700 mb-1">NIS</label><input name="nis" value="{{ old('nis', $registration->nis) }}" class="w-full border-slate-300 rounded-xl"></div>
        <div><label class="block font-medium text-slate-700 mb-1">Gender</label>
            <select name="gender" class="w-full border-slate-300 rounded-xl">
                <option value="LAKI_LAKI" @selected(old('gender', $registration->gender?->value) === 'LAKI_LAKI')>Laki-laki</option>
                <option value="PEREMPUAN" @selected(old('gender', $registration->gender?->value) === 'PEREMPUAN')>Perempuan</option>
            </select>
        </div>
        <div><label class="block font-medium text-slate-700 mb-1">Eskul Utama</label>
            <select name="eskul_utama" class="w-full border-slate-300 rounded-xl">
                @foreach ($eskulOptions as $o)
                    <option value="{{ $o['name'] }}" @selected(old('eskul_utama', $registration->eskul_utama) === $o['name'])>{{ $o['name'] }} ({{ $o['info'] }})</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-2"><label class="block font-medium text-slate-700 mb-1">Alasan</label><textarea name="alasan" rows="2" maxlength="1000" class="w-full border-slate-300 rounded-xl">{{ old('alasan', $registration->alasan) }}</textarea></div>
        <div class="sm:col-span-3"><button class="px-4 py-2 rounded-xl bg-blue-700 text-white text-sm font-bold hover:bg-blue-800">Simpan Perubahan</button></div>
    </form>
</div>

{{-- Nilai --}}
<div class="rounded-2xl border border-slate-200 bg-white p-5 mb-4 shadow-sm">
    <h2 class="font-bold text-sm text-slate-900 mb-3">Penilaian</h2>
    <form method="POST" action="{{ route('admin.registrations.nilai', $registration) }}" class="flex flex-wrap gap-3 items-end text-sm">
        @csrf
        <div><label class="block font-medium text-slate-700 mb-1">Nilai (0–100)</label><input name="nilai" type="number" min="0" max="100" value="{{ old('nilai', $registration->nilai) }}" required class="border-slate-300 rounded-xl w-28"></div>
        <div class="flex-1 min-w-52"><label class="block font-medium text-slate-700 mb-1">Catatan</label><input name="catatan" value="{{ old('catatan', $registration->catatan_nilai) }}" maxlength="1000" class="w-full border-slate-300 rounded-xl"></div>
        <button class="px-4 py-2 rounded-xl bg-blue-700 text-white font-bold hover:bg-blue-800">Simpan Nilai</button>
    </form>
    <p class="mt-1 text-[11px] text-slate-400">Predikat otomatis: A ≥ 85, B ≥ 70, C ≥ 55, D &lt; 55.</p>
    @if ($registration->nilaiHistory->isNotEmpty())
        <ul class="mt-2 space-y-1 text-xs text-slate-600">
            @foreach ($registration->nilaiHistory->sortByDesc('created_at') as $nh)
                <li><b>{{ $nh->nilai }} ({{ $nh->predikat }})</b> • {{ $nh->created_at?->format('d/m/Y H:i') }} • {{ $nh->actor_email ?? '—' }}@if ($nh->catatan) • {{ $nh->catatan }}@endif</li>
            @endforeach
        </ul>
    @endif
</div>

{{-- Kartu & sertifikat --}}
@if (auth()->user()->hasRole(\App\Enums\Role::ADMIN, \App\Enums\Role::OPERATOR))
<div class="grid gap-4 sm:grid-cols-2 mb-4">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="font-bold text-sm text-slate-900 mb-3">Kartu & Sertifikat</h2>
        <div class="flex flex-wrap gap-2 text-sm">
            <form method="POST" action="{{ route('admin.registrations.cabut', $registration) }}" onsubmit="return confirm('Cabut kartu & terbitkan QR baru? QR lama hangus.')">
                @csrf
                <button class="px-4 py-2 rounded-xl border border-slate-300 font-semibold hover:bg-slate-50">Cabut Kartu & QR Baru</button>
            </form>
            @if ($registration->status->value === 'APPROVED' && !$registration->certificate)
                <form method="POST" action="{{ route('admin.registrations.sertifikat', $registration) }}">
                    @csrf
                    <button class="px-4 py-2 rounded-xl bg-blue-700 text-white font-bold hover:bg-blue-800">Terbitkan Sertifikat</button>
                </form>
            @endif
        </div>
        <p class="mt-2 text-[11px] text-slate-400">QR lama hangus saat dicabut. Sertifikat idempoten (1 per pendaftar).</p>
    </div>

    {{-- Token & password --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="font-bold text-sm text-slate-900 mb-3">Token & Password Akun</h2>
        <p class="text-sm text-slate-600">Token saat ini:
            @if ($registration->user?->login_token && !$registration->user?->login_token_revoked_at)
                <span class="font-mono font-bold tracking-widest">{{ $registration->user->login_token }}</span>
            @else
                <span class="text-slate-400">— belum ada —</span>
            @endif
        </p>
        <div class="mt-2 flex flex-wrap gap-2 text-sm">
            <form method="POST" action="{{ route('admin.registrations.token', $registration) }}" onsubmit="return confirm('Buat token baru? Token lama hangus.')">
                @csrf
                <button class="px-4 py-2 rounded-xl border border-slate-300 font-semibold hover:bg-slate-50">{{ $registration->user?->login_token_hash ? 'Reset Token' : 'Buat Token' }}</button>
            </form>
        </div>
        <form method="POST" action="{{ route('admin.registrations.password', $registration) }}" class="mt-3 flex gap-2 items-end text-sm">
            @csrf
            <div class="flex-1">
                <label class="block font-medium text-slate-700 mb-1">Password baru (min. 8)</label>
                <input name="password" type="text" required minlength="8" maxlength="100" class="w-full border-slate-300 rounded-xl">
            </div>
            <button class="px-4 py-2 rounded-xl border border-slate-300 font-semibold hover:bg-slate-50">Reset Password</button>
        </form>
    </div>
</div>
@endif

{{-- Riwayat --}}
<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <h2 class="font-bold text-sm text-slate-900 mb-3">Riwayat Perubahan Status</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b text-left text-slate-500">
                    <th class="py-2 px-3">Waktu</th>
                    <th class="py-2 px-3">Dari</th>
                    <th class="py-2 px-3">Ke</th>
                    <th class="py-2 px-3">Aktor</th>
                    <th class="py-2 px-3">Alasan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($registration->statusHistory->sortByDesc('created_at') as $h)
                    <tr class="border-b hover:bg-slate-50">
                        <td class="py-2 px-3">{{ $h->created_at?->format('d/m/Y H:i') }}</td>
                        <td class="py-2 px-3">{{ $h->old_status?->label() ?? '—' }}</td>
                        <td class="py-2 px-3">{{ $h->new_status?->label() ?? $h->new_status }}</td>
                        <td class="py-2 px-3">{{ $h->actor_email ?? '-' }}</td>
                        <td class="py-2 px-3">{{ $h->reason ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-4 px-3 text-center text-slate-500">Belum ada riwayat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

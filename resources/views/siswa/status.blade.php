@extends('layouts.siswa')

@section('title', 'Status & E-Card')

@php use SimpleSoftwareIO\QrCode\Facades\QrCode as SimpleQr; @endphp

@section('content')
<div class="space-y-4">
    <h1 class="text-lg font-bold text-slate-900">Status & E-Card</h1>

    @if (!$aktif)
        <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
            <p class="font-bold text-slate-900">Belum ada pendaftaran.</p>
            <a href="{{ route('siswa.daftar') }}" class="mt-3 inline-block rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-blue-800">Daftar Sekarang</a>
        </div>
    @else
        <div class="no-print flex gap-2">
            <button onclick="window.print()" class="rounded-xl bg-blue-700 px-4 py-2 text-sm font-bold text-white hover:bg-blue-800">Cetak / PDF</button>
        </div>

        {{-- E-Card --}}
        <div id="ecard-print-area" class="print-area overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="bg-blue-900 px-5 py-4 text-white">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-blue-300">{{ config('app.name', 'Bagimu Extra') }}</p>
                        <p class="font-extrabold">E-Card Bukti Pendaftaran</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold whitespace-nowrap bg-emerald-100 text-emerald-800">Terdaftar</span>
                </div>
            </div>

            <div class="p-5">
                <div class="flex items-center gap-4">
                    <span class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-3xl font-bold text-blue-700 ring-4 ring-blue-50">
                        {{ strtoupper(substr($aktif->user?->name ?? '?', 0, 1)) }}
                    </span>
                    <div class="min-w-0">
                        <h2 class="truncate text-xl font-bold text-slate-900">{{ $aktif->user?->name }}</h2>
                        <p class="truncate text-sm text-slate-500">NIS {{ $aktif->nis ?? $aktif->user?->username }} • Akun siswa</p>
                        <p class="mt-1 text-sm font-medium text-slate-700">{{ $aktif->kelas }} • {{ $aktif->jurusan?->short() ?? '—' }}</p>
                        <p class="text-xs text-slate-400">Periode {{ $aktif->periode }}</p>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-xl bg-slate-50 p-3.5">
                        <p class="text-xs font-medium text-slate-400">Kode Pendaftaran</p>
                        <p class="mt-1 font-mono text-sm font-semibold text-slate-800">{{ $aktif->kode() }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3.5">
                        <p class="text-xs font-medium text-slate-400">Tanggal Daftar</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800">{{ $aktif->created_at->format('d M Y') }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3.5">
                        <p class="text-xs font-medium text-slate-400">Eskul Utama</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800">{{ $aktif->eskul_utama }}</p>
                        @if ($aktif->eskul_cadangan)
                            <p class="mt-0.5 text-xs text-slate-500">Cadangan: {{ $aktif->eskul_cadangan }}</p>
                        @endif
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3.5">
                        <p class="text-xs font-medium text-slate-400">Kontak Sekolah</p>
                        @if (trim((string) $kontakPembina) !== '')
                            <a href="https://wa.me/{{ \App\Support\Whatsapp::normalize($kontakPembina) }}" target="_blank" rel="noopener noreferrer"
                               class="mt-1 inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-600 hover:text-emerald-700">
                                +{{ \App\Support\Whatsapp::normalize($kontakPembina) }}
                            </a>
                        @else
                            <p class="mt-1 text-sm text-slate-400">Belum diatur — hubungi TU.</p>
                        @endif
                    </div>
                </div>

                @if ($aktif->no_absen || $aktif->nama_wali_kelas)
                    <div class="mt-4 flex flex-wrap gap-x-6 gap-y-1 rounded-xl bg-slate-50 p-3.5 text-sm text-slate-600">
                        <span>No. Absen: <b class="text-slate-800">{{ $aktif->no_absen ?? '—' }}</b></span>
                        @if ($aktif->nama_wali_kelas)
                            <span>Wali Kelas: <b class="text-slate-800">{{ $aktif->nama_wali_kelas }}</b></span>
                        @endif
                    </div>
                @endif

                @if ($jadwals->isNotEmpty())
                    <div class="mt-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Jadwal Latihan</p>
                        <ul class="mt-1 space-y-0.5 text-sm text-slate-700">
                            @foreach ($jadwals as $j)
                                <li>{{ ucfirst(strtolower($j->hari)) }} • {{ $j->jam_mulai }}–{{ $j->jam_selesai }} • {{ $j->tempat }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($aktif->verify_token)
                    <div class="mt-4 flex flex-col items-center rounded-xl bg-slate-50 p-4 text-center">
                        {!! SimpleQr::size(180)->generate(route('verify.show', $aktif->verify_token)) !!}
                        <p class="mt-2 font-mono text-sm font-bold tracking-widest text-slate-800">{{ $aktif->kode() }}</p>
                        <p class="text-[11px] text-slate-500">QR verifikasi E-Card (bukan QR login). Scan dengan kamera. Jika QR buram saat cetak, ketik kode di atas pada halaman verifikasi.</p>
                    </div>
                @endif

                @if ($aktif->nilai !== null || $aktif->certificate)
                    <div class="mt-4 rounded-xl bg-slate-50 p-3.5 text-sm">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Nilai Akhir</p>
                        <p class="mt-1 font-bold text-slate-900">{{ $aktif->nilai ?? '—' }}{{ $aktif->predikat ? ' ('.$aktif->predikat.')' : '' }}</p>
                        @if ($aktif->catatan_nilai)
                            <p class="mt-1 text-slate-600">{{ $aktif->catatan_nilai }}</p>
                        @endif
                        <p class="mt-1 text-slate-600">No. Sertifikat: <span class="font-mono font-semibold">{{ $aktif->certificate?->nomor ?? '—' }}</span></p>
                    </div>
                @endif

                <p class="mt-4 text-center text-[11px] text-slate-400">
                    {{ $aktif->kelas }} • {{ $aktif->gender?->label() ?? '—' }} • {{ $aktif->usia }} thn • Dicetak via {{ config('app.name', 'Bagimu Extra') }}
                </p>
            </div>
        </div>

        <div class="no-print">
            <a href="{{ route('siswa.dashboard') }}" class="block rounded-xl border border-slate-300 bg-white px-5 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali ke Beranda</a>
        </div>
    @endif

    @if ($registrations->count() > 1)
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <h2 class="font-bold text-slate-900">Riwayat Periode Lain</h2>
            <ul class="mt-2 space-y-1 text-sm text-slate-600">
                @foreach ($registrations as $r)
                    @if ($aktif && $r->id === $aktif->id) @continue @endif
                    <li class="flex justify-between gap-2">
                        <span>{{ $r->periode }} • {{ $r->eskul_utama }}</span>
                        <x-badge tone="{{ $r->status->value === 'APPROVED' ? 'emerald' : ($r->status->value === 'PENDING' ? 'amber' : 'rose') }}">{{ $r->status->label() }}</x-badge>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection

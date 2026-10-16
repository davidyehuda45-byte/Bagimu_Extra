@extends('layouts.siswa')

@section('title', 'Bukti Pendaftaran')

@section('content')
<div class="space-y-4">
    <h1 class="text-lg font-bold text-slate-900">Bukti Pendaftaran</h1>

    @if (session('just_registered'))
        <div id="auto-exit-banner" class="no-print rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm text-emerald-900">
            <div class="flex items-start gap-3.5">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-xs">
                    <x-icon name="check" class="h-5 w-5" />
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-base text-emerald-950">Pendaftaran Berhasil & Data Terkunci!</h3>
                    <p class="mt-1 text-xs sm:text-sm text-emerald-800 leading-relaxed">
                        Pilihan ekstrakurikuler Anda telah disimpan. Token login Anda bersifat <b>sekali pakai</b> dan kini telah dihanguskan agar data Anda aman dan tidak dapat diubah lagi.
                    </p>
                    <div class="mt-3 flex flex-wrap items-center gap-3">
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-100 px-3 py-1.5 text-xs font-semibold text-emerald-900">
                            <svg class="h-4 w-4 animate-spin text-emerald-700" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"/></svg>
                            <span>Otomatis keluar dalam <b id="countdown-timer">5</b> detik...</span>
                        </span>
                        <form id="auto-exit-form" action="{{ route('auth.session.exit') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="rounded-lg bg-emerald-700 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-emerald-800 transition cursor-pointer">
                                Keluar Sekarang &rarr;
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <script>
                (function() {
                    let sec = 5;
                    const el = document.getElementById('countdown-timer');
                    const timer = setInterval(function() {
                        sec--;
                        if (el) el.textContent = sec;
                        if (sec <= 0) {
                            clearInterval(timer);
                            document.getElementById('auto-exit-form').submit();
                        }
                    }, 1000);
                })();
            </script>
        </div>
    @endif

    @if (!$aktif)
        <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
            <p class="font-bold text-slate-900">Belum ada pendaftaran.</p>
            <a href="{{ route('siswa.daftar') }}" class="mt-3 inline-block rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-blue-800">Daftar Sekarang</a>
        </div>
    @else
        <div class="no-print flex items-center justify-between gap-3">
            <p class="text-xs text-slate-500">Bukti resmi pendaftaran ekstrakurikuler yang telah tercatat di sistem.</p>
            <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-xs sm:text-sm font-bold text-white hover:bg-blue-700 transition shadow-xs shadow-blue-600/20 cursor-pointer shrink-0">
                <x-icon name="printer" class="h-4 w-4" />
                <span>Cetak Bukti</span>
            </button>
        </div>

        {{-- Bukti Pendaftaran Siswa --}}
        <div id="print-area" class="print-area overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-200/90 bg-white shadow-sm">
            <div class="bg-blue-600 px-6 py-5 text-white">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-blue-100">{{ config('app.name', 'Bagimu Extra') }}</p>
                        <p class="font-extrabold text-base sm:text-lg">Bukti Pendaftaran Ekstrakurikuler</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold whitespace-nowrap bg-white/20 text-white border border-white/25">
                        <x-icon name="check" class="h-3.5 w-3.5" />
                        <span>Terdaftar Resmi</span>
                    </span>
                </div>
                <p class="mt-1 text-xs text-blue-100">Data pendaftaran telah tersimpan permanen di database sekolah.</p>
            </div>

            <div class="p-5">
                <div class="flex items-center gap-4">
                    <span class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-3xl font-bold text-blue-700 ring-4 ring-blue-50">
                        {{ strtoupper(substr($aktif->user?->name ?? '?', 0, 1)) }}
                    </span>
                    <div class="min-w-0">
                        <h2 class="truncate text-xl font-bold text-slate-900">{{ $aktif->user?->name }}</h2>
                        <p class="truncate text-sm text-slate-500">NIS {{ $aktif->nis ?? $aktif->user?->username }} • Siswa</p>
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
                        <p class="text-xs font-medium text-slate-400">Eskul Pilihan</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800">{{ $aktif->eskul_utama }}</p>
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
                    {{ $aktif->kelas }} • {{ $aktif->gender?->label() ?? '—' }} • {{ $aktif->usia }} thn • Dicatat via {{ config('app.name', 'Bagimu Extra') }}
                </p>
            </div>
        </div>

        <div class="no-print">
            <a href="{{ route('siswa.dashboard') }}" class="inline-flex items-center justify-center gap-2 w-full rounded-xl border border-slate-200 bg-white px-5 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition shadow-xs">
                &larr; Kembali ke Beranda
            </a>
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

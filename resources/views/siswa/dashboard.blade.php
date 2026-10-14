@extends('layouts.siswa')

@section('title', 'Beranda')

@section('content')
<div class="space-y-4">
    {{-- Hero --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between gap-2">
            <div>
                <p class="text-sm text-slate-500">Halo,</p>
                <h1 class="text-lg font-extrabold text-slate-900">{{ $user->name }}</h1>
                <p class="mt-1 text-xs text-slate-500">
                    @if ($periodeAktif)
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold whitespace-nowrap bg-blue-100 text-blue-800 ring-1 ring-inset ring-blue-600/20">Tahun Ajaran {{ $periodeAktif->periode }}</span>
                    @endif
                    {{ \App\Models\AppSetting::get('school.name', '') }}
                </p>
            </div>
            <a href="{{ route('siswa.profil') }}" class="shrink-0 rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Profil & Akun</a>
        </div>
    </div>

    {{-- Banner status --}}
    @if (!$pendaftaran)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
            <p class="font-bold text-amber-800">Belum mendaftar eskul</p>
            <p class="mt-1 text-sm text-amber-700">Pilih eskul favoritmu dan kirim pendaftaran sekarang.</p>
            <a href="{{ route('siswa.daftar') }}" class="mt-3 inline-block rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-blue-800">Daftar Sekarang</a>
        </div>
    @else
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
            <div class="flex items-center justify-between gap-2">
                <p class="font-bold text-slate-900">{{ $pendaftaran->eskul_utama }}</p>
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold whitespace-nowrap bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-600/20">Terdaftar</span>
            </div>
            <p class="mt-1 text-sm text-slate-600">
                E-Card & QR verifikasi aktif. Tunjukkan ke pembina saat latihan.
            </p>
            <div class="mt-3 flex flex-wrap gap-2">
                <a href="{{ route('siswa.status') }}" class="rounded-xl bg-blue-700 px-4 py-2 text-sm font-bold text-white hover:bg-blue-800">Lihat E-Card</a>
            </div>
        </div>
    @endif

    {{-- Jadwal latihan --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <h2 class="font-bold text-slate-900">Jadwal Latihan</h2>
        @if ($jadwalEskul->isEmpty())
            <p class="mt-1 text-sm text-slate-500">Belum ada jadwal. Jadwal muncul setelah disetujui dan pembina mengisi jadwal.</p>
        @else
            <ul class="mt-2 space-y-2 text-sm">
                @foreach ($jadwalEskul as $e)
                    @foreach ($e->jadwals as $j)
                        <li class="flex flex-wrap gap-x-2 text-slate-600">
                            <b class="text-slate-800">{{ $e->name }}</b>
                            <span>{{ ucfirst(strtolower($j->hari)) }} {{ $j->jam_mulai }}–{{ $j->jam_selesai }}</span>
                            <span class="text-slate-400">{{ $j->tempat }}</span>
                        </li>
                    @endforeach
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Presensi & prestasi --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-slate-900">Presensi & Prestasi</h2>
            <a href="{{ route('siswa.kehadiran') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">Detail →</a>
        </div>
        <div class="mt-2 flex items-center gap-3">
            <div class="h-2.5 flex-1 overflow-hidden rounded-full bg-slate-100">
                <div class="{{ $persenHadir >= 85 ? 'bg-emerald-500' : ($persenHadir >= 70 ? 'bg-amber-400' : 'bg-rose-500') }} h-full rounded-full" style="width: {{ min(100, $persenHadir) }}%"></div>
            </div>
            <span class="text-sm font-bold text-slate-800">{{ $persenHadir }}%</span>
        </div>
        <p class="mt-1 text-xs text-slate-500">
            Kehadiran {{ $persenHadir >= 80 ? 'sangat baik, pertahankan!' : 'di bawah 80%, tingkatkan kehadiranmu.' }}
            Nilai Akhir: <b class="text-slate-800">{{ $pendaftaran?->nilai ?? '—' }}{{ $pendaftaran?->predikat ? ' ('.$pendaftaran->predikat.')' : '' }}</b>
            @if ($izinPending > 0)
                <span class="ml-1 inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-[11px] font-semibold bg-amber-100 text-amber-800 ring-1 ring-inset ring-amber-600/20">{{ $izinPending }} izin menunggu</span>
            @endif
        </p>
    </div>

    {{-- Menu cepat --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        <a href="{{ route('siswa.daftar') }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:bg-slate-50 transition">
            <p class="text-blue-700"><x-icon name="clipboard" class="h-6 w-6" /></p>
            <p class="mt-1 text-sm font-bold text-slate-900">Formulir</p>
            <p class="text-[11px] text-slate-500">Status & pilihan eskul</p>
        </a>
        <a href="{{ route('siswa.status') }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:bg-slate-50 transition">
            <p class="text-blue-700"><x-icon name="id-card" class="h-6 w-6" /></p>
            <p class="mt-1 text-sm font-bold text-slate-900">E-Card Digital</p>
            <p class="text-[11px] text-slate-500">QR & bukti cetak</p>
        </a>
        <a href="{{ route('siswa.kehadiran') }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:bg-slate-50 transition">
            <p class="text-blue-700"><x-icon name="calendar-check" class="h-6 w-6" /></p>
            <p class="mt-1 text-sm font-bold text-slate-900">Presensi Saya</p>
            <p class="text-[11px] text-slate-500">Riwayat & statistik</p>
        </a>
        <a href="{{ route('siswa.izin.create') }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:bg-slate-50 transition">
            <p class="text-blue-700"><x-icon name="inbox" class="h-6 w-6" /></p>
            <p class="mt-1 text-sm font-bold text-slate-900">Ajukan Izin</p>
            <p class="text-[11px] text-slate-500">Surat izin & sakit</p>
        </a>
        <a href="{{ route('siswa.pengumuman') }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:bg-slate-50 transition">
            <p class="text-blue-700"><x-icon name="megaphone" class="h-6 w-6" /></p>
            <p class="mt-1 text-sm font-bold text-slate-900">Pengumuman</p>
            <p class="text-[11px] text-slate-500">Info & agenda sekolah</p>
        </a>
        <a href="{{ route('siswa.profil') }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:bg-slate-50 transition">
            <p class="text-blue-700"><x-icon name="user" class="h-6 w-6" /></p>
            <p class="mt-1 text-sm font-bold text-slate-900">Profil & Akun</p>
            <p class="text-[11px] text-slate-500">Data siswa & sandi</p>
        </a>
    </div>

    {{-- Pengumuman terbaru --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-slate-900">Pengumuman Terbaru</h2>
            <a href="{{ route('siswa.pengumuman') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">Lihat Semua</a>
        </div>
        @if ($pengumuman->isEmpty())
            <p class="mt-1 text-sm text-slate-500">Belum ada pengumuman.</p>
        @else
            <div class="mt-2 space-y-2">
                @foreach ($pengumuman as $p)
                    <div class="border-b border-slate-100 pb-2 last:border-0 last:pb-0">
                        <p class="text-sm font-semibold text-slate-900">{{ $p->judul }}</p>
                        <p class="line-clamp-2 text-xs text-slate-500">{{ $p->isi }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

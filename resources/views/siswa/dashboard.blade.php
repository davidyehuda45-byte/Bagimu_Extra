@extends('layouts.siswa')

@section('title', 'Beranda')

@section('content')
<div class="space-y-5">
    {{-- Hero Greeting Card --}}
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start sm:items-center gap-3.5">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-600 text-white font-extrabold text-lg shadow-sm shadow-blue-600/20">
                    {{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}
                </span>
                <div class="min-w-0">
                    <p class="text-xs font-medium text-slate-500">Selamat datang kembali,</p>
                    <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight truncate">{{ $user->name }}</h1>
                    <div class="mt-1 flex flex-wrap items-center gap-2">
                        @if ($periodeAktif)
                            <x-badge tone="blue" dot="true">T.A. {{ $periodeAktif->periode }}</x-badge>
                        @endif
                        <span class="text-xs text-slate-500 font-medium">
                            {{ \App\Models\AppSetting::get('school.name', '') }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 self-start sm:self-center">
                <a href="{{ route('siswa.profil') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition shadow-xs">
                    <x-icon name="user" class="h-3.5 w-3.5 text-slate-500" />
                    <span>Profil & Akun</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Banner Status Pendaftaran --}}
    @if (!$pendaftaran)
        <div class="rounded-2xl sm:rounded-3xl border border-amber-200/90 bg-gradient-to-r from-amber-50/90 via-amber-50/50 to-white p-5 sm:p-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                        <x-icon name="alert" class="h-5 w-5" />
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="font-bold text-amber-950">Belum Mendaftar Ekstrakurikuler</h2>
                            <x-badge tone="amber">Perlu Tindakan</x-badge>
                        </div>
                        <p class="mt-1 text-xs sm:text-sm text-amber-800 leading-relaxed">
                            Pilih eskul favoritmu sebelum batas waktu pendaftaran berakhir dan kirimkan formulir sekarang.
                        </p>
                    </div>
                </div>
                <a href="{{ route('siswa.daftar') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-xs sm:text-sm font-bold text-white hover:bg-blue-700 transition shadow-sm shadow-blue-600/25 shrink-0">
                    <x-icon name="clipboard" class="h-4 w-4" />
                    <span>Daftar Sekarang</span>
                </a>
            </div>
        </div>
    @else
        <div class="rounded-2xl sm:rounded-3xl border border-emerald-200/90 bg-gradient-to-r from-emerald-50/90 via-emerald-50/40 to-white p-5 sm:p-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3.5">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-sm shadow-emerald-600/20">
                        <x-icon name="check" class="h-6 w-6" />
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base sm:text-lg font-extrabold text-slate-900">{{ $pendaftaran->eskul_utama }}</h2>
                            <x-badge tone="emerald" dot="true">Terdaftar Aktif</x-badge>
                        </div>
                        <p class="mt-1 text-xs sm:text-sm text-slate-600">
                            E-Card & QR verifikasi aktif. Tunjukkan QR Code pada ponsel ke pembina saat latihan.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('siswa.status') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs sm:text-sm font-bold text-white hover:bg-blue-700 transition shadow-sm shadow-blue-600/20">
                        <x-icon name="id-card" class="h-4 w-4" />
                        <span>Buka E-Card Digital</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Grid 2 Kolom: Jadwal & Presensi --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        {{-- Jadwal Latihan --}}
        <x-card>
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                        <x-icon name="calendar" class="h-4 w-4" />
                    </span>
                    <h2 class="font-bold text-slate-900 text-sm sm:text-base">Jadwal Latihan</h2>
                </div>
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Mingguan</span>
            </div>

            @if ($jadwalEskul->isEmpty())
                <x-empty-state icon="calendar" title="Belum ada jadwal" message="Jadwal muncul setelah pendaftaran disetujui dan pembina mengisi jadwal latihan." />
            @else
                <ul class="mt-4 space-y-2.5 text-xs sm:text-sm">
                    @foreach ($jadwalEskul as $e)
                        @foreach ($e->jadwals as $j)
                            <li class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 rounded-xl border border-slate-100 bg-slate-50/70 p-3 hover:bg-slate-50 transition">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center rounded-md bg-blue-100 px-2 py-0.5 text-[11px] font-bold text-blue-800">
                                        {{ ucfirst(strtolower($j->hari)) }}
                                    </span>
                                    <b class="text-slate-900 font-semibold">{{ $e->name }}</b>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-slate-500">
                                    <span class="tabular-nums font-medium text-slate-700">{{ $j->jam_mulai }}–{{ $j->jam_selesai }}</span>
                                    <span>•</span>
                                    <span class="text-slate-500 font-medium">{{ $j->tempat }}</span>
                                </div>
                            </li>
                        @endforeach
                    @endforeach
                </ul>
            @endif
        </x-card>

        {{-- Presensi & Prestasi --}}
        <x-card>
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <x-icon name="calendar-check" class="h-4 w-4" />
                    </span>
                    <h2 class="font-bold text-slate-900 text-sm sm:text-base">Presensi & Prestasi</h2>
                </div>
                <a href="{{ route('siswa.kehadiran') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-700">
                    <span>Detail</span>
                    <span>&rarr;</span>
                </a>
            </div>

            <div class="mt-4">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs font-medium text-slate-500">Tingkat Kehadiran</span>
                    <span class="text-sm font-extrabold tabular-nums text-slate-900">{{ $persenHadir }}%</span>
                </div>
                <x-progress :value="$persenHadir" aria-label="Persentase kehadiran {{ $persenHadir }} persen" />
            </div>

            <div class="mt-4 rounded-xl bg-slate-50 p-3 border border-slate-100/80 space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-500">Nilai Akhir:</span>
                    <span class="font-bold text-slate-900">
                        {{ $pendaftaran?->nilai ?? '—' }}{{ $pendaftaran?->predikat ? ' ('.$pendaftaran->predikat.')' : '' }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-500">Evaluasi:</span>
                    <span class="font-semibold {{ $persenHadir >= 80 ? 'text-emerald-700' : 'text-amber-700' }}">
                        {{ $persenHadir >= 80 ? 'Sangat Baik' : 'Tingkatkan Kehadiran' }}
                    </span>
                </div>
                @if ($izinPending > 0)
                    <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between">
                        <span class="text-xs text-amber-800 font-medium">Pengajuan Izin:</span>
                        <x-badge tone="amber" dot="true">{{ $izinPending }} izin menunggu konfirmasi</x-badge>
                    </div>
                @endif
            </div>
        </x-card>
    </div>

    {{-- Menu Cepat (Quick Actions Grid) --}}
    <div>
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 px-1">Menu Akses Cepat</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
            <a href="{{ route('siswa.daftar') }}" class="group rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-blue-300 hover:shadow-sm hover-lift transition">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-700 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <x-icon name="clipboard" class="h-5 w-5" />
                </div>
                <p class="mt-3 text-sm font-bold text-slate-900 group-hover:text-blue-600 transition-colors">Formulir Pendaftaran</p>
                <p class="mt-0.5 text-xs text-slate-500">Status & pemilihan eskul</p>
            </a>

            <a href="{{ route('siswa.status') }}" class="group rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-blue-300 hover:shadow-sm hover-lift transition">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-700 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <x-icon name="id-card" class="h-5 w-5" />
                </div>
                <p class="mt-3 text-sm font-bold text-slate-900 group-hover:text-blue-600 transition-colors">E-Card Digital</p>
                <p class="mt-0.5 text-xs text-slate-500">QR Code presensi & cetak kartu</p>
            </a>

            <a href="{{ route('siswa.kehadiran') }}" class="group rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-blue-300 hover:shadow-sm hover-lift transition">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-700 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <x-icon name="calendar-check" class="h-5 w-5" />
                </div>
                <p class="mt-3 text-sm font-bold text-slate-900 group-hover:text-blue-600 transition-colors">Presensi Saya</p>
                <p class="mt-0.5 text-xs text-slate-500">Riwayat & rekap kehadiran</p>
            </a>

            <a href="{{ route('siswa.izin.create') }}" class="group rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-blue-300 hover:shadow-sm hover-lift transition">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-700 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <x-icon name="inbox" class="h-5 w-5" />
                </div>
                <p class="mt-3 text-sm font-bold text-slate-900 group-hover:text-blue-600 transition-colors">Ajukan Izin</p>
                <p class="mt-0.5 text-xs text-slate-500">Upload surat izin atau sakit</p>
            </a>

            <a href="{{ route('siswa.pengumuman') }}" class="group rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-blue-300 hover:shadow-sm hover-lift transition">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-700 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <x-icon name="megaphone" class="h-5 w-5" />
                </div>
                <p class="mt-3 text-sm font-bold text-slate-900 group-hover:text-blue-600 transition-colors">Pengumuman</p>
                <p class="mt-0.5 text-xs text-slate-500">Agenda & info eskul sekolah</p>
            </a>

            <a href="{{ route('siswa.profil') }}" class="group rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-blue-300 hover:shadow-sm hover-lift transition">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-700 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <x-icon name="user" class="h-5 w-5" />
                </div>
                <p class="mt-3 text-sm font-bold text-slate-900 group-hover:text-blue-600 transition-colors">Profil & Akun</p>
                <p class="mt-0.5 text-xs text-slate-500">Data NIS, rombel & kata sandi</p>
            </a>
        </div>
    </div>

    {{-- Pengumuman Terbaru --}}
    <x-card>
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                    <x-icon name="megaphone" class="h-4 w-4" />
                </span>
                <h2 class="font-bold text-slate-900 text-sm sm:text-base">Pengumuman Terbaru</h2>
            </div>
            <a href="{{ route('siswa.pengumuman') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-700">
                <span>Lihat Semua</span>
                <span>&rarr;</span>
            </a>
        </div>

        @if ($pengumuman->isEmpty())
            <x-empty-state icon="megaphone" title="Belum ada pengumuman" message="Informasi pengumuman terkini seputar ekstrakurikuler akan ditampilkan di sini." />
        @else
            <div class="mt-3 divide-y divide-slate-100">
                @foreach ($pengumuman as $p)
                    <div class="py-3 first:pt-1 last:pb-0">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-bold text-slate-900">{{ $p->judul }}</p>
                            @if(isset($p->created_at))
                                <span class="text-[11px] text-slate-400 shrink-0">{{ $p->created_at->format('d M') }}</span>
                            @endif
                        </div>
                        <p class="mt-1 line-clamp-2 text-xs text-slate-600 leading-relaxed">{{ $p->isi }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>
</div>
@endsection

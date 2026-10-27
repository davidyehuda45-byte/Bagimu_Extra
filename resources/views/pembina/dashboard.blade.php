@extends('layouts.staff')

@section('title', 'Dashboard Pembina')

@section('content')
<div class="space-y-6">
    {{-- Header Banner Sesuai Foto Desain Resmi (Latar Kampus SMK Bagimu Negeriku) --}}
    <div class="relative overflow-hidden rounded-3xl bg-[#0e5cda] text-white shadow-lg shadow-blue-900/10 min-h-[240px] sm:min-h-[280px] p-6 sm:p-8 xl:p-9 flex flex-col justify-between">
        {{-- Foto Asli Kampus SMK Bagimu Negeriku (Bersih Tanpa Teks Bawaan) --}}
        <img src="{{ asset('images/dashboard-banner-clean.jpg') }}?v={{ filemtime(public_path('images/dashboard-banner-clean.jpg')) }}"
             alt="Kampus SMK Bagimu Negeriku"
             class="pointer-events-none absolute inset-0 h-full w-full object-cover object-right select-none z-0" />

        {{-- Gradient Overlay untuk Menjamin Keterbacaan Teks di Semua Resolusi Layar --}}
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-[#0a4fc0]/90 via-[#0e5cda]/75 to-transparent sm:from-transparent sm:via-transparent z-0"></div>

        {{-- Konten Utama Banner --}}
        <div class="relative z-10 space-y-4 max-w-2xl">
            {{-- Branding Sekolah: Logo + Nama + Slogan 4B --}}
            <div class="flex items-center gap-3.5">
                <x-school-logo :size="48" class="rounded-full bg-white p-1 shadow-md ring-2 ring-white/50 shrink-0" />
                <div class="leading-tight">
                    <p class="text-base sm:text-lg font-black tracking-tight text-white leading-tight drop-shadow-xs">Bagimu Extra</p>
                    <p class="text-xs font-semibold text-white/90">SMK Bagimu Negeriku</p>
                    <p class="text-[11px] font-medium text-blue-100 mt-0.5 flex items-center gap-1">
                        <x-icon name="sparkles" class="h-3 w-3 text-sky-200 shrink-0" />
                        <span>4B Berbudi • Berkualitas • Berdaya • Berhasil</span>
                    </p>
                </div>
            </div>

            {{-- Badges Glassmorphic --}}
            <div class="flex flex-wrap items-center gap-2 pt-1">
                @if ($eskul)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-md text-white border border-white/30 shadow-xs">
                        <x-icon name="trophy" class="h-3.5 w-3.5 text-amber-300" />
                        <span>Eskul: {{ $eskul->name }}</span>
                    </span>
                @endif
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-md text-white border border-white/30 shadow-xs">
                    <x-icon name="user-check" class="h-3.5 w-3.5 text-sky-200" />
                    <span>Pembina: {{ auth()->user()->name }}</span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-md text-white border border-white/30 shadow-xs">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    <span>Sistem Ekstrakurikuler Aktif</span>
                </span>
            </div>

            {{-- Judul Besar & Subtitle --}}
            <div class="space-y-1.5 pt-1">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-white leading-tight drop-shadow-xs">
                    @if ($eskul)
                        Dashboard Pembina /<br class="hidden sm:inline"> {{ $eskul->name }}
                    @else
                        Dashboard Pembina /<br class="hidden sm:inline"> Pelatih Eskul
                    @endif
                </h1>
                <p class="text-xs sm:text-sm text-blue-100/95 leading-relaxed max-w-xl">
                    @if ($eskul)
                        Pusat kendali eskul {{ $eskul->name }} — kelola kegiatan, scan presensi kamera HP, perizinan siswa, dan rekap penilaian.
                    @else
                        Kelola kegiatan ekstrakurikuler, presensi digital kamera HP, perizinan siswa, dan rekap penilaian aktivitas.
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Tombol Aksi Cepat (Ditaruh di Bawah Banner Sesuai Standar Resmi) --}}
    @if ($eskul)
        <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
            <a href="{{ route('pembina.scan.show', $eskul) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold shadow-xs transition">
                <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 7h.01"/><path d="M17 7h.01"/><path d="M7 17h.01"/><path d="M17 17h.01"/></svg>
                <span>Scan Presensi Kamera HP</span>
            </a>
            <a href="{{ route('pembina.eskul.show', $eskul) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-xs transition">
                <x-icon name="clipboard" class="h-4 w-4 text-white" />
                <span>Menu Eskul Lengkap</span>
            </a>
            <a href="{{ route('pembina.kegiatan.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-xs transition">
                <x-icon name="plus" class="h-4 w-4 text-blue-600" />
                <span>Tambah Pertemuan Baru</span>
            </a>
        </div>

        {{-- Statistik Ringkas Eskul Ini (Tanpa Kontainer Icon) --}}
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs hover:border-slate-300 transition-all duration-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500">Anggota Aktif</span>
                    <x-icon name="users" class="h-5 w-5 text-emerald-600" />
                </div>
                <div class="mt-3 text-3xl font-black text-slate-900 tracking-tight">{{ $pendaftarAktif }}</div>
                <p class="mt-1 text-xs text-slate-400 font-medium">Siswa terdaftar resmi periode berjalan.</p>
                <div class="mt-4 pt-3 border-t border-slate-100">
                    <a href="{{ route('pembina.eskul.show', $eskul) }}#anggota" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 hover:text-emerald-700">
                        <span>Lihat Daftar Anggota</span>
                        <x-icon name="arrow-right" class="h-3 w-3" />
                    </a>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs hover:border-slate-300 transition-all duration-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500">Permohonan Izin</span>
                    <x-icon name="clock" class="h-5 w-5 text-amber-500" />
                </div>
                <div class="mt-3 text-3xl font-black text-slate-900 tracking-tight">{{ $izinPending }}</div>
                <p class="mt-1 text-xs text-slate-400 font-medium">
                    {{ $izinPending > 0 ? 'Menunggu konfirmasi pembina.' : 'Tidak ada permohonan tertunda.' }}
                </p>
                <div class="mt-4 pt-3 border-t border-slate-100">
                    <a href="{{ route('pembina.eskul.show', $eskul) }}#izin" class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 hover:text-amber-700">
                        <span>Periksa Perizinan</span>
                        <x-icon name="arrow-right" class="h-3 w-3" />
                    </a>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs hover:border-slate-300 transition-all duration-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500">Jadwal Sesi Latihan</span>
                    <x-icon name="calendar-check" class="h-5 w-5 text-blue-600" />
                </div>
                <div class="mt-3 text-3xl font-black text-slate-900 tracking-tight">{{ $eskul->jadwals_count }}</div>
                <p class="mt-1 text-xs text-slate-400 font-medium">Sesi latihan rutin setiap minggunya.</p>
                <div class="mt-4 pt-3 border-t border-slate-100">
                    <a href="{{ route('pembina.kegiatan.create') }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-700">
                        <span>Tambah Pertemuan Baru</span>
                        <x-icon name="arrow-right" class="h-3 w-3" />
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
            <a href="{{ route('pembina.kegiatan.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-xs transition">
                <x-icon name="calendar" class="h-4 w-4 text-white" />
                <span>Jadwal Kegiatan</span>
            </a>
            <a href="{{ route('pembina.kegiatan.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-xs transition">
                <x-icon name="plus" class="h-4 w-4 text-blue-600" />
                <span>Tambah Kegiatan Baru</span>
            </a>
        </div>

        {{-- Statistik Ringkas Akun Pembina (Tanpa Eskul Ditugaskan) --}}
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs hover:border-slate-300 transition-all duration-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500">Agenda Kegiatan</span>
                    <x-icon name="calendar" class="h-5 w-5 text-blue-600" />
                </div>
                <div class="mt-3 text-3xl font-black text-slate-900 tracking-tight">{{ $kegiatanMendatang->count() }}</div>
                <p class="mt-1 text-xs text-slate-400 font-medium">Jadwal kegiatan mendatang.</p>
                <div class="mt-4 pt-3 border-t border-slate-100">
                    <a href="{{ route('pembina.kegiatan.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-700">
                        <span>Lihat Semua Jadwal</span>
                        <x-icon name="arrow-right" class="h-3 w-3" />
                    </a>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs hover:border-slate-300 transition-all duration-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500">Status Pembina</span>
                    <x-icon name="user-check" class="h-5 w-5 text-emerald-600" />
                </div>
                <div class="mt-3 text-2xl font-black text-slate-900 tracking-tight">Akun Aktif</div>
                <p class="mt-1 text-xs text-slate-400 font-medium">{{ auth()->user()->name }}</p>
                <div class="mt-4 pt-3 border-t border-slate-100">
                    <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        <span>Terverifikasi Sekolah</span>
                    </span>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs hover:border-slate-300 transition-all duration-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500">Penugasan Eskul</span>
                    <x-icon name="trophy" class="h-5 w-5 text-amber-500" />
                </div>
                <div class="mt-3 text-2xl font-black text-slate-900 tracking-tight">Siap Ditugaskan</div>
                <p class="mt-1 text-xs text-slate-400 font-medium">Hubungi Admin untuk plot eskul binaan.</p>
                <div class="mt-4 pt-3 border-t border-slate-100">
                    <span class="text-xs font-medium text-slate-400">Menunggu penetapan</span>
                </div>
            </div>
        </div>
    @endif

    {{-- Kegiatan Mendatang --}}
    <x-card class="shadow-xs hover:border-slate-300 transition-all duration-200">
        <div class="mb-4 flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <x-icon name="calendar" class="h-4 w-4" />
                </span>
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900">5 Kegiatan / Latihan Mendatang</h2>
                    <p class="text-[11px] text-slate-400">Jadwal kegiatan yang akan segera dilaksanakan</p>
                </div>
            </div>
            <a href="{{ route('pembina.kegiatan.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline">
                <span>Semua Kegiatan</span>
                <x-icon name="arrow-right" class="h-3.5 w-3.5" />
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-left text-xs font-bold text-slate-500">
                        <th class="px-4 py-3 rounded-l-xl">Tanggal</th>
                        <th class="px-4 py-3">Eskul</th>
                        <th class="px-4 py-3">Jenis</th>
                        <th class="px-4 py-3">Agenda Kegiatan</th>
                        <th class="px-4 py-3 text-center rounded-r-xl">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs sm:text-sm">
                    @forelse ($kegiatanMendatang as $kegiatan)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap font-bold text-slate-900">{{ $kegiatan->tanggal->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-700">{{ $kegiatan->eskul->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block rounded-full bg-blue-50 border border-blue-200/60 px-2.5 py-0.5 text-[11px] font-bold text-blue-700">
                                    {{ $kegiatan->jenis->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $kegiatan->agenda }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('pembina.kegiatan.show', $kegiatan) }}" class="inline-flex items-center justify-center h-8 w-8 rounded-xl border border-slate-200 bg-white text-slate-600 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-300 transition shadow-2xs" title="Lihat Detail">
                                    <x-icon name="eye" class="h-4 w-4" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-xs text-slate-400">Tidak ada jadwal kegiatan mendatang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection

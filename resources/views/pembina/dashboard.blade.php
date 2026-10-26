@extends('layouts.staff')

@section('title', 'Dashboard Pembina')

@section('content')
<div class="space-y-6">
    {{-- Header Sambutan --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Dashboard Pembina &amp; Pelatih</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola kegiatan ekstrakurikuler, presensi digital, perizinan siswa, dan rekap penilaian.</p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 border border-blue-200/70 text-xs font-bold text-blue-700 shadow-2xs self-start sm:self-auto">
            <x-icon name="user-check" class="h-3.5 w-3.5 text-blue-600" />
            <span>{{ auth()->user()->name }}</span>
        </span>
    </div>

    @if ($eskul)
        {{-- Banner Eskul yang Diampu Langsung --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-700 via-blue-800 to-indigo-900 p-6 sm:p-8 text-white shadow-xl shadow-blue-900/10">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-blue-500/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-16 right-32 h-64 w-64 rounded-full bg-indigo-500/20 blur-3xl"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-2 max-w-xl">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-bold text-white border border-white/20 backdrop-blur-md">
                        <x-icon name="trophy" class="h-3.5 w-3.5 text-amber-300" />
                        <span>Ekstrakurikuler yang Anda Bina</span>
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white">{{ $eskul->name }}</h2>
                    <p class="text-xs sm:text-sm text-blue-100/90 leading-relaxed">{{ $eskul->description ?? 'Ekstrakurikuler resmi SMK Bagimu Negeriku.' }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('pembina.scan.show', $eskul) }}" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2.5 text-xs font-extrabold transition shadow-md shadow-emerald-950/20 hover:scale-[1.02] active:scale-[0.98]">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 7h.01"/><path d="M17 7h.01"/><path d="M7 17h.01"/><path d="M17 17h.01"/></svg>
                        <span>Scan Presensi Kamera HP</span>
                    </a>
                    <a href="{{ route('pembina.eskul.show', $eskul) }}" class="inline-flex items-center gap-2 rounded-2xl bg-white text-blue-800 hover:bg-blue-50 px-4 py-2.5 text-xs font-bold transition shadow-md shadow-blue-950/20 hover:scale-[1.02] active:scale-[0.98]">
                        <span>Buka Menu Lengkap</span>
                        <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                    </a>
                </div>
            </div>
        </div>

        {{-- Statistik Ringkas Eskul Ini --}}
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="group relative overflow-hidden rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs hover:border-emerald-300 hover:shadow-md transition-all duration-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500">Anggota Aktif</span>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-500/25 group-hover:scale-110 transition-transform duration-200">
                        <x-icon name="users" class="h-5 w-5" />
                    </span>
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

            <div class="group relative overflow-hidden rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs hover:border-amber-300 hover:shadow-md transition-all duration-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500">Permohonan Izin</span>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 text-white shadow-md shadow-amber-500/25 group-hover:scale-110 transition-transform duration-200">
                        <x-icon name="clock" class="h-5 w-5" />
                    </span>
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

            <div class="group relative overflow-hidden rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs hover:border-blue-300 hover:shadow-md transition-all duration-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500">Jadwal Sesi Latihan</span>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/25 group-hover:scale-110 transition-transform duration-200">
                        <x-icon name="calendar-check" class="h-5 w-5" />
                    </span>
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
        <div class="rounded-3xl border border-amber-200/90 bg-amber-50/80 p-6 text-sm text-amber-900 shadow-xs">
            <div class="flex items-start gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-200 text-amber-900">
                    <x-icon name="alert" class="h-5 w-5" />
                </span>
                <div>
                    <h3 class="font-bold text-base mb-1">Belum Ada Eskul yang Ditugaskan</h3>
                    <p class="text-xs sm:text-sm text-amber-800">Akun Anda belum dikaitkan dengan ekstrakurikuler mana pun. Silakan hubungi Administrator untuk penugasan eskul binaan Anda.</p>
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
                    <h2 class="text-sm font-extrabold text-slate-900">5 Kegiatan &amp; Latihan Mendatang</h2>
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

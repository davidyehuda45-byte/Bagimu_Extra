@extends('layouts.staff')

@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">
    {{-- Hero Welcome Banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-700 via-blue-800 to-indigo-900 p-6 sm:p-8 text-white shadow-xl shadow-blue-900/10">
        {{-- Decorative Glow Circles --}}
        <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-blue-500/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-16 right-32 h-64 w-64 rounded-full bg-indigo-500/20 blur-3xl"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="flex flex-wrap items-center gap-2">
                    @if ($periode)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/15 backdrop-blur-md text-blue-100 border border-white/20">
                            <x-icon name="calendar" class="h-3.5 w-3.5 text-sky-300" />
                            <span>Tahun Ajaran {{ $periode }}</span>
                        </span>
                    @endif
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 backdrop-blur-md text-emerald-200 border border-emerald-400/30">
                        <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Sistem Ekstrakurikuler Aktif</span>
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                    Dashboard Admin &amp; Kesiswaan
                </h1>
                <p class="text-xs sm:text-sm text-blue-100/90 leading-relaxed">
                    Pusat kendali operasional pendaftaran eskul, verifikasi berkas siswa, pemantauan kehadiran, dan rekap penilaian digital.
                </p>
            </div>

            {{-- Quick Action Buttons on Hero --}}
            <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                <a href="{{ route('admin.students.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold backdrop-blur-md border border-white/20 shadow-sm transition hover:scale-[1.02] active:scale-[0.98]">
                    <x-icon name="users" class="h-4 w-4 text-sky-200" />
                    <span>Direktori Siswa</span>
                </a>
                <a href="{{ route('admin.registrations.index', ['periode' => $periode]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white text-blue-800 hover:bg-blue-50 text-xs font-extrabold shadow-md shadow-blue-950/20 transition hover:scale-[1.02] active:scale-[0.98]">
                    <x-icon name="clipboard" class="h-4 w-4 text-blue-700" />
                    <span>Data Pendaftar</span>
                </a>
                <a href="{{ route('admin.wa-blast.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold shadow-md shadow-emerald-950/20 transition hover:scale-[1.02] active:scale-[0.98]">
                    <x-icon name="send" class="h-4 w-4" />
                    <span>Kirim WA Blast</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Metric Stat Cards (Grid 4 Kolom) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Total Pendaftar --}}
        <div class="group relative overflow-hidden rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs hover:border-blue-300 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">Total Pendaftar</span>
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/25 group-hover:scale-110 transition-transform duration-200">
                    <x-icon name="clipboard" class="h-5 w-5" />
                </span>
            </div>
            <div class="mt-3 text-3xl font-black text-slate-900 tracking-tight">{{ number_format($total) }}</div>
            <div class="mt-1 flex items-center gap-1.5 text-xs text-slate-500">
                <span class="inline-flex items-center gap-1 font-semibold text-emerald-600">
                    <x-icon name="check" class="h-3.5 w-3.5" />
                    {{ $approved }} diterima
                </span>
                <span>•</span>
                <span class="font-semibold text-amber-600">{{ $pending }} pending</span>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('admin.registrations.index', ['periode' => $periode]) }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-700 group-hover:translate-x-0.5 transition-transform">
                    <span>Kelola Data Pendaftar</span>
                    <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                </a>
            </div>
        </div>

        {{-- Card 2: Menunggu Verifikasi --}}
        <div class="group relative overflow-hidden rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs hover:border-amber-300 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">Menunggu Verifikasi</span>
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 text-white shadow-md shadow-amber-500/25 group-hover:scale-110 transition-transform duration-200">
                    <x-icon name="clock" class="h-5 w-5" />
                </span>
            </div>
            <div class="mt-3 text-3xl font-black text-slate-900 tracking-tight">{{ number_format($pending) }}</div>
            <div class="mt-1 text-xs text-slate-500">
                @if ($pending > 0)
                    <span class="font-semibold text-amber-600">⚠️ Butuh persetujuan admin</span>
                @else
                    <span class="font-semibold text-emerald-600">✓ Semua berkas tuntas</span>
                @endif
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('admin.registrations.index', ['periode' => $periode, 'status' => 'PENDING']) }}" class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 hover:text-amber-700 group-hover:translate-x-0.5 transition-transform">
                    <span>Tinjau Berkas Masuk</span>
                    <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                </a>
            </div>
        </div>

        {{-- Card 3: Ekstrakurikuler Aktif --}}
        <div class="group relative overflow-hidden rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs hover:border-purple-300 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">Ekstrakurikuler Aktif</span>
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-tr from-violet-600 to-purple-600 text-white shadow-md shadow-purple-500/25 group-hover:scale-110 transition-transform duration-200">
                    <x-icon name="trophy" class="h-5 w-5" />
                </span>
            </div>
            <div class="mt-3 text-3xl font-black text-slate-900 tracking-tight">{{ $distEskul->where('eskul.aktif', true)->count() }}</div>
            <div class="mt-1 text-xs text-slate-500 font-medium">
                Dari total {{ $distEskul->count() }} cabang eskul terdaftar
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('admin.eskuls.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-purple-600 hover:text-purple-700 group-hover:translate-x-0.5 transition-transform">
                    <span>Kelola Master Eskul</span>
                    <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                </a>
            </div>
        </div>

        {{-- Card 4: Siswa Aktif Terdaftar --}}
        <div class="group relative overflow-hidden rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs hover:border-emerald-300 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">Siswa Aktif Terdaftar</span>
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-500/25 group-hover:scale-110 transition-transform duration-200">
                    <x-icon name="users" class="h-5 w-5" />
                </span>
            </div>
            <div class="mt-3 text-3xl font-black text-slate-900 tracking-tight">{{ number_format(\App\Models\Student::where('status', 'AKTIF')->count()) }}</div>
            <div class="mt-1 text-xs text-slate-500 font-medium">
                Basis data induk siswa sekolah
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('admin.students.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 hover:text-emerald-700 group-hover:translate-x-0.5 transition-transform">
                    <span>Buka Direktori Siswa</span>
                    <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                </a>
            </div>
        </div>
    </div>

    {{-- Pusat Tugas & Tindakan (Action Center) --}}
    @if (count($tugas) > 0)
        <div class="rounded-3xl border border-amber-200/90 bg-gradient-to-br from-amber-50/90 via-white to-amber-50/40 p-5 shadow-xs">
            <div class="flex items-center justify-between mb-3.5">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-amber-500 text-white shadow-xs">
                        <x-icon name="alert" class="h-4 w-4" />
                    </span>
                    <div>
                        <h2 class="font-extrabold text-slate-900 text-sm">Pusat Tindakan &amp; Perhatian</h2>
                        <p class="text-[11px] text-slate-500">Tugas sistem yang membutuhkan penanganan administrator.</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-800 border border-amber-200">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-ping"></span>
                    <span>{{ count($tugas) }} Butuh Perhatian</span>
                </span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                @foreach ($tugas as $t)
                    @if ($t['url'])
                        <a href="{{ $t['url'] }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-white border border-amber-200/80 hover:border-amber-400 hover:shadow-sm text-xs font-semibold text-slate-800 transition-all group">
                            <span class="truncate mr-2 text-slate-700 group-hover:text-amber-900">{{ $t['label'] }}</span>
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600 group-hover:bg-amber-500 group-hover:text-white transition-colors">
                                <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                            </span>
                        </a>
                    @else
                        <div class="flex items-center p-3.5 rounded-2xl bg-white/80 border border-amber-200/60 text-xs font-medium text-slate-600">
                            <span class="truncate">{{ $t['label'] }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    {{-- Akses Pintasan Menu Cepat (Quick Shortcuts Hub) --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Akses Pintasan Cepat</h2>
            <span class="text-xs text-slate-400">1-Klik menuju fitur utama</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            {{-- Shortcut 1 --}}
            <a href="{{ route('admin.registrations.index', ['periode' => $periode]) }}" class="group flex flex-col items-center text-center p-4 rounded-3xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-blue-300 transition-all duration-200">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-200 shadow-xs mb-2.5">
                    <x-icon name="clipboard" class="h-6 w-6" />
                </span>
                <span class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition-colors">Pendaftar</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Kelola verifikasi</span>
            </a>

            {{-- Shortcut 2 --}}
            <a href="{{ route('admin.students.index') }}" class="group flex flex-col items-center text-center p-4 rounded-3xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-emerald-300 transition-all duration-200">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-200 shadow-xs mb-2.5">
                    <x-icon name="users" class="h-6 w-6" />
                </span>
                <span class="text-xs font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">Data Siswa</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Import &amp; Token</span>
            </a>

            {{-- Shortcut 3 --}}
            <a href="{{ route('admin.eskuls.index') }}" class="group flex flex-col items-center text-center p-4 rounded-3xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-purple-300 transition-all duration-200">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-50 text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors duration-200 shadow-xs mb-2.5">
                    <x-icon name="trophy" class="h-6 w-6" />
                </span>
                <span class="text-xs font-bold text-slate-900 group-hover:text-purple-600 transition-colors">Master Eskul</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Jadwal &amp; kuota</span>
            </a>

            {{-- Shortcut 4 --}}
            <a href="{{ route('admin.kehadiran.index') }}" class="group flex flex-col items-center text-center p-4 rounded-3xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-sky-300 transition-all duration-200">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 group-hover:bg-sky-600 group-hover:text-white transition-colors duration-200 shadow-xs mb-2.5">
                    <x-icon name="calendar-check" class="h-6 w-6" />
                </span>
                <span class="text-xs font-bold text-slate-900 group-hover:text-sky-600 transition-colors">Presensi</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Absensi latihan</span>
            </a>

            {{-- Shortcut 5 --}}
            <a href="{{ route('admin.wa-blast.index') }}" class="group flex flex-col items-center text-center p-4 rounded-3xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-emerald-300 transition-all duration-200">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-200 shadow-xs mb-2.5">
                    <x-icon name="send" class="h-6 w-6" />
                </span>
                <span class="text-xs font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">WA Blast</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Kirim notifikasi</span>
            </a>

            {{-- Shortcut 6 --}}
            <a href="{{ route('admin.nilai.index') }}" class="group flex flex-col items-center text-center p-4 rounded-3xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-amber-300 transition-all duration-200">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-colors duration-200 shadow-xs mb-2.5">
                    <x-icon name="file" class="h-6 w-6" />
                </span>
                <span class="text-xs font-bold text-slate-900 group-hover:text-amber-600 transition-colors">Nilai Rapor</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Rekapitulasi nilai</span>
            </a>
        </div>
    </div>

    {{-- Main Analytics & Data Section (2 Kolom) --}}
    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Distribusi Peminat Per Eskul --}}
        <x-card class="shadow-xs hover:border-slate-300 transition-all duration-200">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <x-icon name="trophy" class="h-4 w-4" />
                    </span>
                    <div>
                        <h2 class="font-extrabold text-slate-900 text-sm">Distribusi Per Eskul</h2>
                        <p class="text-[11px] text-slate-400">Total siswa terdaftar per ekstrakurikuler</p>
                    </div>
                </div>
                <a href="{{ route('admin.rekap.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline">
                    <span>Rekap Lengkap</span>
                    <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                </a>
            </div>

            <div class="space-y-3.5">
                @forelse ($distEskul as $idx => $row)
                    @php
                        $penuh = $row['eskul']->kuota_aktif && $row['count'] >= $row['eskul']->kuota;
                        $pct = $row['eskul']->kuota_aktif
                            ? min(100, round($row['count'] / max(1, $row['eskul']->kuota) * 100))
                            : ($maxEskul > 0 ? min(100, round($row['count'] / $maxEskul * 100)) : 0);
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[10px] font-black {{ $idx === 0 ? 'bg-amber-100 text-amber-800' : ($idx === 1 ? 'bg-slate-200 text-slate-700' : ($idx === 2 ? 'bg-amber-50 text-amber-900 border border-amber-200' : 'bg-slate-100 text-slate-500')) }}">
                                    {{ $idx + 1 }}
                                </span>
                                <span class="font-bold text-slate-900 truncate">{{ $row['eskul']->name }}</span>
                                @if (! $row['eskul']->aktif)
                                    <span class="shrink-0 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-500">Nonaktif</span>
                                @elseif ($penuh)
                                    <span class="shrink-0 px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700">Penuh</span>
                                @endif
                            </div>
                            <span class="font-extrabold text-slate-700 tabular-nums shrink-0 ml-2">
                                {{ $row['count'] }}
                                @if ($row['eskul']->kuota_aktif)
                                    <span class="text-slate-400 font-normal">/ {{ $row['eskul']->kuota }}</span>
                                @else
                                    <span class="text-slate-400 font-normal">siswa</span>
                                @endif
                            </span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full transition-all duration-500 {{ $penuh ? 'bg-rose-500' : ($idx === 0 ? 'bg-gradient-to-r from-blue-600 to-indigo-600' : 'bg-blue-600') }}" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <x-empty-state title="Belum ada data eskul" message="Daftar ekstrakurikuler akan muncul di sini." />
                @endforelse
            </div>
        </x-card>

        <div class="space-y-6">
            {{-- Distribusi Per Jurusan --}}
            <x-card class="shadow-xs hover:border-slate-300 transition-all duration-200">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                            <x-icon name="chart" class="h-4 w-4" />
                        </span>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-sm">Distribusi Per Jurusan</h2>
                            <p class="text-[11px] text-slate-400">Sebaran peminatan eskul berdasarkan konsentrasi keahlian</p>
                        </div>
                    </div>
                </div>
                @php
                    $chartJurusan = collect($distJurusan)->map(fn ($j) => [
                        'label' => $j['label'],
                        'value' => $j['count'],
                        'display' => $j['count'].' siswa',
                        'tone' => 'blue',
                    ])->all();
                @endphp
                <x-bar-chart :items="$chartJurusan" :max="$maxJurusan" />
            </x-card>

            {{-- Pendaftar Terbaru --}}
            <x-card class="shadow-xs hover:border-slate-300 transition-all duration-200">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <x-icon name="clipboard" class="h-4 w-4" />
                        </span>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-sm">Pendaftar Terbaru</h2>
                            <p class="text-[11px] text-slate-400">Aktivitas pendaftaran siswa periode ini</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.registrations.index', ['periode' => $periode]) }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline">
                        <span>Lihat Semua</span>
                        <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($terbaru as $r)
                        @php
                            $nama = $r->user?->name ?? 'Siswa';
                            $initial = strtoupper(substr($nama, 0, 1));
                            $bgColors = ['bg-blue-100 text-blue-700', 'bg-indigo-100 text-indigo-700', 'bg-purple-100 text-purple-700', 'bg-emerald-100 text-emerald-700', 'bg-amber-100 text-amber-700'];
                            $avatarCls = $bgColors[crc32($nama) % count($bgColors)];
                        @endphp
                        <div class="flex items-center gap-3 py-3 hover:bg-slate-50/80 rounded-2xl px-2 transition-colors">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $avatarCls }} text-xs font-black shadow-2xs">
                                {{ $initial }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-xs font-bold text-slate-900">{{ $nama }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="font-mono text-[11px] text-slate-400">{{ $r->kode() }}</span>
                                    <span class="text-[10px] text-slate-300">•</span>
                                    <span class="text-[11px] font-medium text-slate-600 truncate">{{ $r->eskul_utama }}</span>
                                </div>
                            </div>
                            <div class="shrink-0 text-right">
                                <x-badge tone="{{ $r->status->value === 'APPROVED' ? 'emerald' : ($r->status->value === 'PENDING' ? 'amber' : 'rose') }}" class="!text-[10px]">
                                    {{ $r->status->label() }}
                                </x-badge>
                                <p class="text-[10px] text-slate-400 mt-1 font-medium">{{ $r->created_at?->diffForHumans() ?? 'Baru saja' }}</p>
                            </div>
                        </div>
                    @empty
                        <x-empty-state title="Belum ada pendaftar" message="Pendaftar terbaru akan muncul di sini." />
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection

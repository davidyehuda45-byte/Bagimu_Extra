@extends('layouts.staff')

@section('title', 'Dashboard Wali Kelas')

@section('content')
<div class="space-y-6">
    {{-- Header & Info Rombel --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs">
        <div class="flex items-center gap-4">
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-md shadow-indigo-600/20">
                <x-icon name="academic-cap" class="h-7 w-7" />
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-lg sm:text-xl font-bold text-slate-900">
                        Rombel: {{ $kelas?->nama ?? 'Belum Ditentukan' }}
                    </h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">
                        Wali Kelas
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Wali Kelas: <b>{{ $kelas?->wali_kelas_nama ?? auth()->user()->name }}</b> • T.A. {{ $periode?->periode ?? 'Semua' }}
                </p>
            </div>
        </div>

        {{-- Switch Kelas & Periode bila multi-kelas atau admin --}}
        <form method="GET" action="{{ route('wali-kelas.dashboard') }}" class="flex flex-wrap items-center gap-2 self-start sm:self-center">
            @if (auth()->user()->isAdmin() && $allClasses->count() > 1)
                <div>
                    <select name="kelas_id" class="border-slate-300 rounded-xl px-3 py-1.5 text-xs font-semibold focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                        @foreach ($allClasses as $c)
                            <option value="{{ $c->id }}" @selected($kelas && $kelas->id === $c->id)>{{ $c->nama }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div>
                <select name="periode" class="border-slate-300 rounded-xl px-3 py-1.5 text-xs font-semibold focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                    @foreach ($periodes as $p)
                        <option value="{{ $p->periode }}" @selected($periode && $periode->periode === $p->periode)>{{ $p->periode }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    @if (! $kelas)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-xs font-semibold text-amber-800 flex items-center gap-3">
            <x-icon name="alert-circle" class="h-5 w-5 text-amber-600 shrink-0" />
            <div>
                <p class="font-bold text-sm text-amber-900">Akun Anda Belum Ditugaskan ke Rombel Kelas</p>
                <p class="mt-0.5 text-amber-700">Hubungi Administrator sekolah untuk menautkan akun Anda ke rombel binaan di menu <b>Kelola Wali Kelas</b>.</p>
            </div>
        </div>
    @endif

    {{-- Kartu Statistik Rombel --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Siswa Rombel</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-slate-900">{{ $totalSiswa }}</span>
                <span class="text-xs text-slate-500 font-semibold">Siswa</span>
            </div>
        </div>

        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/40 p-5 shadow-xs">
            <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider block">Terdaftar Eskul</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-emerald-700">{{ $totalIkutEskul }}</span>
                <span class="text-xs text-emerald-600 font-bold">{{ $totalSiswa > 0 ? round(($totalIkutEskul / $totalSiswa) * 100) : 0 }}%</span>
            </div>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-amber-50/40 p-5 shadow-xs">
            <span class="text-xs font-bold text-amber-800 uppercase tracking-wider block">Belum Memilih Eskul</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-amber-700">{{ $belumIkut }}</span>
                <span class="text-xs text-amber-600 font-bold">Perlu diarahkan</span>
            </div>
        </div>

        <div class="rounded-2xl border border-blue-200 bg-blue-50/40 p-5 shadow-xs">
            <span class="text-xs font-bold text-blue-800 uppercase tracking-wider block">Nilai Masuk untuk Rapor</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-blue-700">{{ $sudahDinilai }}</span>
                <a href="{{ route('wali-kelas.nilai', ['kelas_id' => $kelas?->id, 'periode' => $periode?->periode]) }}" class="text-xs font-bold text-blue-700 hover:underline">
                    Lihat Nilai →
                </a>
            </div>
        </div>
    </div>

    {{-- Navigasi Cepat & Rekap Eskul Siswa --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-5 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700">
                        <x-icon name="user-group" class="h-4 w-4" />
                    </span>
                    <h2 class="text-sm font-bold text-slate-900">Aktivitas Siswa Rombel Ini</h2>
                </div>
                <a href="{{ route('wali-kelas.siswa', ['kelas_id' => $kelas?->id, 'periode' => $periode?->periode]) }}" class="text-xs font-bold text-blue-700 hover:underline">
                    Lihat Semua Siswa →
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider font-bold">
                        <tr>
                            <th class="py-2 px-3 text-left">Nama Siswa</th>
                            <th class="py-2 px-3 text-left">NIS</th>
                            <th class="py-2 px-3 text-left">Eskul Pilihan</th>
                            <th class="py-2 px-3 text-center">Nilai Rapor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($registrations->take(8) as $r)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-2 px-3 font-semibold text-slate-900">{{ $r->student?->nama ?? $r->user?->name ?? '-' }}</td>
                                <td class="py-2 px-3 text-slate-600 font-mono">{{ $r->nis }}</td>
                                <td class="py-2 px-3 font-bold text-indigo-700">{{ $r->eskul_utama }}</td>
                                <td class="py-2 px-3 text-center">
                                    @if ($r->nilai !== null)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full font-bold bg-emerald-100 text-emerald-800 text-[11px]">
                                            {{ $r->nilai }} ({{ $r->predikat ?? 'A' }})
                                        </span>
                                    @else
                                        <span class="text-slate-400">— Belum —</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-400">Belum ada siswa yang mendaftar eskul di periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Panel Aksi Wali Kelas --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs space-y-4">
            <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">Menu Cepat Wali Kelas</h2>

            <div class="space-y-2.5">
                <a href="{{ route('wali-kelas.nilai', ['kelas_id' => $kelas?->id, 'periode' => $periode?->periode]) }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-200/80 hover:bg-slate-50 transition-colors">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                        <x-icon name="clipboard-check" class="h-5 w-5" />
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-900 block">Rekap Nilai Eskul</span>
                        <span class="text-[11px] text-slate-400">Lihat nilai siswa untuk dimasukkan ke rapor</span>
                    </div>
                </a>

                <a href="{{ route('wali-kelas.cetak-nilai', ['kelas_id' => $kelas?->id, 'periode' => $periode?->periode]) }}" target="_blank" class="flex items-center gap-3 p-3 rounded-xl border border-indigo-200 bg-indigo-50/50 hover:bg-indigo-50 transition-colors">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-xs">
                        <x-icon name="printer" class="h-5 w-5" />
                    </div>
                    <div>
                        <span class="text-xs font-bold text-indigo-950 block">Cetak Lembar Nilai Rapor</span>
                        <span class="text-[11px] text-indigo-700">Format cetak siap lampirkan ke rapor</span>
                    </div>
                </a>

                <a href="{{ route('wali-kelas.siswa', ['kelas_id' => $kelas?->id, 'periode' => $periode?->periode]) }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-200/80 hover:bg-slate-50 transition-colors">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                        <x-icon name="users" class="h-5 w-5" />
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-900 block">Direktori Siswa Rombel</span>
                        <span class="text-[11px] text-slate-400">Pantau kontak dan keikutsertaan siswa</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection


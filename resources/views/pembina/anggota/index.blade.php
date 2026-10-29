@extends('layouts.staff')

@section('title', 'Direktori Anggota Siswa — Pembina')

@section('content')
<div class="space-y-6">
    {{-- Header Direktori Anggota --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-bold text-slate-900">Direktori Anggota Siswa</h1>
                <span class="inline-flex items-center rounded-xl bg-blue-50 border border-blue-200 px-2.5 py-1 text-xs font-bold text-blue-800 shadow-2xs">
                    {{ $totalAnggota }} Anggota
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Daftar lengkap murid binaan eskul <b>{{ $currentEskul?->name ?? 'Ekstrakurikuler' }}</b> • Tahun Ajaran {{ $selectedPeriode }}.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if ($currentEskul)
                <a href="{{ route('pembina.anggota.export', ['eskul_id' => $currentEskul->id, 'periode' => $selectedPeriode]) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-emerald-300 bg-emerald-50 text-xs font-bold text-emerald-800 shadow-2xs hover:bg-emerald-100 transition-colors"
                   title="Unduh data anggota dalam format Excel">
                    <x-icon name="chart" class="h-4 w-4 text-emerald-700" />
                    <span>Export Excel</span>
                </a>

                <a href="{{ route('pembina.scan.show', $currentEskul->id) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-blue-200 bg-blue-50 text-xs font-bold text-blue-700 shadow-2xs hover:bg-blue-100 transition-colors"
                   title="Pindai QR E-Card Siswa untuk Presensi Cepat">
                    <x-icon name="scan" class="h-4 w-4 text-blue-600" />
                    <span>Scan Presensi</span>
                </a>

                <a href="{{ route('pembina.eskul.show', [$currentEskul->id, 'tab' => 'anggota']) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-700 text-xs font-bold text-white shadow-xs hover:bg-blue-800 transition-colors">
                    <x-icon name="user-plus" class="h-4 w-4" />
                    <span>Kelola Anggota</span>
                </a>
            @endif
        </div>
    </div>

    {{-- Filter & Pencarian Cepat --}}
    <div class="rounded-2xl border border-slate-200/90 bg-white p-4 shadow-xs">
        <form method="GET" action="{{ route('pembina.anggota.index') }}" class="flex flex-wrap items-center gap-3">
            @if ($eskuls->count() > 1)
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Eskul</label>
                    <select name="eskul_id" class="rounded-xl border border-slate-300 pl-3 pr-8 py-2 text-xs font-bold text-slate-800 bg-white focus:border-blue-600 focus:ring-blue-600" onchange="this.form.submit()">
                        @foreach ($eskuls as $e)
                            <option value="{{ $e->id }}" @selected($currentEskul && $currentEskul->id === $e->id)>{{ $e->name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <input type="hidden" name="eskul_id" value="{{ $currentEskul?->id }}">
            @endif

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tahun Ajaran</label>
                <select name="periode" class="rounded-xl border border-slate-300 pl-3 pr-8 py-2 text-xs font-bold text-slate-800 bg-white focus:border-blue-600 focus:ring-blue-600" onchange="this.form.submit()">
                    @foreach ($allPeriodes as $p)
                        <option value="{{ $p->periode }}" @selected($selectedPeriode === $p->periode)>{{ $p->periode }}</option>
                    @endforeach
                </select>
            </div>

            @if ($daftarKelas->isNotEmpty())
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kelas Rombel</label>
                    <select name="kelas" class="rounded-xl border border-slate-300 pl-3 pr-8 py-2 text-xs font-bold text-slate-800 bg-white focus:border-blue-600 focus:ring-blue-600" onchange="this.form.submit()">
                        <option value="">Semua Rombel</option>
                        @foreach ($daftarKelas as $k)
                            <option value="{{ $k }}" @selected(request('kelas') === $k)>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="flex-1 min-w-[200px]">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Murid</label>
                <div class="relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari berdasarkan NIS atau Nama siswa..."
                           class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2 text-xs text-slate-800 placeholder-slate-400 focus:border-blue-600 focus:ring-blue-600">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <x-icon name="search" class="h-3.5 w-3.5" />
                    </div>
                </div>
            </div>

            <div class="self-end flex items-center gap-2">
                <button type="submit" class="rounded-xl bg-blue-700 px-4 py-2 text-xs font-bold text-white hover:bg-blue-800 transition shadow-2xs cursor-pointer">
                    Filter
                </button>
                @if (request()->hasAny(['q', 'kelas']))
                    <a href="{{ route('pembina.anggota.index', ['eskul_id' => $currentEskul?->id, 'periode' => $selectedPeriode]) }}"
                       class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Ringkasan Metrik Singkat --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Anggota</span>
            <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $totalAnggota }}</span>
            <span class="text-[10px] text-slate-500">Siswa aktif terdaftar</span>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Sudah Dinilai</span>
            <span class="text-2xl font-black text-emerald-700 mt-1 block">{{ $sudahDinilai }}</span>
            <span class="text-[10px] text-emerald-600 font-semibold">{{ $totalAnggota > 0 ? round(($sudahDinilai / $totalAnggota) * 100) : 0 }}% Ketercapaian</span>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Belum Dinilai</span>
            <span class="text-2xl font-black text-amber-700 mt-1 block">{{ max(0, $totalAnggota - $sudahDinilai) }}</span>
            <span class="text-[10px] text-amber-600 font-semibold">Perlu input nilai</span>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Sebaran Rombel</span>
            <span class="text-2xl font-black text-blue-700 mt-1 block">{{ $daftarKelas->count() }}</span>
            <span class="text-[10px] text-slate-500">Kelas asal siswa</span>
        </div>
    </div>

    {{-- Tabel Direktori Anggota --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider font-bold text-[11px] border-b border-slate-200/80">
                    <tr>
                        <th class="py-3 px-4 text-center w-12">No</th>
                        <th class="py-3 px-4 text-left">NIS</th>
                        <th class="py-3 px-4 text-left">Nama Lengkap Murid</th>
                        <th class="py-3 px-4 text-center">L/P</th>
                        <th class="py-3 px-4 text-left">Kelas Rombel</th>
                        <th class="py-3 px-4 text-center">Hadir</th>
                        <th class="py-3 px-4 text-center">Tidak Hadir</th>
                        <th class="py-3 px-4 text-center">Pertemuan</th>
                        <th class="py-3 px-4 text-center">Nilai Rapor</th>
                        <th class="py-3 px-4 text-center">Predikat</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($anggotaList as $index => $reg)
                        @php
                            $student = $reg->student;
                            $hadirCount = $reg->kehadiran->where('status', \App\Enums\KehadiranStatus::HADIR)->count();
                            $izinCount = $reg->kehadiran->where('status', \App\Enums\KehadiranStatus::IZIN)->count();
                            $sakitCount = $reg->kehadiran->where('status', \App\Enums\KehadiranStatus::SAKIT)->count();
                            $alpaCount = $reg->kehadiran->where('status', \App\Enums\KehadiranStatus::ALPA)->count();
                            $tidakHadirCount = $izinCount + $sakitCount + $alpaCount;
                            $totalPertemuan = $reg->kehadiran->count();
                            $predikat = $reg->predikat ?: ($reg->nilai !== null ? ($reg->nilai >= 85 ? 'A' : ($reg->nilai >= 70 ? 'B' : ($reg->nilai >= 55 ? 'C' : 'D'))) : '-');
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-4 text-center font-bold text-slate-400">
                                {{ $anggotaList->firstItem() + $index }}
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-700">
                                {{ $reg->nis }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">
                                    {{ $student?->nama ?: ($reg->user?->name ?: '-') }}
                                </div>
                                @if ($reg->whatsapp || $student?->whatsapp)
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        WA: {{ $reg->whatsapp ?: $student?->whatsapp }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @php $g = $reg->gender?->value ?: ($student?->gender?->value ?: '-'); @endphp
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold {{ $g === 'L' || $g === 'LAKI_LAKI' ? 'bg-blue-50 text-blue-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $g === 'L' || $g === 'LAKI_LAKI' ? 'L' : ($g === 'P' || $g === 'PEREMPUAN' ? 'P' : '-') }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-700">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-slate-100 text-slate-800 font-bold text-[11px]">
                                    {{ $reg->kelas ?: ($student?->kelas ?: '-') }}
                                </span>
                            </td>
                            {{-- Ringkasan Kehadiran: Berapa kali Hadir --}}
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 font-black text-xs border border-emerald-200">
                                    {{ $hadirCount }}
                                </span>
                            </td>
                            {{-- Ringkasan Kehadiran: Berapa kali Tidak Hadir --}}
                            <td class="py-3 px-4 text-center">
                                @if ($tidakHadirCount > 0)
                                    <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full bg-rose-50 text-rose-800 font-black text-xs border border-rose-200"
                                          title="Rincian: Izin: {{ $izinCount }}, Sakit: {{ $sakitCount }}, Alpa: {{ $alpaCount }}">
                                        {{ $tidakHadirCount }}
                                    </span>
                                    <span class="block text-[9px] text-slate-400 mt-0.5">
                                        {{ $izinCount }}I / {{ $sakitCount }}S / {{ $alpaCount }}A
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 font-bold text-xs">
                                        0
                                    </span>
                                @endif
                            </td>
                            {{-- Total Pertemuan Terselenggara --}}
                            <td class="py-3 px-4 text-center font-bold text-slate-600">
                                {{ $totalPertemuan }}
                            </td>
                            {{-- Nilai Angka Rapor --}}
                            <td class="py-3 px-4 text-center">
                                @if ($reg->nilai !== null)
                                    <span class="font-black text-sm text-slate-900 font-mono">{{ $reg->nilai }}</span>
                                @else
                                    <span class="text-slate-300 font-bold">—</span>
                                @endif
                            </td>
                            {{-- Predikat Rapor --}}
                            <td class="py-3 px-4 text-center">
                                @if ($predikat !== '-')
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-lg font-black text-xs {{ $predikat === 'A' ? 'bg-emerald-100 text-emerald-800' : ($predikat === 'B' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $predikat }}
                                    </span>
                                @else
                                    <span class="text-slate-300 font-bold">—</span>
                                @endif
                            </td>
                            {{-- Aksi Cepat --}}
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('pembina.eskul.show', [$currentEskul->id, 'tab' => 'nilai']) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:text-blue-700 font-bold text-[11px] shadow-2xs transition-colors"
                                       title="Input atau Perbarui Nilai Rapor Siswa">
                                        <x-icon name="pencil" class="h-3 w-3 text-slate-500" />
                                        <span>Nilai</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-8 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <x-icon name="users" class="h-8 w-8 text-slate-300" />
                                    <p class="font-semibold text-slate-600">Tidak ada anggota siswa yang sesuai dengan kriteria pencarian.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($anggotaList->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $anggotaList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

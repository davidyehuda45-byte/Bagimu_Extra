@extends('layouts.staff')

@section('title', 'Rekap & Riwayat Ekstrakurikuler')

@section('content')
<div class="space-y-6">
    {{-- Header & Pemilih Periode --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200/80 pb-5">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Rekapitulasi & Riwayat Ekstrakurikuler</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pemantauan data pendaftar, pembina eskul, dan sebaran kelas per tahun ajaran.</p>
        </div>

        {{-- Filter Periode & Tombol Ekspor --}}
        <div class="flex flex-wrap items-center gap-2.5">
            <form method="GET" action="{{ route('admin.rekap.index') }}" class="flex items-center gap-2">
                <label for="periode-selector" class="text-xs font-semibold text-slate-600 shrink-0">Tahun Periode:</label>
                <div class="relative">
                    <select id="periode-selector" name="periode" onchange="this.form.submit()" class="rounded-xl border-slate-300 py-1.5 pl-3 pr-8 text-xs font-bold text-slate-800 bg-white shadow-2xs focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        @foreach ($allPeriodes as $p)
                            <option value="{{ $p->periode }}" @selected($selectedPeriode === $p->periode)>
                                {{ $p->periode }} {{ $p->nama ? "({$p->nama})" : '' }} — {{ $p->status->value === 'DIBUKA' ? 'Aktif' : $p->status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            <a href="{{ route('admin.rekap.export', ['periode' => $selectedPeriode]) }}" class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-300 bg-emerald-50 px-3.5 py-1.5 text-xs font-bold text-emerald-800 hover:bg-emerald-100 transition shadow-2xs cursor-pointer">
                <x-icon name="chart" class="h-3.5 w-3.5 text-emerald-700" />
                <span>Ekspor Excel (.xls)</span>
            </a>
        </div>
    </div>

    {{-- Banner Status Periode Terpilih --}}
    @php
        $isAktif = $periodeModel?->status?->value === 'DIBUKA';
        $isArsip = $periodeModel?->status?->value === 'DIARSIPKAN';
    @endphp
    <div class="rounded-2xl border p-4 shadow-2xs {{ $isAktif ? 'border-blue-200 bg-blue-50/50' : ($isArsip ? 'border-slate-300 bg-slate-100/70' : 'border-amber-200 bg-amber-50/50') }}">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-start sm:items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $isAktif ? 'bg-blue-600 text-white' : ($isArsip ? 'bg-slate-300 text-slate-700' : 'bg-amber-500 text-white') }} shadow-2xs">
                    <x-icon name="calendar-check" class="h-5 w-5" />
                </span>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-slate-900">Periode {{ $selectedPeriode }}</span>
                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold {{ $isAktif ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($isArsip ? 'bg-slate-200 text-slate-700 border border-slate-300' : 'bg-amber-100 text-amber-800 border border-amber-300') }}">
                            {{ $isAktif ? 'Periode Aktif' : ($isArsip ? 'Arsip Riwayat' : 'Periode Ditutup') }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 mt-0.5">
                        {{ $periodeModel?->nama ? $periodeModel->nama . ' • ' : '' }}
                        {{ $isAktif ? 'Pendaftaran sedang berlangsung atau dibuka untuk siswa.' : 'Periode telah selesai. Data di bawah merupakan arsip resmi riwayat pendaftaran.' }}
                    </p>
                </div>
            </div>

            {{-- Kartu Ringkasan Angka --}}
            <div class="flex items-center gap-4 text-xs shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-200">
                <div class="text-right">
                    <span class="block text-slate-500 text-[11px]">Total Pendaftar</span>
                    <span class="font-extrabold text-slate-900 text-base font-mono">{{ $totalPendaftar }}</span>
                </div>
                <div class="h-7 w-px bg-slate-200"></div>
                <div class="text-right">
                    <span class="block text-slate-500 text-[11px]">Disetujui</span>
                    <span class="font-extrabold text-emerald-700 text-base font-mono">{{ $totalApproved }}</span>
                </div>
                <div class="h-7 w-px bg-slate-200"></div>
                <div class="text-right">
                    <span class="block text-slate-500 text-[11px]">Eskul Aktif</span>
                    <span class="font-extrabold text-blue-700 text-base font-mono">{{ $totalEskulAktif }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid Kartu Rekap Per Eskul --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($rekap as $row)
            @php
                $e = $row['eskul'];
                $penuh = $e->kuota_aktif && $row['count'] >= $e->kuota;
                $pct = $e->kuota_aktif ? min(100, round($row['count'] / max(1, $e->kuota) * 100)) : min(100, $row['count'] * 10);
            @endphp
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs hover:shadow-sm transition flex flex-col justify-between">
                <div>
                    {{-- Header Kartu Eskul --}}
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h2 class="font-bold text-slate-900 text-base">{{ $e->name }}</h2>
                            <p class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ $e->description ?: 'Kegiatan pengembangan bakat siswa' }}</p>
                        </div>
                        @if (! $e->aktif)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200 shrink-0">Nonaktif</span>
                        @endif
                    </div>

                    {{-- Informasi Guru Pembina --}}
                    <div class="mt-3 p-2.5 rounded-xl bg-slate-50 border border-slate-200/70 text-xs">
                        <div class="flex items-center gap-1.5 text-slate-700 font-semibold">
                            <x-icon name="user" class="h-3.5 w-3.5 text-blue-600 shrink-0" />
                            <span class="text-slate-500 font-medium">Pembina:</span>
                            <span class="truncate text-slate-900">{{ $row['pembina_nama'] }}</span>
                        </div>
                        @if (!empty($row['pembina_kontak']))
                            <div class="mt-1 flex items-center gap-1 text-[11px] text-emerald-700 pl-5">
                                <x-icon name="phone" class="h-3 w-3 text-emerald-600" />
                                <span>{{ $row['pembina_kontak'] }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Angka Keterisian Kuota --}}
                    <div class="mt-3.5">
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-bold text-slate-800">{{ $row['count'] }} Siswa Terdaftar</span>
                            <span class="text-[11px] font-semibold text-slate-500">
                                {{ $e->kuota_aktif ? "Kuota: {$row['count']}/{$e->kuota}" : 'Kuota Bebas' }}
                            </span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full transition-all duration-300 {{ $penuh ? 'bg-rose-500' : 'bg-blue-600' }}" style="width: {{ $pct }}%"></div>
                        </div>
                        @if ($penuh)
                            <p class="mt-1 text-[11px] font-bold text-rose-600">Kuota Terisi Penuh</p>
                        @endif
                    </div>

                    {{-- Sebaran Asal Rombel / Kelas --}}
                    <div class="mt-4 pt-3 border-t border-slate-100">
                        <span class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Sebaran Asal Kelas:</span>
                        @if (!empty($row['kelas_dist']) && count($row['kelas_dist']) > 0)
                            <div class="flex flex-wrap gap-1">
                                @foreach ($row['kelas_dist'] as $kd)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-blue-50 text-blue-800 border border-blue-200/80">
                                        <span>{{ $kd['kelas'] }}</span>
                                        <span class="font-bold font-mono">({{ $kd['count'] }})</span>
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-[11px] text-slate-400 italic">Belum ada siswa terdaftar pada periode ini.</p>
                        @endif
                    </div>
                </div>

                {{-- Tombol Lihat Siswa --}}
                <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[11px] text-slate-400">Periode {{ $selectedPeriode }}</span>
                    <a href="{{ route('admin.rekap.show', ['eskul' => $e->name, 'periode' => $selectedPeriode]) }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                        <span>Lihat Rincian Siswa</span>
                        <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                    </a>
                </div>
            </div>
        @empty
            <div class="sm:col-span-2 lg:col-span-3 py-12 text-center text-slate-400 rounded-2xl border border-slate-200 bg-white">
                <x-icon name="trophy" class="h-10 w-10 mx-auto text-slate-300 mb-2" />
                <p class="font-bold text-slate-700">Belum Ada Data Ekstrakurikuler</p>
                <p class="text-xs text-slate-400 mt-1">Data ekstrakurikuler akan muncul setelah dibuat pada menu Ekstrakurikuler.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

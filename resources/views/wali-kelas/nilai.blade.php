@extends('layouts.staff')

@section('title', 'Rekap Nilai Rapor — Wali Kelas')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Rekap Nilai Eskul Rombel {{ $kelas?->nama }}</h1>
            <p class="text-xs text-slate-500">Nilai ekstrakurikuler resmi dari pembina eskul untuk dimasukkan langsung ke buku rapor siswa.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <form method="GET" action="{{ route('wali-kelas.nilai') }}" class="flex items-center gap-2">
                @if (auth()->user()->isAdmin() && $allClasses->count() > 1)
                    <select name="kelas_id" class="border-slate-300 rounded-xl px-3 py-1.5 text-xs font-semibold focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                        @foreach ($allClasses as $c)
                            <option value="{{ $c->id }}" @selected($kelas && $kelas->id === $c->id)>{{ $c->nama }}</option>
                        @endforeach
                    </select>
                @endif
                <select name="periode" class="border-slate-300 rounded-xl px-3 py-1.5 text-xs font-semibold focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                    @foreach ($periodes as $p)
                        <option value="{{ $p->periode }}" @selected($periode && $periode->periode === $p->periode)>{{ $p->periode }}</option>
                    @endforeach
                </select>
            </form>

            <a href="{{ route('wali-kelas.cetak-nilai', ['kelas_id' => $kelas?->id, 'periode' => $periode?->periode]) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-700 text-xs font-bold text-white shadow-xs hover:bg-blue-800 transition-colors">
                <x-icon name="printer" class="h-4 w-4" />
                <span>Cetak Lembar Nilai Rapor</span>
            </a>
        </div>
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

    {{-- Ringkasan Ketercapaian Nilai --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                    <x-icon name="document-check" class="h-4 w-4" />
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Nilai / Predikat Rapor</h2>
                    <p class="text-[11px] text-slate-400">Data nilai telah divalidasi oleh pembina ekstrakurikuler masing-masing</p>
                </div>
            </div>
            <span class="text-xs font-bold text-slate-600">
                {{ $dataNilai->whereNotNull('nilai')->count() }} dari {{ $dataNilai->count() }} Siswa Sudah Dinilai
            </span>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-100">
            <table class="min-w-full text-xs">
                <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider font-bold text-[11px]">
                    <tr>
                        <th class="py-2.5 px-4 text-left">No. Absen / NIS</th>
                        <th class="py-2.5 px-4 text-left">Nama Lengkap Siswa</th>
                        <th class="py-2.5 px-4 text-left">Ekstrakurikuler</th>
                        <th class="py-2.5 px-4 text-center">Nilai Angka</th>
                        <th class="py-2.5 px-4 text-center">Predikat</th>
                        <th class="py-2.5 px-4 text-left">Catatan Capaian / Deskripsi Rapor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($dataNilai as $dn)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-2.5 px-4 font-mono font-bold text-slate-700">
                                {{ $dn->no_absen ? '#'.$dn->no_absen.' • ' : '' }}{{ $dn->nis }}
                            </td>
                            <td class="py-2.5 px-4 font-semibold text-slate-900">
                                {{ $dn->student?->nama ?? $dn->user?->name ?? '-' }}
                            </td>
                            <td class="py-2.5 px-4 font-bold text-indigo-700">
                                {{ $dn->eskul_utama }}
                            </td>
                            <td class="py-2.5 px-4 text-center font-bold text-sm">
                                @if ($dn->nilai !== null)
                                    <span class="text-slate-900 font-mono">{{ $dn->nilai }}</span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-center">
                                @if ($dn->predikat)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md font-black text-xs {{ in_array($dn->predikat, ['A', 'A+']) ? 'bg-emerald-100 text-emerald-800' : (in_array($dn->predikat, ['B', 'B+']) ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $dn->predikat }}
                                    </span>
                                @elseif ($dn->nilai !== null)
                                    @php
                                        $pred = $dn->nilai >= 85 ? 'A' : ($dn->nilai >= 70 ? 'B' : ($dn->nilai >= 55 ? 'C' : 'D'));
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md font-black text-xs bg-slate-100 text-slate-700">
                                        {{ $pred }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[10px]">Belum</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-slate-600">
                                {{ $dn->catatan_nilai ?: ($dn->nilai ? 'Menunjukkan keaktifan dan penguasaan teknik dasar yang baik.' : 'Belum ada catatan dari pembina.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Belum ada data nilai ekstrakurikuler untuk rombel ini di periode terpilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


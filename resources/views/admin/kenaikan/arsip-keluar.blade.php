@extends('layouts.staff')

@section('title', 'Arsip Siswa Keluar / Mutasi')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.kenaikan.index') }}" class="text-xs font-semibold text-blue-700 hover:underline">
                    ← Kembali ke Kenaikan Kelas
                </a>
            </div>
            <h1 class="text-xl font-bold text-slate-900">Arsip Data Siswa Keluar & Mutasi</h1>
            <p class="text-xs text-slate-500">Rekaman data siswa yang dikeluarkan, mutasi, atau tidak naik kelas permanen tetap tersimpan rapi di sini.</p>
        </div>
        <span class="text-xs font-bold text-slate-700 bg-white border border-slate-200 px-3 py-1.5 rounded-xl shadow-2xs">
            Total Arsip: {{ $arsip->total() }} Siswa
        </span>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
        <div class="overflow-x-auto rounded-xl border border-slate-100">
            <table class="min-w-full text-xs">
                <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider font-bold text-[11px]">
                    <tr>
                        <th class="py-2.5 px-4 text-left">Tanggal Keluar</th>
                        <th class="py-2.5 px-4 text-left">NIS / NISN</th>
                        <th class="py-2.5 px-4 text-left">Nama Siswa</th>
                        <th class="py-2.5 px-4 text-left">Kelas Terakhir</th>
                        <th class="py-2.5 px-4 text-center">Status</th>
                        <th class="py-2.5 px-4 text-left">Alasan / Catatan Pengeluaran</th>
                        <th class="py-2.5 px-4 text-left">Diproses Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($arsip as $a)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-2.5 px-4 text-slate-600 font-mono">
                                {{ $a->tanggal_keluar ? $a->tanggal_keluar->format('d/m/Y') : '-' }}
                            </td>
                            <td class="py-2.5 px-4 font-mono font-bold text-slate-800">
                                {{ $a->nis }}
                                @if ($a->nisn)
                                    <span class="text-[10px] text-slate-400 block font-normal">NISN: {{ $a->nisn }}</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 font-semibold text-slate-900">
                                {{ $a->nama }}
                            </td>
                            <td class="py-2.5 px-4 font-semibold text-slate-700">
                                {{ $a->kelas_terakhir ?? '-' }}
                                @if ($a->jurusan)
                                    <span class="text-[10px] text-slate-400 block">{{ $a->jurusan }}</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $a->status_keluar === 'MUTASI' ? 'bg-blue-100 text-blue-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $a->status_keluar }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-slate-700">
                                {{ $a->alasan ?? '—' }}
                            </td>
                            <td class="py-2.5 px-4 text-slate-500 font-medium text-[11px]">
                                {{ $a->diproses_oleh ?? 'Sistem' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                Belum ada siswa yang diarsipkan keluar atau mutasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($arsip->hasPages())
            <div class="mt-4 pt-3 border-t border-slate-100">
                {{ $arsip->links() }}
            </div>
        @endif
    </div>
</div>
@endsection


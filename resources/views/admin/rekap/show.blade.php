@extends('layouts.staff')

@section('title', 'Rincian Siswa — ' . $model->name)

@section('content')
<div class="space-y-6">
    {{-- Header & Navigasi Kembali --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200/80 pb-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.rekap.index', ['periode' => $periode]) }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                    <x-icon name="chevron-left" class="h-3.5 w-3.5" />
                    <span>Kembali ke Rekap Eskul</span>
                </a>
            </div>
            <h1 class="text-xl font-bold text-slate-900">{{ $model->name }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Rincian data siswa terdaftar pada Periode <b>{{ $periode }}</b>
                {{ $model->kuota_aktif ? "• Kuota: {$model->kuota} Siswa" : '• Kuota: Tanpa Batas' }}
            </p>
        </div>

        {{-- Selector Ganti Periode --}}
        <div class="flex items-center gap-2">
            <form method="GET" action="{{ route('admin.rekap.show', $model->name) }}" class="flex items-center gap-2">
                <label for="detail-periode-select" class="text-xs font-semibold text-slate-600 shrink-0">Periode:</label>
                <select id="detail-periode-select" name="periode" onchange="this.form.submit()" class="rounded-xl border-slate-300 py-1.5 pl-3 pr-8 text-xs font-bold text-slate-800 bg-white shadow-2xs focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                    @foreach ($allPeriodes as $p)
                        <option value="{{ $p->periode }}" @selected($periode === $p->periode)>
                            {{ $p->periode }} ({{ $p->status->value === 'DIBUKA' ? 'Aktif' : $p->status->label() }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- Banner Informasi Eskul & Pembina --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="block text-slate-400 font-medium text-[11px]">Guru Pembina</span>
                <span class="font-bold text-slate-900 text-sm flex items-center gap-1.5 mt-0.5">
                    <x-icon name="user" class="h-4 w-4 text-blue-600" />
                    {{ $pembinaNama }}
                </span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="block text-slate-400 font-medium text-[11px]">Total Siswa Terdaftar</span>
                <span class="font-bold text-blue-700 text-sm font-mono mt-0.5 block">{{ $siswa->total() }} Siswa</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="block text-slate-400 font-medium text-[11px]">Status Periode</span>
                <span class="font-bold text-slate-800 text-sm mt-0.5 inline-flex items-center gap-1">
                    <span class="h-2 w-2 rounded-full {{ $periodeModel?->status?->value === 'DIBUKA' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                    {{ $periodeModel?->status?->value === 'DIBUKA' ? 'Periode Aktif' : ($periodeModel?->status?->label() ?? 'Ditutup') }}
                </span>
            </div>
        </div>

        {{-- Statistik Sebaran Rombel Kelas --}}
        @if ($kelasStats->isNotEmpty())
            <div class="mt-4 pt-3 border-t border-slate-100">
                <span class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Sebaran Asal Rombel / Kelas (Periode {{ $periode }}):</span>
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($kelasStats as $ks)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-semibold bg-blue-50 text-blue-800 border border-blue-200/80">
                            <span>{{ $ks->kelas ?: 'Tanpa Kelas' }}</span>
                            <span class="font-mono font-bold bg-blue-200/60 text-blue-900 px-1.5 py-0.2 rounded-md">{{ $ks->total }}</span>
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Filter & Pencarian Siswa --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs">
        <form method="GET" action="{{ route('admin.rekap.show', $model->name) }}" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mb-4">
            <input type="hidden" name="periode" value="{{ $periode }}">
            
            <div class="flex flex-wrap items-center gap-2 flex-1">
                <div class="relative flex-1 sm:max-w-xs">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau NIS siswa..." class="w-full rounded-xl border-slate-300 py-1.5 pl-8 pr-3 text-xs focus:border-blue-500 focus:ring-blue-500">
                    <div class="absolute left-2.5 top-2 text-slate-400 pointer-events-none">
                        <x-icon name="search" class="h-3.5 w-3.5" />
                    </div>
                </div>

                @if ($kelasList->isNotEmpty())
                    <select name="kelas" onchange="this.form.submit()" class="rounded-xl border-slate-300 py-1.5 text-xs focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelasList as $k)
                            <option value="{{ $k }}" @selected(request('kelas') === $k)>{{ $k }}</option>
                        @endforeach
                    </select>
                @endif

                <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition cursor-pointer">
                    Cari
                </button>

                @if (request()->hasAny(['q', 'kelas']))
                    <a href="{{ route('admin.rekap.show', ['eskul' => $model->name, 'periode' => $periode]) }}" class="px-2.5 py-1.5 text-xs text-rose-600 font-semibold hover:underline">
                        Reset Filter
                    </a>
                @endif
            </div>

            <div class="text-xs text-slate-500 self-center sm:self-auto">
                Menampilkan <b>{{ $siswa->total() }}</b> siswa
            </div>
        </form>

        {{-- Tabel Daftar Siswa --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-left font-bold text-slate-600 uppercase tracking-wider bg-slate-50/70">
                        <th class="py-2.5 px-3 w-12 text-center">No</th>
                        <th class="py-2.5 px-3">NIS</th>
                        <th class="py-2.5 px-3">Nama Lengkap Siswa</th>
                        <th class="py-2.5 px-3">Kelas</th>
                        <th class="py-2.5 px-3 hidden sm:table-cell">Jurusan</th>
                        <th class="py-2.5 px-3 text-center">Nilai Akhir</th>
                        <th class="py-2.5 px-3 hidden md:table-cell">Tgl Daftar</th>
                        <th class="py-2.5 px-3 text-right">Kontak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($siswa as $i => $r)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-2.5 px-3 text-center text-slate-400 font-mono">{{ ($siswa->firstItem() ?? 0) + $i }}</td>
                            <td class="py-2.5 px-3 font-mono font-bold text-slate-800">{{ $r->nis }}</td>
                            <td class="py-2.5 px-3">
                                <span class="font-semibold text-slate-900 block">{{ $r->user?->name ?? '—' }}</span>
                                @if ($r->nama_wali_kelas)
                                    <span class="text-[10px] text-slate-400 font-medium">Wali: {{ $r->nama_wali_kelas }}</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200/80 font-mono">
                                    {{ $r->kelas }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 hidden sm:table-cell text-slate-600 font-medium">{{ $r->jurusan?->short() ?? '-' }}</td>
                            <td class="py-2.5 px-3 text-center">
                                @if ($r->nilai !== null)
                                    <span class="inline-flex items-center gap-1 font-mono font-bold text-xs text-blue-900 bg-blue-50 px-2 py-0.5 rounded-lg border border-blue-200">
                                        <span>{{ $r->nilai }}</span>
                                        <span class="text-[10px] font-extrabold text-blue-600">({{ $r->predikat ?? '-' }})</span>
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">—</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 hidden md:table-cell text-slate-500 font-mono">{{ $r->created_at?->format('d/m/Y') }}</td>
                            <td class="py-2.5 px-3 text-right">
                                @php $kontak = $r->whatsapp ?: ($r->wali_kontak ?? ''); @endphp
                                @if ($kontak && \App\Support\Whatsapp::link($kontak))
                                    <a href="{{ \App\Support\Whatsapp::link($kontak) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 font-semibold text-emerald-700 hover:text-emerald-800 hover:underline">
                                        <x-icon name="phone" class="h-3 w-3 text-emerald-600" />
                                        <span>{{ $kontak }}</span>
                                    </a>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-slate-400">
                                <x-icon name="user" class="h-8 w-8 mx-auto text-slate-300 mb-2" />
                                <p class="font-semibold text-slate-600">Tidak ada data siswa terdaftar</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Tidak ditemukan pendaftaran pada eskul ini untuk periode {{ $periode }}.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 pt-3 border-t border-slate-100">
            {{ $siswa->links() }}
        </div>
    </div>
</div>
@endsection

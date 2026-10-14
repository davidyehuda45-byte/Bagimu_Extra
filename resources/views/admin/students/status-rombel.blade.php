@extends('layouts.staff')

@section('title', 'Cek Status Pendaftaran Per Rombel')

@section('content')
<div class="space-y-5">
    {{-- Top Header Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.students.index') }}" class="inline-flex h-8 w-8 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition" title="Kembali ke Direktori Siswa">
                    <x-icon name="arrow-left" class="h-4 w-4" />
                </a>
                <h1 class="text-xl font-bold text-slate-900">Status Pendaftaran Per Rombel</h1>
                @if ($periode)
                    <span class="inline-flex items-center rounded-xl bg-blue-50 border border-blue-200 px-2.5 py-1 text-xs font-bold text-blue-800">
                        Periode {{ $periode->periode }}
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-1">Monitoring rasio keikutsertaan pemilihan eskul per rombel secara visual (ala E-Voting).</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.students.export', ['format' => 'xlsx']) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-2xs transition">
                <x-icon name="file" class="h-3.5 w-3.5 text-emerald-600" />
                <span>Export Excel</span>
            </a>
            <a href="{{ route('admin.students.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-700 px-4 py-2.5 text-xs font-bold text-white hover:bg-blue-800 shadow-2xs transition">
                <x-icon name="users" class="h-3.5 w-3.5" />
                <span>Direktori Siswa</span>
            </a>
        </div>
    </div>

    {{-- Banner Ringkasan Keseluruhan --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs">
            <span class="text-[11px] font-bold uppercase text-slate-400">Total Rombel</span>
            <p class="mt-1 text-2xl font-black text-slate-900">{{ count($rombelStats) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs">
            <span class="text-[11px] font-bold uppercase text-slate-400">Total Siswa</span>
            <p class="mt-1 text-2xl font-black text-slate-900">{{ $totalSemuaSiswa }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/60 p-4 shadow-2xs">
            <span class="text-[11px] font-bold uppercase text-emerald-700">Sudah Mendaftar</span>
            <p class="mt-1 text-2xl font-black text-emerald-900">
                {{ $totalSemuaTerdaftar }}
                <span class="text-xs font-semibold text-emerald-700">({{ $totalSemuaSiswa > 0 ? round(($totalSemuaTerdaftar / $totalSemuaSiswa) * 100) : 0 }}%)</span>
            </p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50/60 p-4 shadow-2xs">
            <span class="text-[11px] font-bold uppercase text-amber-700">Belum Mendaftar</span>
            <p class="mt-1 text-2xl font-black text-amber-900">{{ max(0, $totalSemuaSiswa - $totalSemuaTerdaftar) }}</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('admin.students.status-rombel') }}" class="rounded-2xl border border-slate-200 bg-white p-4 flex flex-wrap items-center gap-3 shadow-2xs">
        <div class="min-w-[140px]">
            <label for="tingkat" class="block font-semibold mb-1 text-slate-700 text-xs">Tingkat</label>
            <select id="tingkat" name="tingkat" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 text-xs focus:border-blue-500 focus:ring-blue-500">
                <option value="">Semua Tingkat</option>
                @foreach ($tingkatList as $t)
                    <option value="{{ $t }}" @selected($tingkatFilter === $t)>Kelas {{ $t }}</option>
                @endforeach
            </select>
        </div>

        <div class="min-w-[160px]">
            <label for="jurusan" class="block font-semibold mb-1 text-slate-700 text-xs">Jurusan</label>
            <select id="jurusan" name="jurusan" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 text-xs focus:border-blue-500 focus:ring-blue-500">
                <option value="">Semua Jurusan</option>
                @foreach ($jurusanList as $j)
                    <option value="{{ $j->value }}" @selected($jurusanFilter === $j->value)>{{ $j->label() }}</option>
                @endforeach
            </select>
        </div>

        <div class="self-end pb-0.5">
            <a href="{{ route('admin.students.status-rombel') }}" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                Reset Filter
            </a>
        </div>
    </form>

    {{-- Grid Card Per Rombel (Ala E-Voting Cek Status Memilih) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse ($rombelStats as $stat)
            <div class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-xs hover:shadow-md transition-all">
                <div>
                    {{-- Judul Kelas & Badge --}}
                    <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2.5 mb-3">
                        <span class="font-mono font-bold text-sm text-slate-900 bg-slate-100 px-2.5 py-1 rounded-lg">
                            {{ $stat['kelas'] }}
                        </span>
                        @if ($stat['belum'] === 0 && $stat['total'] > 0)
                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                Selesai 100%
                            </span>
                        @elseif ($stat['persentase'] >= 50)
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                {{ $stat['persentase'] }}%
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                {{ $stat['persentase'] }}%
                            </span>
                        @endif
                    </div>

                    {{-- Rasio Besar & Subtitle --}}
                    <div class="mb-3">
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black text-slate-900">{{ $stat['terdaftar'] }}</span>
                            <span class="text-sm font-bold text-slate-400">/ {{ $stat['total'] }}</span>
                        </div>
                        <p class="text-xs mt-0.5 font-medium {{ $stat['belum'] > 0 ? 'text-amber-700' : 'text-emerald-700' }}">
                            {{ $stat['belum'] > 0 ? $stat['belum'] . ' siswa belum memilih' : 'Seluruh siswa telah terdaftar' }}
                        </p>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100 mb-4">
                        <div class="h-full rounded-full transition-all duration-300 {{ $stat['belum'] === 0 ? 'bg-emerald-500' : ($stat['persentase'] >= 50 ? 'bg-blue-600' : 'bg-amber-500') }}"
                             style="width: {{ $stat['persentase'] }}%"></div>
                    </div>
                </div>

                {{-- Action Footer --}}
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <a href="{{ route('admin.students.index', ['kelas' => $stat['kelas']]) }}" class="font-bold text-blue-700 hover:text-blue-900 hover:underline">
                        Lihat Siswa →
                    </a>
                    <a href="{{ route('admin.students.cetak-kartu-rombel', ['kelas' => $stat['kelas']]) }}" target="_blank" class="inline-flex items-center gap-1 font-semibold text-slate-600 hover:text-slate-900">
                        <x-icon name="printer" class="h-3 w-3" />
                        <span>Cetak Kartu</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-slate-200 bg-white p-12 text-center text-slate-400 shadow-2xs">
                <p class="font-semibold text-slate-700 text-sm">Tidak ada data rombel ditemukan.</p>
                <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter tingkat atau jurusan.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

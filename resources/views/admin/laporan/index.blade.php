@extends('layouts.staff')

@section('title', 'Laporan')

@section('content')
<div class="mb-4 flex flex-wrap items-center gap-3">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Laporan Rekap</h1>
        <p class="text-sm text-slate-500">Rekapitulasi pendaftar, kuota, nilai, dan kehadiran.</p>
    </div>
    <div class="ms-auto flex gap-2">
        <a href="{{ route('admin.laporan.export', request()->query()) }}" class="px-4 py-2 rounded-xl border border-slate-300 text-sm bg-white font-semibold">Ekspor CSV (filter ini)</a>
        <button onclick="window.print()" class="px-4 py-2 rounded-xl border border-slate-300 text-sm bg-white font-semibold">Cetak</button>
    </div>
</div>

<form method="GET" action="{{ route('admin.laporan.index') }}" class="mb-4 grid grid-cols-2 sm:grid-cols-6 gap-3 items-end rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-sm">
    <div>
        <label class="block text-xs font-medium text-slate-700">Periode</label>
        <select name="periode" class="mt-1 w-full border-slate-300 rounded-xl text-sm">
            @foreach ($periodes as $p)
                <option value="{{ $p->periode }}" @selected($periode === $p->periode)>{{ $p->periode }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-700">Jurusan</label>
        <select name="jurusan" class="mt-1 w-full border-slate-300 rounded-xl text-sm">
            <option value="">Semua</option>
            @foreach (\App\Enums\Jurusan::cases() as $j)
                <option value="{{ $j->value }}" @selected(request('jurusan') === $j->value)>{{ $j->short() }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-700">Kelas</label>
        <input type="text" name="kelas" value="{{ request('kelas') }}" class="mt-1 w-full border-slate-300 rounded-xl text-sm" placeholder="cth: 10 RPL">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-700">Eskul</label>
        <select name="eskul" class="mt-1 w-full border-slate-300 rounded-xl text-sm">
            <option value="">Semua</option>
            @foreach ($eskuls as $e)
                <option value="{{ $e->name }}" @selected(request('eskul') === $e->name)>{{ $e->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex gap-2">
        <button class="px-4 py-2 rounded-xl bg-blue-700 text-white text-sm font-bold hover:bg-blue-800">Tampilkan</button>
    </div>
</form>

{{-- Ringkasan --}}
<div class="grid grid-cols-2 gap-3 mb-4">
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="text-xs text-slate-500">Pendaftar</div>
        <div class="text-3xl font-extrabold text-slate-900">{{ $total }}</div>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="text-xs text-slate-500">Siswa tanpa eskul</div>
        <div class="text-3xl font-extrabold text-slate-700">{{ $tanpaEskul }}</div>
    </div>
</div>

<div class="grid gap-4 lg:grid-cols-2">
    {{-- Rekap nilai --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <h2 class="font-bold text-slate-900 text-sm mb-2">Rekap Nilai</h2>
        @if (empty($nilai))
            <p class="text-sm text-slate-500">Belum ada nilai.</p>
        @else
            <div class="flex flex-wrap gap-1.5">
                @foreach (['A' => 'bg-emerald-100 text-emerald-800', 'B' => 'bg-blue-100 text-blue-800', 'C' => 'bg-amber-100 text-amber-800', 'D' => 'bg-rose-100 text-rose-800'] as $p => $cls)
                    @if (!empty($nilai[$p]))
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $cls }}">{{ $p }}: {{ $nilai[$p] }}</span>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Kuota --}}
<div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm mt-4">
    <h2 class="font-bold text-slate-900 text-sm mb-3">Pemenuhan Kuota & Daftar Tunggu</h2>
    <div class="space-y-2.5">
        @foreach ($kuota as $row)
            @php($penuh = $row['eskul']->kuota_aktif && $row['count'] >= $row['eskul']->kuota)
            <div>
                <div class="flex justify-between text-sm">
                    <span class="font-medium text-slate-800">{{ $row['eskul']->name }}</span>
                    <span class="text-xs text-slate-500">{{ $row['eskul']->kuota_aktif ? $row['count'].'/'.$row['eskul']->kuota.($penuh ? ' — Penuh' : '') : $row['count'].' (bebas)' }}</span>
                </div>
                <div class="mt-1 h-2.5 overflow-hidden rounded-full bg-slate-100">
                    <div class="{{ $penuh ? 'bg-rose-500' : 'bg-blue-600' }} h-full rounded-full" style="width: {{ $row['eskul']->kuota_aktif ? min(100, round($row['count'] / max(1, $row['eskul']->kuota) * 100)) : min(100, $row['count'] * 10) }}%"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="grid gap-4 lg:grid-cols-2 mt-4">
    {{-- Per jurusan --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <h2 class="font-bold text-slate-900 text-sm mb-2">Per Jurusan</h2>
        <ul class="space-y-1 text-sm">
            @foreach (\App\Enums\Jurusan::cases() as $j)
                <li class="flex justify-between"><span class="text-slate-600">{{ $j->label() }}</span><b>{{ $perJurusan[$j->value] ?? 0 }}</b></li>
            @endforeach
        </ul>
    </div>

    {{-- Kehadiran --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <h2 class="font-bold text-slate-900 text-sm mb-2">Kehadiran Periode</h2>
        <div class="flex flex-wrap gap-1.5">
            @foreach (['HADIR' => 'bg-emerald-100 text-emerald-800', 'IZIN' => 'bg-amber-100 text-amber-800', 'SAKIT' => 'bg-amber-100 text-amber-800', 'ALPA' => 'bg-rose-100 text-rose-800'] as $val => $cls)
                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $cls }}">{{ $val }}: {{ $kehadiran[$val] ?? 0 }}</span>
            @endforeach
        </div>
    </div>
</div>
@endsection

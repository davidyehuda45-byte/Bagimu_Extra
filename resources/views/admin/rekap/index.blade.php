@extends('layouts.staff')

@section('title', 'Rekap Eskul')

@section('content')
<h1 class="text-xl font-bold text-slate-900 mb-1">Rekap Per Eskul</h1>
<p class="text-sm text-slate-500 mb-4">Periode berjalan{{ $periode ? ": $periode" : '' }}.</p>

<div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($rekap as $row)
        @php($e = $row['eskul'])
        @php($penuh = $e->kuota_aktif && $row['count'] >= $e->kuota)
        <a href="{{ route('admin.rekap.show', ['eskul' => $e->name]) }}" class="block rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:bg-slate-50">
            <div class="flex items-center gap-2">
                <p class="font-bold text-slate-900">{{ $e->name }}</p>
                @if (! $e->aktif)
                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-500">Nonaktif</span>
                @endif
            </div>
            <p class="mt-1 text-sm text-slate-600"><b class="text-slate-900">{{ $row['count'] }}</b> siswa terdaftar</p>
            <p class="text-xs text-slate-500">Kuota: {{ $e->kuota_aktif ? $row['count'].'/'.$e->kuota : $row['count'].' • Bebas' }}</p>
            <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                <div class="{{ $penuh ? 'bg-rose-500' : 'bg-blue-600' }} h-full rounded-full" style="width: {{ $e->kuota_aktif ? min(100, round($row['count'] / max(1, $e->kuota) * 100)) : min(100, $row['count'] * 10) }}%"></div>
            </div>
            @if ($penuh)
                <p class="mt-1 text-xs font-semibold text-rose-600">Kuota penuh — pendaftaran ditutup</p>
            @endif
            <p class="mt-2 text-xs font-medium text-blue-600">Lihat Siswa →</p>
        </a>
    @empty
        <p class="text-sm text-slate-500">Belum ada eskul.</p>
    @endforelse
</div>
@endsection

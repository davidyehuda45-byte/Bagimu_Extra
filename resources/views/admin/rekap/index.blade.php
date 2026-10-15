@extends('layouts.staff')

@section('title', 'Rekap Eskul')

@section('content')
<h1 class="text-xl font-bold text-slate-900 mb-1">Rekap Per Eskul</h1>
<p class="text-sm text-slate-500 mb-4">Periode berjalan{{ $periode ? ": $periode" : '' }}.</p>

<div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($rekap as $row)
        @php($e = $row['eskul'])
        @php($penuh = $e->kuota_aktif && $row['count'] >= $e->kuota)
        @php($pct = $e->kuota_aktif ? min(100, round($row['count'] / max(1, $e->kuota) * 100)) : min(100, $row['count'] * 10))
        <a href="{{ route('admin.rekap.show', ['eskul' => $e->name]) }}" class="block rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
            <div class="flex items-center gap-2">
                <p class="font-bold text-slate-900">{{ $e->name }}</p>
                @if (! $e->aktif)
                    <x-badge tone="slate" class="!text-[10px]">Nonaktif</x-badge>
                @endif
            </div>
            <p class="mt-1 text-sm text-slate-600"><b class="text-slate-900">{{ $row['count'] }}</b> siswa terdaftar</p>
            <p class="text-xs text-slate-500">Kuota: {{ $e->kuota_aktif ? $row['count'].'/'.$e->kuota : $row['count'].' • Bebas' }}</p>
            <x-progress :value="$pct" tone="{{ $penuh ? 'rose' : 'blue' }}" class="mt-2 !h-2" aria-label="Kuota {{ $e->name }} {{ $pct }} persen" />
            @if ($penuh)
                <p class="mt-1 text-xs font-semibold text-rose-600">Kuota penuh — pendaftaran ditutup</p>
            @endif
            <p class="mt-2 text-xs font-medium text-blue-600">Lihat Siswa →</p>
        </a>
    @empty
        <div class="sm:col-span-2 lg:col-span-3">
            <x-card><x-empty-state icon="trophy" title="Belum ada eskul" message="Data rekap akan muncul setelah eskul dibuat." /></x-card>
        </div>
    @endforelse
</div>
@endsection

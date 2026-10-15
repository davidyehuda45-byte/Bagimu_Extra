@extends('layouts.staff')

@section('title', 'Dashboard Admin')

@section('content')
<h1 class="text-xl font-bold text-slate-900 mb-4">Dashboard Admin TU</h1>

{{-- Widget --}}
<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-4">
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="text-xs text-slate-500">Total Pendaftar{{ $periode ? " ($periode)" : '' }}</div>
        <div class="text-3xl font-extrabold text-slate-900">{{ $total }}</div>
        <a href="{{ route('admin.registrations.index', ['periode' => $periode]) }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">Kelola Pendaftar →</a>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="text-xs text-slate-500">Eskul Aktif</div>
        <div class="text-3xl font-extrabold text-blue-600">{{ $distEskul->where('eskul.aktif', true)->count() }}</div>
        <a href="{{ route('admin.eskuls.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">Kelola eskul →</a>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="text-xs text-slate-500">Siswa Aktif</div>
        <div class="text-3xl font-extrabold text-emerald-600">{{ \App\Models\Student::where('status', 'AKTIF')->count() }}</div>
        <a href="{{ route('admin.students.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">Lihat data siswa →</a>
    </div>
</div>

{{-- Pusat tugas --}}
@if (count($tugas) > 0)
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 mb-4">
        <h2 class="font-bold text-amber-800 text-sm">Pusat Tugas ({{ count($tugas) }})</h2>
        <ul class="mt-2 space-y-1 text-sm">
            @foreach ($tugas as $t)
                <li>
                    @if ($t['url'])
                        <a href="{{ $t['url'] }}" class="text-blue-700 hover:underline">• {{ $t['label'] }}</a>
                    @else
                        <span class="text-amber-800">• {{ $t['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid gap-4 lg:grid-cols-2">
    {{-- Distribusi per eskul --}}
    <x-card>
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-bold text-slate-900 text-sm">Distribusi Per Eskul</h2>
            <a href="{{ route('admin.rekap.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">Lihat rekap lengkap →</a>
        </div>
        @php
            $chartEskul = $distEskul->map(function ($row) {
                $penuh = $row['eskul']->kuota_aktif && $row['count'] >= $row['eskul']->kuota;
                $pct = $row['eskul']->kuota_aktif
                    ? min(100, round($row['count'] / max(1, $row['eskul']->kuota) * 100))
                    : $row['count'];
                return [
                    'label' => $row['eskul']->name.(!$row['eskul']->aktif ? ' (Nonaktif)' : '').($penuh ? ' (Penuh)' : ''),
                    'value' => $pct,
                    'display' => $row['count'].($row['eskul']->kuota_aktif ? '/'.$row['eskul']->kuota : ''),
                    'tone' => $penuh ? 'rose' : 'blue',
                ];
            })->all();
        @endphp
        <x-bar-chart :items="$chartEskul" :max="100" />
    </x-card>

    <div class="space-y-4">
        {{-- Distribusi per jurusan --}}
        <x-card>
            <h2 class="font-bold text-slate-900 text-sm mb-3">Distribusi Per Jurusan</h2>
            @php
                $chartJurusan = collect($distJurusan)->map(fn ($j) => [
                    'label' => $j['label'],
                    'value' => $j['count'],
                    'display' => $j['count'],
                    'tone' => 'blue',
                ])->all();
            @endphp
            <x-bar-chart :items="$chartJurusan" :max="$maxJurusan" />
        </x-card>

        {{-- Pendaftar terbaru --}}
        <x-card>
            <div class="flex items-center justify-between mb-2">
                <h2 class="font-bold text-slate-900 text-sm">Pendaftar Terbaru</h2>
                <a href="{{ route('admin.registrations.index', ['periode' => $periode]) }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">Kelola Pendaftar →</a>
            </div>
            <ul class="divide-y divide-slate-100 text-sm">
                @forelse ($terbaru as $r)
                    <li class="flex items-center gap-2 py-2">
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-slate-900">{{ $r->user?->name ?? '—' }}</p>
                            <p class="font-mono text-[11px] text-slate-400">{{ $r->kode() }} • {{ $r->created_at?->format('d M Y H:i') }}</p>
                        </div>
                        <span class="shrink-0 text-xs text-slate-500">{{ $r->eskul_utama }}</span>
                        <x-badge tone="{{ $r->status->value === 'APPROVED' ? 'emerald' : ($r->status->value === 'PENDING' ? 'amber' : 'rose') }}" class="!text-[11px]">{{ $r->status->label() }}</x-badge>
                    </li>
                @empty
                    <li><x-empty-state title="Belum ada pendaftar" message="Pendaftar terbaru akan muncul di sini." /></li>
                @endforelse
            </ul>
        </x-card>
    </div>
</div>
@endsection

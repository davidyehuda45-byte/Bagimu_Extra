@extends('layouts.staff')

@section('title', 'Dashboard Admin')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Dashboard Admin & Kesiswaan</h1>
        <p class="text-xs text-slate-500 mt-0.5">Ringkasan statistik pendaftaran eskul dan pemantauan aktivitas siswa.</p>
    </div>
    @if ($periode)
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 border border-blue-200/70 text-xs font-bold text-blue-700 shadow-2xs self-start sm:self-auto">
            <x-icon name="calendar" class="h-3.5 w-3.5 text-blue-600" />
            Tahun Ajaran {{ $periode }}
        </span>
    @endif
</div>

{{-- Metric Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs hover:border-slate-300 transition-all">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500">Total Pendaftar{{ $periode ? " ($periode)" : '' }}</span>
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 border border-blue-100">
                <x-icon name="clipboard" class="h-5 w-5" />
            </span>
        </div>
        <div class="mt-3 text-3xl font-black text-slate-900 tracking-tight">{{ $total }}</div>
        <div class="mt-3 pt-3 border-t border-slate-100">
            <a href="{{ route('admin.registrations.index', ['periode' => $periode]) }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-700">
                <span>Kelola Data Pendaftar</span>
                <span class="text-sm font-semibold">&rarr;</span>
            </a>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs hover:border-slate-300 transition-all">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500">Ekstrakurikuler Aktif</span>
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-50 text-blue-700 border border-blue-100">
                <x-icon name="trophy" class="h-5 w-5" />
            </span>
        </div>
        <div class="mt-3 text-3xl font-black text-slate-900 tracking-tight">{{ $distEskul->where('eskul.aktif', true)->count() }}</div>
        <div class="mt-3 pt-3 border-t border-slate-100">
            <a href="{{ route('admin.eskuls.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-700">
                <span>Kelola Master Eskul</span>
                <span class="text-sm font-semibold">&rarr;</span>
            </a>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs hover:border-slate-300 transition-all sm:col-span-2 lg:col-span-1">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500">Siswa Aktif Terdaftar</span>
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-50 text-blue-700 border border-blue-100">
                <x-icon name="users" class="h-5 w-5" />
            </span>
        </div>
        <div class="mt-3 text-3xl font-black text-slate-900 tracking-tight">{{ \App\Models\Student::where('status', 'AKTIF')->count() }}</div>
        <div class="mt-3 pt-3 border-t border-slate-100">
            <a href="{{ route('admin.students.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-700">
                <span>Buka Direktori Siswa</span>
                <span class="text-sm font-semibold">&rarr;</span>
            </a>
        </div>
    </div>
</div>

{{-- Pusat tugas interaktif --}}
@if (count($tugas) > 0)
    <div class="rounded-3xl border border-amber-200/80 bg-gradient-to-br from-amber-50/80 via-white to-amber-50/40 p-5 mb-6 shadow-xs">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-amber-100 text-amber-800">
                    <x-icon name="alert" class="h-4 w-4" />
                </span>
                <h2 class="font-bold text-slate-900 text-sm">Pusat Tindakan & Tugas</h2>
            </div>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                {{ count($tugas) }} Butuh Perhatian
            </span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
            @foreach ($tugas as $t)
                @if ($t['url'])
                    <a href="{{ $t['url'] }}" class="flex items-center justify-between p-3 rounded-2xl bg-white border border-amber-200/70 hover:border-amber-300 hover:shadow-2xs text-xs font-semibold text-slate-800 transition-all group">
                        <span class="truncate mr-2">{{ $t['label'] }}</span>
                        <x-icon name="arrow-right" class="h-3.5 w-3.5 text-amber-600 group-hover:translate-x-0.5 transition-transform shrink-0" />
                    </a>
                @else
                    <div class="flex items-center p-3 rounded-2xl bg-white border border-amber-200/50 text-xs font-medium text-slate-600">
                        <span class="truncate">{{ $t['label'] }}</span>
                    </div>
                @endif
            @endforeach
        </div>
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

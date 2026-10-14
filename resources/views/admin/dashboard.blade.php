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
        <a href="{{ route('admin.rekap.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">Lihat rekap →</a>
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
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-bold text-slate-900 text-sm">Distribusi Per Eskul</h2>
            <a href="{{ route('admin.rekap.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">Lihat rekap lengkap →</a>
        </div>
        <div class="space-y-2.5">
            @forelse ($distEskul as $row)
                @php($penuh = $row['eskul']->kuota_aktif && $row['count'] >= $row['eskul']->kuota)
                <div>
                    <div class="flex items-center justify-between gap-2 text-sm">
                        <span class="font-medium text-slate-800 truncate">
                            {{ $row['eskul']->name }}
                            @if (! $row['eskul']->aktif)
                                <span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-500">Nonaktif</span>
                            @endif
                            @if ($penuh)
                                <span class="ml-1 rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-semibold text-rose-700">Penuh</span>
                            @endif
                        </span>
                        <span class="shrink-0 text-xs text-slate-500">{{ $row['count'] }}{{ $row['eskul']->kuota_aktif ? '/'.$row['eskul']->kuota : '' }}</span>
                    </div>
                    <div class="mt-1 h-2.5 overflow-hidden rounded-full bg-slate-100">
                        <div class="{{ $penuh ? 'bg-rose-500' : 'bg-blue-600' }} h-full rounded-full" style="width: {{ $row['eskul']->kuota_aktif ? min(100, round($row['count'] / max(1, $row['eskul']->kuota) * 100)) : min(100, round($row['count'] / $maxEskul * 100)) }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">Belum ada eskul.</p>
            @endforelse
        </div>
    </div>

    <div class="space-y-4">
        {{-- Distribusi per jurusan --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <h2 class="font-bold text-slate-900 text-sm mb-3">Distribusi Per Jurusan</h2>
            <div class="space-y-2">
                @foreach ($distJurusan as $j)
                    <div>
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-slate-800">{{ $j['label'] }}</span>
                            <span class="text-xs text-slate-500">{{ $j['count'] }}</span>
                        </div>
                        <div class="mt-1 h-2.5 overflow-hidden rounded-full bg-slate-100">
                            <div class="bg-blue-600 h-full rounded-full" style="width: {{ round($j['count'] / $maxJurusan * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Pendaftar terbaru --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
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
                        <span class="shrink-0 inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $r->status->value === 'APPROVED' ? 'bg-emerald-100 text-emerald-800' : ($r->status->value === 'PENDING' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">{{ $r->status->label() }}</span>
                    </li>
                @empty
                    <li class="py-3 text-center text-sm text-slate-500">Belum ada pendaftar.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

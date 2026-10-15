@extends('layouts.staff')

@section('title', 'Per Jurusan & Kelas')

@section('content')
<h1 class="text-xl font-bold text-slate-900 mb-4">Rekap Per Jurusan & Kelas</h1>

<form method="GET" action="{{ route('admin.kelas.index') }}" class="mb-4 flex flex-wrap gap-3 items-end rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <div>
        <label for="periode" class="block text-xs font-medium text-slate-700">Periode</label>
        <select name="periode" id="periode" class="mt-1 border-slate-300 rounded-xl px-3 py-2 text-sm" onchange="this.form.submit()">
            @foreach ($periodes as $p)
                <option value="{{ $p->periode }}" @selected($periode === $p->periode)>{{ $p->periode }}</option>
            @endforeach
        </select>
    </div>
    <span class="text-xs text-slate-400">Total pendaftar: {{ $rincian->sum('total') }}</span>
</form>

{{-- Matriks --}}
<div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm mb-4">
    <h2 class="font-bold text-slate-900 text-sm mb-2">Ringkasan Kelas × Jurusan</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b text-left text-slate-500">
                    <th class="py-2 px-3">Kelas</th>
                    @foreach ($jurusans as $j)
                        <th class="py-2 px-3">{{ $j->short() }}</th>
                    @endforeach
                    <th class="py-2 px-3">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kelasList as $k)
                    <tr class="border-b hover:bg-slate-50">
                        <td class="py-2 px-3 font-semibold">{{ $k }}</td>
                        @foreach ($jurusans as $j)
                            <td class="py-2 px-3">
                                @if (($matrix[$k][$j->value] ?? 0) > 0)
                                    <span class="inline-block rounded-lg bg-blue-100 px-2 py-0.5 font-bold text-blue-800">{{ $matrix[$k][$j->value] }}</span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                        @endforeach
                        <td class="py-2 px-3 font-bold">{{ array_sum($matrix[$k]) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($jurusans) + 2 }}" class="py-6 text-center text-slate-500">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Rincian --}}
@foreach ($rincian as $row)
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm mb-4">
        <h2 class="font-bold text-slate-900 text-sm">{{ $row['jurusan']->label() }}
            <span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600">{{ $row['total'] }}</span>
        </h2>
        @forelse ($row['perKelas'] as $kelas => $list)
            <h3 class="mt-3 mb-1 text-xs font-bold uppercase tracking-wider text-slate-400">{{ $kelas }} ({{ $list->count() }})</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-slate-500">
                            <th class="py-1.5 px-3">No</th>
                            <th class="py-1.5 px-3">Nama</th>
                            <th class="py-1.5 px-3 hidden sm:table-cell">Email</th>
                            <th class="py-1.5 px-3">Eskul</th>
                            <th class="py-1.5 px-3">WA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list->sortBy(fn ($r) => $r->user?->name) as $i => $r)
                            <tr class="border-b hover:bg-slate-50">
                                <td class="py-1.5 px-3">{{ $i + 1 }}</td>
                                <td class="py-1.5 px-3 font-medium">{{ $r->user?->name ?? '—' }}</td>
                                <td class="py-1.5 px-3 hidden sm:table-cell text-slate-500">{{ $r->user?->email }}</td>
                                <td class="py-1.5 px-3">{{ $r->eskul_utama }}</td>
                                <td class="py-1.5 px-3">
                                    @if ($r->whatsapp && \App\Support\Whatsapp::link($r->whatsapp))
                                        <a href="{{ \App\Support\Whatsapp::link($r->whatsapp) }}" target="_blank" rel="noopener" class="font-medium text-emerald-600 hover:text-emerald-700">WA</a>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <p class="mt-1 text-sm text-slate-500">Belum ada pendaftar jurusan ini.</p>
        @endforelse
    </div>
@endforeach
@endsection

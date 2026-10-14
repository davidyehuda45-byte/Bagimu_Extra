@extends('layouts.staff')

@section('title', 'Cetak Peserta')

@section('content')
<div class="no-print mb-4 flex flex-wrap items-center gap-2">
    <h1 class="text-xl font-bold text-slate-900">Cetak Peserta</h1>
    <div class="ms-auto flex gap-2">
        @foreach ($periodes as $p)
            <a href="{{ route('admin.cetak.index', ['periode' => $p]) }}" class="rounded-xl px-3 py-1.5 text-sm font-medium {{ $periode === $p ? 'bg-blue-700 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">{{ $p }}</a>
        @endforeach
        <button onclick="window.print()" class="rounded-xl bg-blue-700 px-4 py-2 text-sm font-bold text-white hover:bg-blue-800">Cetak {{ $peserta->count() }} Peserta</button>
    </div>
</div>

<div class="print-area grid gap-3 sm:grid-cols-2 print:grid-cols-1">
    @forelse ($peserta as $i => $r)
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-start gap-3">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-sm font-bold text-slate-600">{{ $i + 1 }}</span>
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-slate-900">{{ $r->user?->name ?? '—' }}</p>
                    <p class="text-xs text-slate-500">{{ $r->kelas }} • {{ $r->jurusan?->short() ?? '—' }}</p>
                    <p class="mt-0.5 text-sm font-semibold text-blue-700">{{ $r->eskul_utama }}</p>
                    <p class="mt-0.5 font-mono text-[11px] text-slate-400">{{ $r->kode() }} • Terdaftar</p>
                </div>
            </div>
        </div>
    @empty
        <p class="text-sm text-slate-500">Belum ada peserta disetujui.</p>
    @endforelse
</div>
@endsection

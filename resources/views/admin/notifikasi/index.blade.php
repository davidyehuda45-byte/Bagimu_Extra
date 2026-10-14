@extends('layouts.staff')

@section('title', 'Pusat Notifikasi')

@section('content')
<h1 class="text-xl font-bold text-slate-900 mb-4">Pusat Notifikasi</h1>

<div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm mb-4">
    <h2 class="font-bold text-slate-900 text-sm mb-2">Template Terdaftar</h2>
    <div class="flex flex-wrap gap-1.5">
        @foreach ($templates as $t)
            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-500/20">{{ $t }}</span>
        @endforeach
    </div>
</div>

<div class="flex flex-wrap gap-2 mb-4 text-sm">
    <a href="{{ route('admin.notifikasi.index') }}" class="rounded-xl px-3 py-1.5 font-medium {{ $status === '' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Semua</a>
    @foreach ($statusList as $s)
        <a href="{{ route('admin.notifikasi.index', ['status' => $s->value]) }}" class="rounded-xl px-3 py-1.5 font-medium {{ $status === $s->value ? 'bg-blue-700 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">{{ $s->label() }} ({{ $counts[$s->value] ?? 0 }})</a>
    @endforeach
</div>

<div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <div class="space-y-2">
        @forelse ($logs as $log)
            <div class="rounded-xl border border-slate-100 p-3 text-sm">
                <div class="flex flex-wrap items-center gap-1.5">
                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $log->status->value === 'TERKIRIM' ? 'bg-emerald-100 text-emerald-800' : ($log->status->value === 'GAGAL' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">{{ $log->status->label() }}</span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600">{{ $log->channel->label() }}</span>
                    <span class="font-mono text-xs text-slate-700">{{ $log->template }}</span>
                    <span class="ms-auto text-[11px] text-slate-400">→ {{ $log->to }} • {{ $log->attempts }}x • {{ $log->created_at?->format('d/m/Y H:i') }}</span>
                    @if ($log->status->value !== 'TERKIRIM')
                        <form method="POST" action="{{ route('admin.notifikasi.retry', $log) }}">
                            @csrf
                            <button class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs font-semibold hover:bg-slate-50">Kirim Ulang</button>
                        </form>
                    @endif
                </div>
                @if ($log->provider_resp)
                    <p class="mt-1 text-xs text-slate-500">Respons: {{ $log->provider_resp }}</p>
                @endif
            </div>
        @empty
            <p class="py-4 text-center text-sm text-slate-500">Belum ada log notifikasi.</p>
        @endforelse
    </div>
    <div class="mt-4">{{ $logs->links() }}</div>
</div>
@endsection

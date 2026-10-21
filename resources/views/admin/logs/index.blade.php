@extends('layouts.staff')

@section('title', 'Riwayat Aktivitas & Log Sistem')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900 mb-1">Riwayat Aktivitas Sistem</h1>
        <p class="text-sm text-slate-500">{{ $logs->total() }} catatan audit — filter aksi: {{ $aksi !== '' ? $aksi : 'semua' }}{{ $q !== '' ? ' | cari: "'.$q.'"' : '' }}</p>
    </div>
    <form method="GET" action="{{ route('admin.logs.index') }}" class="flex items-center gap-2">
        @if ($aksi !== '')
            <input type="hidden" name="aksi" value="{{ $aksi }}">
        @endif
        <input type="text" name="q" value="{{ $q }}" placeholder="Cari email, IP, aksi, detail..." class="rounded-xl border border-slate-200 px-3 py-1.5 text-sm text-slate-800 placeholder-slate-400 focus:border-blue-600 focus:outline-none focus:ring-1 focus:ring-blue-600">
        <button type="submit" class="rounded-xl bg-blue-700 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-blue-800 transition">Cari</button>
        @if ($q !== '' || $aksi !== '')
            <a href="{{ route('admin.logs.index') }}" class="rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50">Reset</a>
        @endif
    </form>
</div>

<div class="flex flex-wrap gap-2 mb-4 text-xs sm:text-sm">
    <a href="{{ route('admin.logs.index', array_filter(['q' => $q])) }}" class="rounded-xl px-3 py-1.5 font-medium {{ $aksi === '' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Semua</a>
    @foreach ($chipActions->take(8) as $a)
        <a href="{{ route('admin.logs.index', array_filter(['aksi' => $a, 'q' => $q])) }}" class="rounded-xl px-3 py-1.5 font-medium {{ $aksi === $a ? 'bg-blue-700 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">{{ ucwords(str_replace(['.', '_'], ' ', $a)) }}</a>
    @endforeach
</div>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2.5 text-left font-semibold text-slate-600 text-xs">Waktu</th>
                    <th class="px-4 py-2.5 text-left font-semibold text-slate-600 text-xs">Pelaku / Role</th>
                    <th class="px-4 py-2.5 text-left font-semibold text-slate-600 text-xs">IP Address</th>
                    <th class="px-4 py-2.5 text-left font-semibold text-slate-600 text-xs">Aksi</th>
                    <th class="px-4 py-2.5 text-left font-semibold text-slate-600 text-xs">Status</th>
                    <th class="px-4 py-2.5 text-left font-semibold text-slate-600 text-xs">Detail & Target</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($logs as $log)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2.5 text-slate-500 whitespace-nowrap text-xs font-mono">
                            {{ $log->created_at?->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-4 py-2.5">
                            <p class="font-medium text-slate-800 text-xs sm:text-sm">{{ $log->admin_email }}</p>
                            <span class="inline-block mt-0.5 rounded px-1.5 py-0.2 bg-slate-100 text-[10px] font-bold text-slate-600">
                                {{ $log->actor_role ?? 'USER' }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-xs font-mono text-slate-600 whitespace-nowrap">
                            {{ $log->ip_address ?? '127.0.0.1' }}
                        </td>
                        <td class="px-4 py-2.5">
                            <span class="inline-flex items-center rounded-lg bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700">
                                {{ ucwords(str_replace(['.', '_'], ' ', $log->action)) }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            @if ($log->status === 'FAILED')
                                <span class="inline-flex items-center rounded-full bg-rose-50 px-2 py-0.5 text-[11px] font-bold text-rose-700">
                                    ✕ Gagal
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-700">
                                    ✓ Berhasil
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-slate-600 text-xs max-w-sm">
                            <p class="truncate" title="{{ $log->detail }}">{{ $log->detail ?? '-' }}</p>
                            @if ($log->target_type)
                                <p class="text-[10px] text-slate-400 mt-0.5">Target: {{ $log->target_type }} #{{ $log->target_id ?? '-' }}</p>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center">
                            <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                <x-icon name="history" class="h-5 w-5" />
                            </div>
                            <p class="text-sm font-semibold text-slate-700">Belum ada catatan aktivitas</p>
                            <p class="text-xs text-slate-400 mt-0.5">Tidak ditemukan log yang sesuai dengan filter atau kata kunci pencarian.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-100">{{ $logs->links() }}</div>
</div>
@endsection

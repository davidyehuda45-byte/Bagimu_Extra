@extends('layouts.staff')

@section('title', 'Riwayat Aktivitas')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900 mb-1">Riwayat Aktivitas</h1>
        <p class="text-sm text-slate-500">{{ $logs->total() }} catatan — filter aksi: {{ $aksi !== '' ? $aksi : 'semua' }}{{ $q !== '' ? ' | cari: "'.$q.'"' : '' }}</p>
    </div>
    <form method="GET" action="{{ route('admin.logs.index') }}" class="flex items-center gap-2">
        @if ($aksi !== '')
            <input type="hidden" name="aksi" value="{{ $aksi }}">
        @endif
        <input type="text" name="q" value="{{ $q }}" placeholder="Cari email, target, detail..." class="rounded-xl border border-slate-200 px-3 py-1.5 text-sm text-slate-800 placeholder-slate-400 focus:border-blue-600 focus:outline-none focus:ring-1 focus:ring-blue-600">
        <button type="submit" class="rounded-xl bg-blue-700 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-blue-800 transition">Cari</button>
        @if ($q !== '' || $aksi !== '')
            <a href="{{ route('admin.logs.index') }}" class="rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50">Reset</a>
        @endif
    </form>
</div>

<div class="flex flex-wrap gap-2 mb-4 text-sm">
    <a href="{{ route('admin.logs.index', array_filter(['q' => $q])) }}" class="rounded-xl px-3 py-1.5 font-medium {{ $aksi === '' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Semua</a>
    @foreach ($chipActions as $a)
        <a href="{{ route('admin.logs.index', array_filter(['aksi' => $a, 'q' => $q])) }}" class="rounded-xl px-3 py-1.5 font-medium {{ $aksi === $a ? 'bg-blue-700 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">{{ ucwords(str_replace(['.', '_'], ' ', $a)) }}</a>
    @endforeach
</div>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-slate-500">Waktu</th>
                    <th class="px-4 py-2 text-left font-medium text-slate-500">Admin</th>
                    <th class="px-4 py-2 text-left font-medium text-slate-500">Aksi</th>
                    <th class="px-4 py-2 text-left font-medium text-slate-500">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($logs as $log)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 text-slate-500 whitespace-nowrap">{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-2">
                            <p class="font-medium text-slate-800">{{ $log->admin_email }}</p>
                            @if ($log->target_type)
                                <p class="text-[11px] text-slate-400">{{ $log->target_type }} #{{ $log->target_id ?? '-' }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-2"><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700">{{ ucwords(str_replace(['.', '_'], ' ', $log->action)) }}</span></td>
                        <td class="px-4 py-2 text-slate-600 max-w-md truncate hidden md:table-cell" title="{{ $log->detail }}">{{ $log->detail ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center">
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
    <div class="p-4">{{ $logs->links() }}</div>
</div>
@endsection

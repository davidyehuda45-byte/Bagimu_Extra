@extends('layouts.staff')

@section('title', 'Perizinan')

@section('content')
<div class="mb-4">
    <h1 class="text-xl font-bold">Perizinan</h1>
    <p class="text-sm text-gray-600">Tinjau dan putuskan pengajuan izin siswa.</p>
</div>

<form method="GET" action="{{ route('admin.izin.index') }}" class="mb-4 flex flex-wrap gap-3 items-end bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
    <div class="w-full sm:w-auto">
        <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">Status Izin</label>
        <select name="status" id="status" class="w-full sm:w-auto min-w-[180px] max-w-full rounded-xl border-slate-300 px-3.5 py-2 pr-10 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 truncate">
            <option value="">Semua Status</option>
            @foreach ($statusList as $st)
                <option value="{{ $st->value }}" @selected($filterStatus === $st->value)>{{ $st->label() }}</option>
            @endforeach
        </select>
    </div>
    <div class="w-full sm:w-auto">
        <label for="eskul_id" class="block text-xs font-semibold text-slate-700 mb-1">Ekstrakurikuler</label>
        <select name="eskul_id" id="eskul_id" class="w-full sm:w-auto min-w-[240px] max-w-full rounded-xl border-slate-300 px-3.5 py-2 pr-10 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 truncate">
            <option value="">Semua Ekstrakurikuler</option>
            @foreach ($eskuls as $e)
                <option value="{{ $e->id }}" @selected((string) $filterEskul === (string) $e->id)>{{ $e->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex items-center gap-2">
        <button class="px-4 py-2 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-semibold text-sm transition-colors">Saring</button>
        <a href="{{ route('admin.izin.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm transition-colors">Reset</a>
    </div>
</form>

<div class="bg-white border rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium">Siswa</th>
                    <th class="px-4 py-2 text-left font-medium">Eskul</th>
                    <th class="px-4 py-2 text-left font-medium">Tanggal / Jenis</th>
                    <th class="px-4 py-2 text-left font-medium">Alasan</th>
                    <th class="px-4 py-2 text-left font-medium">Status</th>
                    <th class="px-4 py-2 text-left font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($izin as $row)
                    <tr>
                        <td class="px-4 py-2">{{ $row->registration?->user?->name ?? '-' }}<div class="text-xs text-gray-500">{{ $row->registration?->nis ?? '' }}</div></td>
                        <td class="px-4 py-2">{{ $row->eskul?->name ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $row->tanggal?->format('Y-m-d') }}<div class="text-xs text-gray-500">{{ $row->jenis?->label() ?? $row->jenis }}</div></td>
                        <td class="px-4 py-2 max-w-xs">
                            {{ $row->alasan }}
                            @if ($row->bukti)
                                <div class="mt-1"><a href="{{ asset('storage/' . $row->bukti) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:underline font-semibold"><x-icon name="file" class="h-3.5 w-3.5" /> Lihat Bukti</a></div>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <span class="inline-block px-2 py-1 rounded text-xs border">{{ $row->status?->label() ?? $row->status }}</span>
                        </td>
                        <td class="px-4 py-2">
                            @if (($row->status?->value ?? $row->status) === 'PENDING' && !auth()->user()->hasRole(\App\Enums\Role::KEPALA_SEKOLAH))
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('admin.izin.approve', $row) }}">
                                        @csrf
                                        <button class="px-3 py-1 rounded bg-green-700 text-white text-xs">Setujui</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.izin.reject', $row) }}">
                                        @csrf
                                        <button class="px-3 py-1 rounded bg-red-700 text-white text-xs">Tolak</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-xs text-gray-500">Diputuskan oleh {{ $row->decided_by ?? '-' }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center">
                            <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                <x-icon name="inbox" class="h-5 w-5" />
                            </div>
                            <p class="text-sm font-semibold text-slate-700">Tidak ada pengajuan izin</p>
                            <p class="text-xs text-slate-400 mt-0.5">Belum ada permohonan izin yang sesuai dengan filter.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $izin->links() }}</div>
</div>
@endsection

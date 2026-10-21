@extends('layouts.staff')

@section('title', 'Data Eskul')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-bold">Data Eskul</h1>
    <a href="{{ route('admin.eskuls.create') }}" class="px-4 py-2 rounded bg-blue-600 text-white text-sm hover:bg-blue-700">Tambah Eskul</a>
</div>

<div class="bg-white rounded-lg shadow p-4">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b text-left text-gray-500">
                    <th class="py-2 px-3">Nama</th>
                    <th class="py-2 px-3">Pembina</th>
                    <th class="py-2 px-3">Kuota</th>
                    <th class="py-2 px-3">Jadwal</th>
                    <th class="py-2 px-3">Status</th>
                    <th class="py-2 px-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($eskuls as $eskul)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-3 font-medium">{{ $eskul->name }}</td>
                        <td class="py-2 px-3">{{ $eskul->pembina?->name ?? '-' }}</td>
                        <td class="py-2 px-3">{{ $eskul->kuota_aktif ? $eskul->kuota : 'Tanpa batas' }}</td>
                        <td class="py-2 px-3">
                            <a href="{{ route('admin.eskuls.show', $eskul) }}#jadwal" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition" title="Lihat dan kelola jadwal eskul ini">
                                <x-icon name="clock" class="h-3.5 w-3.5" />
                                {{ $eskul->jadwals->count() }} Jadwal
                            </a>
                        </td>
                        <td class="py-2 px-3">
                            @if ($eskul->aktif)
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Aktif</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">Nonaktif</span>
                            @endif
                        </td>
                        <td class="py-2 px-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.eskuls.show', $eskul) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm">
                                    <x-icon name="eye" class="h-3 w-3" /> Detail
                                </a>
                                <a href="{{ route('admin.eskuls.edit', $eskul) }}" class="inline-flex items-center gap-1 rounded-lg border border-amber-300 bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800 hover:bg-amber-100 transition shadow-sm">
                                    <x-icon name="pencil" class="h-3 w-3" /> Ubah
                                </a>
                                <form method="POST" action="{{ route('admin.eskuls.destroy', $eskul) }}" onsubmit="return confirm('Hapus permanen ekstrakurikuler {{ $eskul->name }}? Tindakan ini tidak dapat dibatalkan.')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-rose-300 bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-100 transition shadow-sm" title="Hapus eskul">
                                        <x-icon name="trash" class="h-3 w-3" /> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-4 px-3 text-center text-gray-500">Belum ada eskul.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $eskuls->links() }}</div>
</div>
@endsection

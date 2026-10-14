@extends('layouts.staff')

@section('title', 'Data Periode')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-bold">Data Periode</h1>
    <a href="{{ route('admin.periodes.create') }}" class="px-4 py-2 rounded bg-blue-600 text-white text-sm hover:bg-blue-700">Tambah Periode</a>
</div>

<div class="bg-white rounded-lg shadow p-4">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b text-left text-gray-500">
                    <th class="py-2 px-3">Periode</th>
                    <th class="py-2 px-3">Nama</th>
                    <th class="py-2 px-3">Status</th>
                    <th class="py-2 px-3">Jendela</th>
                    <th class="py-2 px-3">Pendaftar</th>
                    <th class="py-2 px-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($periodes as $p)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-3 font-medium">{{ $p->periode }}</td>
                        <td class="py-2 px-3">{{ $p->nama ?? '-' }}</td>
                        <td class="py-2 px-3">
                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $p->status->value === 'DIBUKA' ? 'bg-emerald-100 text-emerald-800' : ($p->status->value === 'DIARSIPKAN' ? 'bg-slate-200 text-slate-600' : 'bg-amber-100 text-amber-800') }}">{{ $p->status->value === 'DIBUKA' ? 'Aktif' : $p->status->label() }}</span>
                        </td>
                        <td class="py-2 px-3">{{ $p->window && $p->window->buka ? 'Buka' : 'Tutup' }}</td>
                        <td class="py-2 px-3">{{ $p->registrations_count }}</td>
                        <td class="py-2 px-3 flex gap-2">
                            <a href="{{ route('admin.periodes.show', $p->periode) }}" class="text-blue-600 hover:underline">Detail</a>
                            <a href="{{ route('admin.periodes.edit', $p->periode) }}" class="text-yellow-600 hover:underline">Ubah</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-4 px-3 text-center text-gray-500">Belum ada periode.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $periodes->links() }}</div>
</div>
@endsection

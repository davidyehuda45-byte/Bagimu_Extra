@extends('layouts.staff')

@section('title', 'Inventaris')

@section('content')
<div class="mb-4 flex flex-wrap items-center gap-3">
    <div>
        <h1 class="text-xl font-bold">Inventaris</h1>
        <p class="text-sm text-gray-600">Kelola barang dan peminjaman perlengkapan eskul.</p>
    </div>
    <a href="{{ route('admin.inventory.create') }}" class="ms-auto px-4 py-2 rounded bg-gray-900 text-white text-sm">Tambah Barang</a>
</div>

<form method="GET" action="{{ route('admin.inventory.index') }}" class="mb-4 flex flex-wrap gap-3 items-end bg-white border rounded-lg p-4">
    <div>
        <label for="eskul_id" class="block text-xs font-medium text-gray-700">Ekstrakurikuler</label>
        <select name="eskul_id" id="eskul_id" class="mt-1 border rounded px-3 py-2 text-sm">
            <option value="">Semua</option>
            @foreach ($eskuls as $e)
                <option value="{{ $e->id }}" @selected((string) $filterEskul === (string) $e->id)>{{ $e->name }}</option>
            @endforeach
        </select>
    </div>
    <button class="px-4 py-2 rounded bg-gray-900 text-white text-sm">Saring</button>
</form>

<div class="bg-white border rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium">Nama</th>
                    <th class="px-4 py-2 text-left font-medium">Eskul</th>
                    <th class="px-4 py-2 text-left font-medium">Jumlah</th>
                    <th class="px-4 py-2 text-left font-medium">Kondisi</th>
                    <th class="px-4 py-2 text-left font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($items as $item)
                    <tr>
                        <td class="px-4 py-2">{{ $item->nama }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $item->eskul?->name ?? 'Umum' }}</td>
                        <td class="px-4 py-2">{{ $item->jumlah }}</td>
                        <td class="px-4 py-2">{{ $item->kondisi?->label() ?? $item->kondisi }}</td>
                        <td class="px-4 py-2">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.inventory.show', $item) }}" class="px-3 py-1 rounded border text-xs hover:bg-gray-50">Detail</a>
                                <a href="{{ route('admin.inventory.edit', $item) }}" class="px-3 py-1 rounded border text-xs hover:bg-gray-50">Ubah</a>
                                <form method="POST" action="{{ route('admin.inventory.destroy', $item) }}" onsubmit="return confirm('Hapus barang ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 rounded bg-red-700 text-white text-xs">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center">
                            <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                <x-icon name="package" class="h-5 w-5" />
                            </div>
                            <p class="text-sm font-semibold text-slate-700">Belum ada barang inventaris</p>
                            <p class="text-xs text-slate-400 mt-0.5">Mulai catat perlengkapan dan peralatan ekstrakurikuler di sini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $items->links() }}</div>
</div>
@endsection

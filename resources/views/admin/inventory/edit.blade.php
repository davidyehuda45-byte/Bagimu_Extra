@extends('layouts.staff')

@section('title', 'Ubah Barang')

@section('content')
<div class="mb-4">
    <h1 class="text-xl font-bold">Ubah Barang</h1>
    <p class="text-sm text-gray-600">Perbarui data barang {{ $item->nama }}.</p>
</div>

<form method="POST" action="{{ route('admin.inventory.update', $item) }}" class="bg-white border rounded-lg p-6 w-full space-y-4">
    @csrf
    @method('PUT')
    <div>
        <label for="nama" class="block text-sm font-medium">Nama Barang</label>
        <input type="text" name="nama" id="nama" value="{{ old('nama', $item->nama) }}" required maxlength="100" class="mt-1 w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label for="eskul_id" class="block text-sm font-medium">Ekstrakurikuler (opsional)</label>
        <select name="eskul_id" id="eskul_id" class="mt-1 w-full border rounded px-3 py-2 text-sm">
            <option value="">Umum</option>
            @foreach ($eskuls as $e)
                <option value="{{ $e->id }}" @selected(old('eskul_id', $item->eskul_id) == $e->id)>{{ $e->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="jumlah" class="block text-sm font-medium">Jumlah</label>
            <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah', $item->jumlah) }}" required min="1" class="mt-1 w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label for="kondisi" class="block text-sm font-medium">Kondisi</label>
            <select name="kondisi" id="kondisi" class="mt-1 w-full border rounded px-3 py-2 text-sm">
                @foreach ($kondisiList as $k)
                    <option value="{{ $k->value }}" @selected(old('kondisi', $item->kondisi?->value ?? $item->kondisi) === $k->value)>{{ $k->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div>
        <label for="keterangan" class="block text-sm font-medium">Keterangan</label>
        <textarea name="keterangan" id="keterangan" rows="3" maxlength="500" class="mt-1 w-full border rounded px-3 py-2 text-sm">{{ old('keterangan', $item->keterangan) }}</textarea>
    </div>
    <div class="flex gap-2">
        <button class="px-4 py-2 rounded bg-gray-900 text-white text-sm">Simpan</button>
        <a href="{{ route('admin.inventory.index') }}" class="px-4 py-2 rounded border text-sm">Batal</a>
    </div>
</form>
@endsection

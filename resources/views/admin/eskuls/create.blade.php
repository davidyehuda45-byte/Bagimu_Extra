@extends('layouts.staff')

@section('title', 'Tambah Eskul')

@section('content')
<h1 class="text-xl font-bold mb-4">Tambah Eskul</h1>

<form method="POST" action="{{ route('admin.eskuls.store') }}" class="bg-white rounded-lg shadow p-5 space-y-4 w-full">
    @csrf
    <div>
        <label class="block text-sm font-medium mb-1">Nama Eskul</label>
        <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Deskripsi</label>
        <textarea name="description" rows="3" class="w-full border rounded px-3 py-2 text-sm">{{ old('description') }}</textarea>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Kuota</label>
            <input type="number" name="kuota" value="{{ old('kuota') }}" min="0" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Pembina</label>
            <select name="pembina_id" class="w-full border rounded px-3 py-2 text-sm">
                <option value="">— Tanpa pembina —</option>
                @foreach ($pembinas as $p)
                    <option value="{{ $p->id }}" @selected(old('pembina_id') == $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="flex gap-6">
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="kuota_aktif" value="1" @checked(old('kuota_aktif'))> Batasi kuota</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="aktif" value="1" @checked(old('aktif', true))> Aktif</label>
    </div>
    <div class="flex gap-2">
        <button class="px-4 py-2 rounded bg-blue-600 text-white text-sm hover:bg-blue-700">Simpan</button>
        <a href="{{ route('admin.eskuls.index') }}" class="px-4 py-2 rounded border text-sm">Batal</a>
    </div>
</form>
@endsection

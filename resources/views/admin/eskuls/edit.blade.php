@extends('layouts.staff')

@section('title', 'Ubah Eskul')

@section('content')
<h1 class="text-xl font-bold mb-4">Ubah Eskul: {{ $eskul->name }}</h1>

<form method="POST" action="{{ route('admin.eskuls.update', $eskul) }}" class="bg-white rounded-lg shadow p-5 space-y-4 w-full">
    @csrf
    @method('PUT')
    <div>
        <label class="block text-sm font-medium mb-1">Nama Eskul</label>
        <input type="text" name="name" value="{{ old('name', $eskul->name) }}" required class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Deskripsi</label>
        <textarea name="description" rows="3" class="w-full border rounded px-3 py-2 text-sm">{{ old('description', $eskul->description) }}</textarea>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Kuota</label>
            <input type="number" name="kuota" value="{{ old('kuota', $eskul->kuota) }}" min="0" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Pembina</label>
            <select name="pembina_id" class="w-full border rounded px-3 py-2 text-sm">
                <option value="">— Tanpa pembina —</option>
                @foreach ($pembinas as $p)
                    <option value="{{ $p->id }}" @selected(old('pembina_id', $eskul->pembina_id) == $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="flex gap-6">
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="kuota_aktif" value="1" @checked(old('kuota_aktif', $eskul->kuota_aktif))> Batasi kuota</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="aktif" value="1" @checked(old('aktif', $eskul->aktif))> Aktif</label>
    </div>
    <div class="flex gap-2">
        <button class="px-4 py-2 rounded bg-blue-600 text-white text-sm hover:bg-blue-700">Simpan Perubahan Eskul</button>
        <a href="{{ route('admin.eskuls.show', $eskul) }}" class="px-4 py-2 rounded border text-sm">Batal</a>
    </div>
</form>

<div class="mt-6 w-full rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <x-icon name="clock" class="h-5 w-5 text-blue-700" />
            <h2 class="text-base font-bold text-slate-900">Jadwal Latihan Eskul</h2>
        </div>
        <a href="{{ route('admin.eskuls.show', $eskul) }}#jadwal" class="inline-flex items-center gap-1 rounded-xl bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700 hover:bg-blue-100 transition">
            Kelola / Ubah Jadwal &rarr;
        </a>
    </div>
    <p class="text-xs text-slate-500 mb-3">Jadwal latihan eskul saat ini:</p>
    @if ($eskul->jadwals->isEmpty())
        <p class="text-xs text-slate-400 italic">Belum ada jadwal yang diatur untuk ekstrakurikuler ini.</p>
    @else
        <div class="divide-y divide-slate-100 text-xs">
            @foreach ($eskul->jadwals as $j)
                <div class="py-2 flex items-center justify-between">
                    <span class="font-bold text-slate-800">{{ $j->hari }}</span>
                    <span class="font-mono text-slate-600">{{ $j->jam_mulai }} – {{ $j->jam_selesai }}</span>
                    <span class="text-slate-500">{{ $j->tempat ?? '—' }}</span>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

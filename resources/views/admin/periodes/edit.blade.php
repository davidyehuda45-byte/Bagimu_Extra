@extends('layouts.staff')

@section('title', 'Ubah Periode')

@section('content')
<h1 class="text-xl font-bold mb-4">Ubah Periode: {{ $periode->periode }}</h1>

<form method="POST" action="{{ route('admin.periodes.update', $periode->periode) }}" class="bg-white rounded-lg shadow p-5 space-y-4 w-full">
    @csrf
    @method('PUT')
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Nama</label>
            <input type="text" name="nama" value="{{ old('nama', $periode->nama) }}" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Tahun Ajaran</label>
            <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', $periode->tahun_ajaran) }}" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Semester</label>
            <input type="text" name="semester" value="{{ old('semester', $periode->semester) }}" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <select name="status" required class="w-full border rounded px-3 py-2 text-sm">
                @foreach ($statusList as $s)
                    <option value="{{ $s->value }}" @selected(old('status', $periode->status->value) === $s->value)>{{ $s->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Tanggal Mulai</label>
            <input type="date" name="tgl_mulai" value="{{ old('tgl_mulai', $periode->tgl_mulai?->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Tanggal Selesai</label>
            <input type="date" name="tgl_selesai" value="{{ old('tgl_selesai', $periode->tgl_selesai?->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Maks. Eskul per Siswa</label>
            <input type="number" name="max_eskul_per_siswa" value="{{ old('max_eskul_per_siswa', $periode->max_eskul_per_siswa) }}" min="1" required class="w-full border rounded px-3 py-2 text-sm">
        </div>
    </div>
    <div class="flex gap-2">
        <button class="px-4 py-2 rounded bg-blue-600 text-white text-sm hover:bg-blue-700">Simpan</button>
        <a href="{{ route('admin.periodes.show', $periode->periode) }}" class="px-4 py-2 rounded border text-sm">Batal</a>
    </div>
</form>
@endsection

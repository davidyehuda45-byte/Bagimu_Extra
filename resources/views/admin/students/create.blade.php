@extends('layouts.staff')

@section('title', 'Tambah Siswa')

@section('content')
<h1 class="text-xl font-bold mb-4">Tambah Siswa</h1>

<form method="POST" action="{{ route('admin.students.store') }}" class="bg-white rounded-lg shadow p-5 space-y-4 w-full">
    @csrf
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">NIS</label>
            <input type="text" name="nis" value="{{ old('nis') }}" required class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">NISN</label>
            <input type="text" name="nisn" value="{{ old('nisn') }}" maxlength="20" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Nama</label>
            <input type="text" name="nama" value="{{ old('nama') }}" required class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Kelas</label>
            <input type="text" name="kelas" value="{{ old('kelas') }}" required class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Jurusan</label>
            <select name="jurusan" required class="w-full border rounded px-3 py-2 text-sm">
                @foreach ($jurusanList as $j)
                    <option value="{{ $j->value }}" @selected(old('jurusan') === $j->value)>{{ $j->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Jenis Kelamin</label>
            <select name="gender" class="w-full border rounded px-3 py-2 text-sm">
                <option value="">— Pilih —</option>
                @foreach ($genderList as $g)
                    <option value="{{ $g->value }}" @selected(old('gender') === $g->value)>{{ $g->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">No. Absen</label>
            <input type="number" name="no_absen" value="{{ old('no_absen') }}" min="1" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2 text-sm">
                @foreach (['AKTIF', 'LULUS', 'PINDAH', 'NONAKTIF', 'ALUMNI'] as $s)
                    <option value="{{ $s }}" @selected(old('status', 'AKTIF') === $s)>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Nama Wali</label>
            <input type="text" name="wali_nama" value="{{ old('wali_nama') }}" maxlength="100" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Kontak Wali</label>
            <input type="text" name="wali_kontak" value="{{ old('wali_kontak') }}" maxlength="20" class="w-full border rounded px-3 py-2 text-sm">
        </div>
    </div>
    <div class="flex gap-2">
        <button class="px-4 py-2 rounded bg-blue-600 text-white text-sm hover:bg-blue-700">Simpan</button>
        <a href="{{ route('admin.students.index') }}" class="px-4 py-2 rounded border text-sm">Batal</a>
    </div>
</form>
@endsection

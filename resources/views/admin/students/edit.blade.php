@extends('layouts.staff')

@section('title', 'Ubah Siswa')

@section('content')
<h1 class="text-xl font-bold text-slate-900 mb-4">Ubah Siswa: {{ $student->nama }}</h1>

<form method="POST" action="{{ route('admin.students.update', $student) }}" class="rounded-2xl border border-slate-200 bg-white p-5 space-y-4 w-full shadow-sm">
    @csrf
    @method('PUT')
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700">NIS</label>
            <input type="text" name="nis" value="{{ old('nis', $student->nis) }}" required class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700">NISN</label>
            <input type="text" name="nisn" value="{{ old('nisn', $student->nisn) }}" maxlength="20" class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700">Nama</label>
            <input type="text" name="nama" value="{{ old('nama', $student->nama) }}" required class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700">Kelas</label>
            <input type="text" name="kelas" value="{{ old('kelas', $student->kelas) }}" required class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700">Jurusan</label>
            <select name="jurusan" required class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
                @foreach ($jurusanList as $j)
                    <option value="{{ $j->value }}" @selected(old('jurusan', $student->jurusan->value) === $j->value)>{{ $j->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700">Jenis Kelamin</label>
            <select name="gender" class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
                <option value="">— Pilih —</option>
                @foreach ($genderList as $g)
                    <option value="{{ $g->value }}" @selected(old('gender', $student->gender?->value) === $g->value)>{{ $g->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700">No. Absen</label>
            <input type="number" name="no_absen" value="{{ old('no_absen', $student->no_absen) }}" min="1" class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700">Status</label>
            <select name="status" class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
                @foreach (['AKTIF', 'LULUS', 'PINDAH', 'NONAKTIF', 'ALUMNI'] as $s)
                    <option value="{{ $s }}" @selected(old('status', $student->status) === $s)>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700">Nama Wali</label>
            <input type="text" name="wali_nama" value="{{ old('wali_nama', $student->wali_nama) }}" maxlength="100" class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700">Kontak Wali</label>
            <input type="text" name="wali_kontak" value="{{ old('wali_kontak', $student->wali_kontak) }}" maxlength="20" class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700"><span class="inline-flex items-center gap-1.5"><x-icon name="phone" class="h-3.5 w-3.5 text-slate-400" />Nomor HP / WA Siswa</span></label>
            <input type="text" name="whatsapp" value="{{ old('whatsapp', $student->whatsapp) }}" maxlength="20" placeholder="cth: 081234567890" class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700">Alasan koreksi (wajib, tercatat audit)</label>
            <input type="text" name="alasan" value="{{ old('alasan') }}" required maxlength="500" class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
        </div>
    </div>
    <div class="flex gap-2">
        <button class="px-4 py-2 rounded-xl bg-blue-700 text-white text-sm font-bold hover:bg-blue-800">Simpan</button>
        <a href="{{ route('admin.students.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-sm">Batal</a>
    </div>
</form>

<form method="POST" action="{{ route('admin.students.destroy', $student) }}" data-confirm="Hapus permanen data siswa {{ $student->nama }}? Data yang dihapus tidak dapat dikembalikan." data-confirm-title="Hapus Data Siswa" data-confirm-btn="Hapus Siswa" class="mt-4 w-full">
    @csrf
    @method('DELETE')
    <button class="px-4 py-2 rounded-xl bg-rose-600 text-white text-sm font-bold hover:bg-rose-700">Hapus Siswa</button>
</form>
@endsection

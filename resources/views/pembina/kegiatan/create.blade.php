@extends('layouts.staff')

@section('title', 'Tambah Kegiatan')

@section('content')
<div class="w-full space-y-4">
    <div>
        <h1 class="text-xl font-bold">Tambah Kegiatan</h1>
        <p class="text-sm text-gray-600">Isi rencana kegiatan eskul yang Anda ampu.</p>
    </div>

    <form method="POST" action="{{ route('pembina.kegiatan.store') }}" class="space-y-3 rounded-lg border bg-white p-4 text-sm shadow-sm">
        @csrf
        <div>
            <label class="mb-1 block font-medium">Eskul</label>
            <select name="eskul_id" class="w-full rounded border px-2 py-1.5" required>
                <option value="">Pilih eskul</option>
                @foreach ($eskuls as $eskul)
                    <option value="{{ $eskul->id }}" @selected(old('eskul_id') == $eskul->id)>{{ $eskul->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label class="mb-1 block font-medium">Tanggal</label>
                <input type="date" name="tanggal" value="{{ old('tanggal') }}" class="w-full rounded border px-2 py-1.5" required>
            </div>
            <div>
                <label class="mb-1 block font-medium">Jenis</label>
                <select name="jenis" class="w-full rounded border px-2 py-1.5" required>
                    <option value="">Pilih jenis</option>
                    <option value="LATIHAN" @selected(old('jenis') === 'LATIHAN')>Latihan</option>
                    <option value="LOMBA" @selected(old('jenis') === 'LOMBA')>Lomba</option>
                    <option value="ACARA" @selected(old('jenis') === 'ACARA')>Acara</option>
                </select>
            </div>
        </div>
        <div>
            <label class="mb-1 block font-medium">Agenda</label>
            <input type="text" name="agenda" value="{{ old('agenda') }}" maxlength="255" class="w-full rounded border px-2 py-1.5" placeholder="Contoh: Latihan passing bawah" required>
        </div>
        <div>
            <label class="mb-1 block font-medium">Tujuan</label>
            <textarea name="tujuan" rows="2" class="w-full rounded border px-2 py-1.5" placeholder="Tujuan kegiatan">{{ old('tujuan') }}</textarea>
        </div>
        <div>
            <label class="mb-1 block font-medium">Materi</label>
            <textarea name="materi" rows="2" class="w-full rounded border px-2 py-1.5" placeholder="Materi yang disampaikan">{{ old('materi') }}</textarea>
        </div>
        <div>
            <label class="mb-1 block font-medium">Lokasi</label>
            <input type="text" name="lokasi" value="{{ old('lokasi') }}" maxlength="255" class="w-full rounded border px-2 py-1.5" placeholder="Contoh: Lapangan utama">
        </div>
        <div>
            <label class="mb-1 block font-medium">Dokumentasi</label>
            <input type="text" name="dokumentasi" value="{{ old('dokumentasi') }}" maxlength="255" class="w-full rounded border px-2 py-1.5" placeholder="Tautan dokumentasi (opsional)">
        </div>
        <div>
            <label class="mb-1 block font-medium">Hasil</label>
            <textarea name="hasil" rows="2" class="w-full rounded border px-2 py-1.5" placeholder="Hasil kegiatan (diisi setelah pelaksanaan)">{{ old('hasil') }}</textarea>
        </div>
        <div>
            <label class="mb-1 block font-medium">Kendala</label>
            <textarea name="kendala" rows="2" class="w-full rounded border px-2 py-1.5" placeholder="Kendala yang dihadapi">{{ old('kendala') }}</textarea>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="rounded bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700">Simpan</button>
            <a href="{{ route('pembina.kegiatan.index') }}" class="rounded border px-4 py-2 hover:bg-gray-50">Batal</a>
        </div>
    </form>
</div>
@endsection

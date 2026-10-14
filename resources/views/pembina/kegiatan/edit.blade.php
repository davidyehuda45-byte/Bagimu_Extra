@extends('layouts.staff')

@section('title', 'Ubah Kegiatan')

@section('content')
<div class="w-full space-y-4">
    <div>
        <h1 class="text-xl font-bold">Ubah Kegiatan</h1>
        <p class="text-sm text-gray-600">Perbarui data kegiatan eskul.</p>
    </div>

    <form method="POST" action="{{ route('pembina.kegiatan.update', $kegiatan) }}" class="space-y-3 rounded-lg border bg-white p-4 text-sm shadow-sm">
        @csrf
        @method('PUT')
        <div>
            <label class="mb-1 block font-medium">Eskul</label>
            <select name="eskul_id" class="w-full rounded border px-2 py-1.5" required>
                @foreach ($eskuls as $eskul)
                    <option value="{{ $eskul->id }}" @selected((string) old('eskul_id', $kegiatan->eskul_id) === (string) $eskul->id)>{{ $eskul->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label class="mb-1 block font-medium">Tanggal</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', $kegiatan->tanggal->format('Y-m-d')) }}" class="w-full rounded border px-2 py-1.5" required>
            </div>
            <div>
                <label class="mb-1 block font-medium">Jenis</label>
                <select name="jenis" class="w-full rounded border px-2 py-1.5" required>
                    <option value="LATIHAN" @selected(old('jenis', $kegiatan->jenis->value) === 'LATIHAN')>Latihan</option>
                    <option value="LOMBA" @selected(old('jenis', $kegiatan->jenis->value) === 'LOMBA')>Lomba</option>
                    <option value="ACARA" @selected(old('jenis', $kegiatan->jenis->value) === 'ACARA')>Acara</option>
                </select>
            </div>
        </div>
        <div>
            <label class="mb-1 block font-medium">Agenda</label>
            <input type="text" name="agenda" value="{{ old('agenda', $kegiatan->agenda) }}" maxlength="255" class="w-full rounded border px-2 py-1.5" required>
        </div>
        <div>
            <label class="mb-1 block font-medium">Tujuan</label>
            <textarea name="tujuan" rows="2" class="w-full rounded border px-2 py-1.5">{{ old('tujuan', $kegiatan->tujuan) }}</textarea>
        </div>
        <div>
            <label class="mb-1 block font-medium">Materi</label>
            <textarea name="materi" rows="2" class="w-full rounded border px-2 py-1.5">{{ old('materi', $kegiatan->materi) }}</textarea>
        </div>
        <div>
            <label class="mb-1 block font-medium">Lokasi</label>
            <input type="text" name="lokasi" value="{{ old('lokasi', $kegiatan->lokasi) }}" maxlength="255" class="w-full rounded border px-2 py-1.5">
        </div>
        <div>
            <label class="mb-1 block font-medium">Dokumentasi</label>
            <input type="text" name="dokumentasi" value="{{ old('dokumentasi', $kegiatan->dokumentasi) }}" maxlength="255" class="w-full rounded border px-2 py-1.5">
        </div>
        <div>
            <label class="mb-1 block font-medium">Hasil</label>
            <textarea name="hasil" rows="2" class="w-full rounded border px-2 py-1.5">{{ old('hasil', $kegiatan->hasil) }}</textarea>
        </div>
        <div>
            <label class="mb-1 block font-medium">Kendala</label>
            <textarea name="kendala" rows="2" class="w-full rounded border px-2 py-1.5">{{ old('kendala', $kegiatan->kendala) }}</textarea>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="rounded bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700">Simpan Perubahan</button>
            <a href="{{ route('pembina.kegiatan.show', $kegiatan) }}" class="rounded border px-4 py-2 hover:bg-gray-50">Batal</a>
        </div>
    </form>
</div>
@endsection

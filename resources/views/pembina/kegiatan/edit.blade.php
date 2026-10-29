@extends('layouts.staff')

@section('title', 'Ubah Kegiatan')

@section('content')
<div class="w-full max-w-2xl mx-auto space-y-4">
    <div>
        <a href="{{ route('pembina.kegiatan.show', $kegiatan) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">← Kembali ke Detail Kegiatan</a>
        <h1 class="text-xl font-bold text-slate-900 mt-1">Ubah Data Kegiatan</h1>
        <p class="text-xs sm:text-sm text-slate-500">Perbarui jadwal atau agenda kegiatan ekstrakurikuler.</p>
    </div>

    <form method="POST" action="{{ route('pembina.kegiatan.update', $kegiatan) }}" class="space-y-4 rounded-3xl border border-slate-200/90 bg-white p-6 text-sm shadow-xs">
        @csrf
        @method('PUT')

        @if ($eskuls->count() === 1)
            <input type="hidden" name="eskul_id" value="{{ $eskuls->first()->id }}">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                <span class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Ekstrakurikuler</span>
                <span class="text-sm font-bold text-blue-900">{{ $eskuls->first()->name }}</span>
            </div>
        @else
            <div>
                <label class="mb-1 block font-semibold text-slate-700 text-xs">Ekstrakurikuler</label>
                <select name="eskul_id" class="w-full rounded-xl border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600" required>
                    @foreach ($eskuls as $eskul)
                        <option value="{{ $eskul->id }}" @selected((string) old('eskul_id', $kegiatan->eskul_id) === (string) $eskul->id)>{{ $eskul->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <input type="hidden" name="jenis" value="{{ old('jenis', $kegiatan->jenis->value ?? 'LATIHAN') }}">

        <div>
            <label class="mb-1 block font-semibold text-slate-700 text-xs">Tanggal Pelaksanaan</label>
            <input type="date" name="tanggal" value="{{ old('tanggal', $kegiatan->tanggal->format('Y-m-d')) }}" class="w-full rounded-xl border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600" required>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label class="mb-1 block font-semibold text-slate-700 text-xs">Jam Mulai</label>
                <input type="time" name="jam_mulai" value="{{ old('jam_mulai', $kegiatan->jam_mulai) }}" class="w-full rounded-xl border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
            </div>
            <div>
                <label class="mb-1 block font-semibold text-slate-700 text-xs">Jam Selesai</label>
                <input type="time" name="jam_selesai" value="{{ old('jam_selesai', $kegiatan->jam_selesai) }}" class="w-full rounded-xl border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
            </div>
        </div>

        <div>
            <label class="mb-1 block font-semibold text-slate-700 text-xs">Agenda Kegiatan</label>
            <input type="text" name="agenda" value="{{ old('agenda', $kegiatan->agenda) }}" maxlength="200" class="w-full rounded-xl border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600" required>
        </div>

        <div>
            <label class="mb-1 block font-semibold text-slate-700 text-xs">Lokasi / Tempat (Opsional)</label>
            <input type="text" name="lokasi" value="{{ old('lokasi', $kegiatan->lokasi) }}" maxlength="100" class="w-full rounded-xl border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
        </div>

        <div>
            <label class="mb-1 block font-semibold text-slate-700 text-xs">Kendala / Catatan (Opsional)</label>
            <textarea name="kendala" rows="2" class="w-full rounded-xl border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">{{ old('kendala', $kegiatan->kendala) }}</textarea>
        </div>

        <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
            <button type="submit" class="rounded-xl bg-blue-700 px-5 py-2.5 text-xs font-bold text-white hover:bg-blue-800 transition shadow-xs">
                Perbarui Kegiatan
            </button>
            <a href="{{ route('pembina.kegiatan.show', $kegiatan) }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

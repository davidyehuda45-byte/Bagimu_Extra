@extends('layouts.staff')

@section('title', 'Tambah Kegiatan')

@section('content')
<div class="w-full max-w-2xl mx-auto space-y-4">
    <div>
        <a href="{{ route('pembina.kegiatan.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">← Kembali ke Daftar Kegiatan</a>
        <h1 class="text-xl font-bold text-slate-900 mt-1">Tambah Jadwal Kegiatan</h1>
        <p class="text-xs sm:text-sm text-slate-500">Rencanakan agenda kegiatan atau latihan ekstrakurikuler.</p>
    </div>

    <form method="POST" action="{{ route('pembina.kegiatan.store') }}" class="space-y-4 rounded-3xl border border-slate-200/90 bg-white p-6 text-sm shadow-xs">
        @csrf

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
                        <option value="{{ $eskul->id }}" @selected(old('eskul_id') == $eskul->id)>{{ $eskul->name }}</option>
                    @endforeach
                </select>
                @error('eskul_id')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <input type="hidden" name="jenis" value="{{ old('jenis', 'LATIHAN') }}">

        <div>
            <label class="mb-1 block font-semibold text-slate-700 text-xs">Tanggal Pelaksanaan</label>
            <input type="date" name="tanggal" value="{{ old('tanggal', today()->toDateString()) }}" class="w-full rounded-xl border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600" required>
            @error('tanggal')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Waktu Jam Mulai & Jam Selesai --}}
        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label class="mb-1 block font-semibold text-slate-700 text-xs">Jam Mulai</label>
                <input type="time" name="jam_mulai" value="{{ old('jam_mulai', '15:00') }}" class="w-full rounded-xl border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                @error('jam_mulai')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block font-semibold text-slate-700 text-xs">Jam Selesai</label>
                <input type="time" name="jam_selesai" value="{{ old('jam_selesai', '17:00') }}" class="w-full rounded-xl border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                @error('jam_selesai')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label class="mb-1 block font-semibold text-slate-700 text-xs">Agenda Kegiatan</label>
            <input type="text" name="agenda" value="{{ old('agenda') }}" maxlength="200" class="w-full rounded-xl border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Contoh: Latihan passing dan strategi pertahanan" required>
            @error('agenda')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="mb-1 block font-semibold text-slate-700 text-xs">Lokasi / Tempat (Opsional)</label>
            <input type="text" name="lokasi" value="{{ old('lokasi') }}" maxlength="100" class="w-full rounded-xl border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Contoh: Lapangan Utama atau Gedung Olahraga">
            @error('lokasi')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="mb-1 block font-semibold text-slate-700 text-xs">Kendala / Catatan (Opsional)</label>
            <textarea name="kendala" rows="2" class="w-full rounded-xl border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Catatan khusus atau perlengkapan yang perlu disiapkan">{{ old('kendala') }}</textarea>
        </div>

        <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
            <button type="submit" class="rounded-xl bg-blue-700 px-5 py-2.5 text-xs font-bold text-white hover:bg-blue-800 transition shadow-xs">
                Simpan Rencana Kegiatan
            </button>
            <a href="{{ route('pembina.kegiatan.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

@extends('layouts.staff')

@section('title', 'Jendela Pendaftaran')

@section('content')
<h1 class="text-xl font-bold text-slate-900 mb-4">Jendela Pendaftaran</h1>

@if (!$periode)
    <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
        <p class="text-sm text-slate-500">Belum ada periode. Buat periode dulu.</p>
    </div>
@else
    {{-- Kartu status --}}
    @php($buka = $window && $window->exists && $window->isOpen())
    <div class="rounded-2xl border p-5 mb-4 shadow-sm {{ $buka ? 'border-emerald-200 bg-emerald-50' : 'border-rose-200 bg-rose-50' }}">
        <div class="flex flex-wrap items-center gap-3">
            <div>
                <p class="font-extrabold {{ $buka ? 'text-emerald-800' : 'text-rose-700' }}">
                    {{ $buka ? 'DIBUKA — Siswa bisa daftar' : 'DITUTUP — Formulir terkunci' }}
                </p>
                <p class="text-sm {{ $buka ? 'text-emerald-700' : 'text-rose-600' }}">Periode {{ $periode->periode }} • {{ $window && $window->exists ? $window->label() : 'Jendela default (terbuka)' }}</p>
            </div>
            <form method="POST" action="{{ route('admin.windows.update', $periode->periode) }}" class="ms-auto">
                @csrf
                @method('PUT')
                <input type="hidden" name="buka" value="{{ $buka ? '0' : '1' }}">
                @if ($window && $window->exists)
                    <input type="hidden" name="tgl_buka" value="{{ $window->tgl_buka?->format('Y-m-d\TH:i') }}">
                    <input type="hidden" name="tgl_tutup" value="{{ $window->tgl_tutup?->format('Y-m-d\TH:i') }}">
                @endif
                <button class="rounded-xl px-5 py-2.5 text-sm font-bold text-white {{ $buka ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                    {{ $buka ? 'Tutup' : 'Buka' }}
                </button>
            </form>
        </div>
        <p class="mt-2 text-[11px] {{ $buka ? 'text-emerald-600' : 'text-rose-500' }}">Berlaku seketika, jadwal tanggal dipertahankan.</p>
    </div>

    {{-- Jadwal otomatis --}}
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-5 shadow-sm">
        <h2 class="font-bold text-slate-900 text-sm mb-3">Jadwal Otomatis (opsional)</h2>
        <form method="POST" action="{{ route('admin.windows.update', $periode->periode) }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-sm">
            @csrf
            @method('PUT')
            <input type="hidden" name="buka" value="1">
            <div class="sm:col-span-2">
                <label class="block font-medium text-slate-700 mb-1">Buka mulai</label>
                <input type="datetime-local" name="tgl_buka" value="{{ old('tgl_buka', $window?->tgl_buka?->format('Y-m-d\TH:i')) }}" class="w-full border-slate-300 rounded-xl">
            </div>
            <div class="sm:col-span-2">
                <label class="block font-medium text-slate-700 mb-1">Tutup pada</label>
                <input type="datetime-local" name="tgl_tutup" value="{{ old('tgl_tutup', $window?->tgl_tutup?->format('Y-m-d\TH:i')) }}" class="w-full border-slate-300 rounded-xl">
            </div>
            <div class="flex items-end gap-2 sm:col-span-4">
                <button class="px-4 py-2 rounded-xl bg-blue-700 text-white font-bold hover:bg-blue-800">Simpan Jadwal Otomatis</button>
            </div>
        </form>
        @if ($window && $window->exists && ($window->tgl_buka || $window->tgl_tutup))
            <form method="POST" action="{{ route('admin.windows.update', $periode->periode) }}" class="mt-2">
                @csrf
                @method('PUT')
                <input type="hidden" name="buka" value="{{ $window->buka ? '1' : '0' }}">
                <button class="text-xs font-medium text-rose-600 hover:underline">Hapus Jadwal</button>
            </form>
        @endif
    </div>
@endif
@endsection

@extends('layouts.staff')

@section('title', 'Detail Periode')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-bold">Periode {{ $periode->periode }}</h1>
    <div class="flex gap-2">
        <a href="{{ route('admin.periodes.edit', $periode->periode) }}" class="px-4 py-2 rounded bg-yellow-500 text-white text-sm">Ubah</a>
        <a href="{{ route('admin.periodes.index') }}" class="px-4 py-2 rounded border text-sm">Kembali</a>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-5 mb-4 text-sm space-y-1">
    <p><span class="text-gray-500">Nama:</span> {{ $periode->nama ?? '-' }}</p>
    <p><span class="text-gray-500">Tahun Ajaran:</span> {{ $periode->tahun_ajaran ?? '-' }}</p>
    <p><span class="text-gray-500">Semester:</span> {{ $periode->semester ?? '-' }}</p>
    <p><span class="text-gray-500">Status:</span> {{ $periode->status->label() }}</p>
    <p><span class="text-gray-500">Maks. Eskul/Siswa:</span> {{ $periode->max_eskul_per_siswa }}</p>
    <form method="POST" action="{{ route('admin.periodes.destroy', $periode->periode) }}" onsubmit="return confirm('Hapus periode ini?')" class="pt-2">
        @csrf
        @method('DELETE')
        <button class="px-4 py-2 rounded bg-red-600 text-white text-sm hover:bg-red-700">Hapus Periode</button>
    </form>
</div>

<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <h2 class="font-bold text-slate-900 text-sm mb-1">Jendela Pendaftaran</h2>
    <p class="text-sm text-slate-500 mb-3">Status saat ini: {{ $periode->window && $periode->window->buka ? 'Dibuka' : 'Ditutup' }}</p>
    <a href="{{ route('admin.windows.index') }}" class="inline-block px-4 py-2 rounded-xl bg-blue-700 text-white text-sm font-bold hover:bg-blue-800">Kelola Jendela →</a>
</div>
@endsection

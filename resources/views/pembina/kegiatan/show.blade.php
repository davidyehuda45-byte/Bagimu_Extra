@extends('layouts.staff')

@section('title', 'Detail Kegiatan')

@section('content')
<div class="w-full space-y-4">
    <a href="{{ route('pembina.kegiatan.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke Kegiatan Saya</a>

    <div class="rounded-lg border bg-white p-4 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-2">
            <div>
                <h1 class="text-xl font-bold">{{ $kegiatan->agenda }}</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $kegiatan->eskul->name ?? '-' }} · {{ $kegiatan->tanggal->format('d-m-Y') }}</p>
            </div>
            <span class="inline-block rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800">{{ $kegiatan->jenis->label() }}</span>
        </div>

        <dl class="mt-4 space-y-2 text-sm">
            <div class="grid grid-cols-3 gap-2 border-b pb-2">
                <dt class="font-medium text-gray-500">Tujuan</dt>
                <dd class="col-span-2">{{ $kegiatan->tujuan ?? '-' }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-2 border-b pb-2">
                <dt class="font-medium text-gray-500">Materi</dt>
                <dd class="col-span-2">{{ $kegiatan->materi ?? '-' }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-2 border-b pb-2">
                <dt class="font-medium text-gray-500">Lokasi</dt>
                <dd class="col-span-2">{{ $kegiatan->lokasi ?? '-' }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-2 border-b pb-2">
                <dt class="font-medium text-gray-500">Dokumentasi</dt>
                <dd class="col-span-2">{{ $kegiatan->dokumentasi ?? '-' }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-2 border-b pb-2">
                <dt class="font-medium text-gray-500">Hasil</dt>
                <dd class="col-span-2">{{ $kegiatan->hasil ?? '-' }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <dt class="font-medium text-gray-500">Kendala</dt>
                <dd class="col-span-2">{{ $kegiatan->kendala ?? '-' }}</dd>
            </div>
        </dl>

        <div class="mt-4 flex gap-2 text-sm">
            <a href="{{ route('pembina.kegiatan.edit', $kegiatan) }}" class="rounded bg-amber-500 px-4 py-2 font-medium text-white hover:bg-amber-600">Ubah</a>
            <form method="POST" action="{{ route('pembina.kegiatan.destroy', $kegiatan) }}" onsubmit="return confirm('Hapus kegiatan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded bg-red-600 px-4 py-2 font-medium text-white hover:bg-red-700">Hapus</button>
            </form>
        </div>
    </div>
</div>
@endsection

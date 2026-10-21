@extends('layouts.staff')

@section('title', 'Detail Kegiatan')

@section('content')
<div class="w-full max-w-2xl mx-auto space-y-4">
    <a href="{{ route('pembina.kegiatan.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">← Kembali ke Kegiatan Saya</a>

    <div class="rounded-3xl border border-slate-200/90 bg-white p-6 shadow-xs space-y-4">
        <div class="flex flex-wrap items-start justify-between gap-2 border-b border-slate-100 pb-4">
            <div>
                <span class="inline-block rounded-md bg-blue-50 px-2 py-0.5 text-xs font-bold text-blue-700 mb-1">
                    {{ $kegiatan->jenis->label() }}
                </span>
                <h1 class="text-xl font-bold text-slate-900">{{ $kegiatan->agenda }}</h1>
                <p class="mt-0.5 text-xs text-slate-500">{{ $kegiatan->eskul->name ?? '-' }} • {{ $kegiatan->tanggal->format('d/m/Y') }}</p>
            </div>
        </div>

        <dl class="space-y-3 text-xs sm:text-sm">
            <div class="grid grid-cols-3 gap-2 border-b border-slate-100 pb-2.5">
                <dt class="font-medium text-slate-500">Waktu / Jam</dt>
                <dd class="col-span-2 font-semibold text-slate-800">
                    {{ $kegiatan->jam_mulai ? $kegiatan->jam_mulai . ' – ' . ($kegiatan->jam_selesai ?? 'Selesai') : 'Belum diatur' }}
                </dd>
            </div>
            <div class="grid grid-cols-3 gap-2 border-b border-slate-100 pb-2.5">
                <dt class="font-medium text-slate-500">Lokasi / Tempat</dt>
                <dd class="col-span-2 font-medium text-slate-800">{{ $kegiatan->lokasi ?: 'Belum ditentukan' }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <dt class="font-medium text-slate-500">Catatan / Kendala</dt>
                <dd class="col-span-2 text-slate-700 whitespace-pre-line">{{ $kegiatan->kendala ?: 'Tidak ada kendala/catatan khusus.' }}</dd>
            </div>
        </dl>

        <div class="mt-6 flex items-center gap-2 pt-4 border-t border-slate-100 text-xs">
            <a href="{{ route('pembina.kegiatan.edit', $kegiatan) }}" class="rounded-xl bg-amber-500 px-4 py-2 font-bold text-white hover:bg-amber-600 transition shadow-2xs">
                Ubah Kegiatan
            </a>
            <form method="POST" action="{{ route('pembina.kegiatan.destroy', $kegiatan) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2 font-bold text-white hover:bg-rose-700 transition shadow-2xs cursor-pointer">
                    Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

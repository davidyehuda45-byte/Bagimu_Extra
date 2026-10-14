@extends('layouts.siswa')

@section('title', 'Pengajuan Izin')

@section('content')
<div class="space-y-3">
    <div class="flex items-center justify-between">
        <h1 class="text-lg font-bold">Pengajuan Izin</h1>
        <a href="{{ route('siswa.izin.create') }}" class="rounded-lg bg-emerald-700 text-white px-4 py-2.5 text-sm font-semibold">+ Buat Izin</a>
    </div>

    @forelse ($pengajuan as $izin)
        @php($st = $izin->status instanceof \App\Enums\IzinStatus ? $izin->status : \App\Enums\IzinStatus::from($izin->status))
        <div class="rounded-xl bg-white border p-4">
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $st === \App\Enums\IzinStatus::APPROVED ? 'bg-green-100 text-green-800' : ($st === \App\Enums\IzinStatus::REJECTED ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">{{ $st->label() }}</span>
                <span class="text-xs px-2.5 py-1 rounded-full bg-gray-100 text-gray-700 font-semibold">{{ $izin->jenis instanceof \App\Enums\IzinJenis ? $izin->jenis->label() : $izin->jenis }}</span>
            </div>
            <p class="mt-2 text-sm font-semibold">{{ $izin->eskul?->name ?? '—' }}</p>
            <p class="text-xs text-gray-500">Tanggal: {{ $izin->tanggal?->format('d M Y') }}</p>
            <p class="mt-1 text-sm">{{ $izin->alasan }}</p>
            @if ($izin->bukti)
                <p class="mt-1"><a href="{{ asset('storage/' . $izin->bukti) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:underline font-semibold"><x-icon name="file" class="h-3.5 w-3.5" /> Lampiran Bukti</a></p>
            @endif
        </div>
    @empty
        <div class="rounded-xl bg-white border p-4 text-center">
            <p class="text-sm text-gray-600">Belum ada pengajuan izin.</p>
        </div>
    @endforelse

    @if ($pengajuan->hasPages())
        <div>{{ $pengajuan->links() }}</div>
    @endif
</div>
@endsection

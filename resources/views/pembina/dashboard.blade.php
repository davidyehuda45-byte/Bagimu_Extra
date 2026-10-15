@extends('layouts.staff')

@section('title', 'Dashboard Pembina')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-xl font-bold">Dashboard Pembina</h1>
        <p class="text-sm text-gray-600">Ringkasan eskul yang Anda ampu, pendaftar, perizinan, dan kegiatan mendatang.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-lg border bg-white p-4 shadow-sm">
            <div class="text-sm text-gray-500">Eskul Diampu</div>
            <div class="mt-1 text-3xl font-bold">{{ $eskuls->count() }}</div>
            <a href="{{ route('pembina.eskul.index') }}" class="mt-2 inline-block text-sm text-blue-600 hover:underline">Lihat eskul saya</a>
        </div>
        <div class="rounded-lg border bg-white p-4 shadow-sm">
            <div class="text-sm text-gray-500">Anggota Terdaftar</div>
            <div class="mt-1 text-3xl font-bold">{{ $pendaftarPending }}</div>
            <p class="mt-2 text-xs text-gray-500">Periode aktif.</p>
        </div>
        <div class="rounded-lg border bg-white p-4 shadow-sm">
            <div class="text-sm text-gray-500">Izin Menunggu</div>
            <div class="mt-1 text-3xl font-bold">{{ $izinPending }}</div>
            <a href="{{ route('pembina.eskul.index') }}" class="mt-2 inline-block text-sm text-blue-600 hover:underline">Kelola perizinan via Eskul Saya</a>
        </div>
    </div>


    <div class="rounded-lg border bg-white p-4 shadow-sm">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="font-semibold">Eskul Saya</h2>
            <a href="{{ route('pembina.eskul.index') }}" class="text-sm text-blue-600 hover:underline">Lihat semua</a>
        </div>
        <div class="grid gap-3 sm:grid-cols-2">
            @forelse ($eskuls as $eskul)
                <a href="{{ route('pembina.eskul.show', $eskul) }}" class="block rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:bg-slate-50">
                    <p class="font-bold text-slate-900">{{ $eskul->name }}</p>
                    <p class="mt-0.5 line-clamp-1 text-xs text-slate-500">{{ $eskul->description ?? '—' }}</p>
                    <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                        <div class="rounded-xl bg-amber-50 p-2">
                            <p class="text-lg font-extrabold text-amber-600">{{ $stat[$eskul->id]['pending'] ?? 0 }}</p>
                            <p class="text-[10px] text-amber-700">Pending</p>
                        </div>
                        <div class="rounded-xl bg-emerald-50 p-2">
                            <p class="text-lg font-extrabold text-emerald-600">{{ $stat[$eskul->id]['approved'] ?? 0 }}</p>
                            <p class="text-[10px] text-emerald-700">Disetujui</p>
                        </div>
                        <div class="rounded-xl bg-slate-100 p-2">
                            <p class="text-lg font-extrabold text-slate-700">{{ $eskul->jadwals_count }}</p>
                            <p class="text-[10px] text-slate-500">Jadwal</p>
                        </div>
                    </div>
                    <p class="mt-2 text-[11px] text-slate-500">{{ $stat[$eskul->id]['approved'] ?? 0 }} anggota aktif • {{ $eskul->jadwals_count }} sesi/minggu</p>
                </a>
            @empty
                <p class="text-sm text-slate-500">Belum ada eskul yang Anda ampu. Hubungi Admin.</p>
            @endforelse
        </div>
    </div>

    <div class="rounded-lg border bg-white p-4 shadow-sm">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="font-semibold">5 Kegiatan Mendatang</h2>
            <a href="{{ route('pembina.kegiatan.index') }}" class="text-sm text-blue-600 hover:underline">Semua kegiatan</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50 text-left">
                        <th class="px-3 py-2 font-medium">Tanggal</th>
                        <th class="px-3 py-2 font-medium">Eskul</th>
                        <th class="px-3 py-2 font-medium">Jenis</th>
                        <th class="px-3 py-2 font-medium">Agenda</th>
                        <th class="px-3 py-2 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kegiatanMendatang as $kegiatan)
                        <tr class="border-b last:border-0">
                            <td class="px-3 py-2">{{ $kegiatan->tanggal->format('d-m-Y') }}</td>
                            <td class="px-3 py-2">{{ $kegiatan->eskul->name ?? '-' }}</td>
                            <td class="px-3 py-2">
                                <span class="inline-block rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800">{{ $kegiatan->jenis->label() }}</span>
                            </td>
                            <td class="px-3 py-2">{{ $kegiatan->agenda }}</td>
                            <td class="px-3 py-2">
                                <a href="{{ route('pembina.kegiatan.show', $kegiatan) }}" class="text-blue-600 hover:underline">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-4 text-center text-gray-500">Tidak ada kegiatan mendatang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

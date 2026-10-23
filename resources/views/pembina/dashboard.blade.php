@extends('layouts.staff')

@section('title', 'Dashboard Pembina')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Dashboard Pembina</h1>
        <p class="text-sm text-slate-500">Kelola kegiatan ekstrakurikuler, data anggota terdaftar, perizinan, dan jadwal latihan.</p>
    </div>

    @if ($eskul)
        {{-- Banner Eskul yang Diampu Langsung --}}
        <div class="rounded-3xl border border-blue-200/90 bg-gradient-to-r from-blue-700 to-blue-800 p-6 text-white shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-white border border-white/20 mb-2">
                        <x-icon name="trophy" class="h-3.5 w-3.5" /> Ekstrakurikuler yang Anda Bina
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white">{{ $eskul->name }}</h2>
                    <p class="mt-1 text-xs sm:text-sm text-blue-100 max-w-xl">{{ $eskul->description ?? 'Tidak ada deskripsi tambahan.' }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('pembina.scan.show', $eskul) }}" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-500 text-white px-4 py-2.5 text-xs font-bold hover:bg-emerald-600 transition shadow-sm">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 7h.01"/><path d="M17 7h.01"/><path d="M7 17h.01"/><path d="M17 17h.01"/></svg>
                        <span>Scan Presensi Kamera HP</span>
                    </a>
                    <a href="{{ route('pembina.eskul.show', $eskul) }}" class="inline-flex items-center gap-2 rounded-2xl bg-white px-4 py-2.5 text-xs font-bold text-blue-700 hover:bg-blue-50 transition shadow-sm">
                        <span>Buka Menu Lengkap</span>
                        <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                    </a>
                </div>
            </div>
        </div>

        {{-- Statistik Ringkas Eskul Ini --}}
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs">
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Anggota Aktif</div>
                <div class="mt-2 text-3xl font-extrabold text-emerald-600">{{ $pendaftarAktif }}</div>
                <p class="mt-1 text-xs text-slate-400">Siswa resmi terdaftar periode berjalan.</p>
            </div>
            <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs">
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Permohonan Izin</div>
                <div class="mt-2 text-3xl font-extrabold text-amber-600">{{ $izinPending }}</div>
                <a href="{{ route('pembina.eskul.show', $eskul) }}#izin" class="mt-1 inline-block text-xs font-semibold text-blue-600 hover:underline">Periksa perizinan →</a>
            </div>
            <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs">
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Jadwal Sesi Latihan</div>
                <div class="mt-2 text-3xl font-extrabold text-slate-800">{{ $eskul->jadwals_count }}</div>
                <p class="mt-1 text-xs text-slate-400">Sesi latihan rutin setiap minggunya.</p>
            </div>
        </div>
    @else
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-900">
            <h3 class="font-bold text-base mb-1">Belum Ada Eskul yang Ditugaskan</h3>
            <p class="text-xs sm:text-sm">Akun Anda belum dikaitkan dengan ekstrakurikuler mana pun. Silakan hubungi Administrator untuk penugasan eskul.</p>
        </div>
    @endif

    {{-- Kegiatan Mendatang --}}
    <div class="rounded-3xl border border-slate-200/90 bg-white p-6 shadow-xs">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">5 Kegiatan Mendatang</h2>
            <a href="{{ route('pembina.kegiatan.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Semua kegiatan →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50 text-left text-xs font-bold text-slate-500">
                        <th class="px-4 py-2.5">Tanggal</th>
                        <th class="px-4 py-2.5">Eskul</th>
                        <th class="px-4 py-2.5">Jenis</th>
                        <th class="px-4 py-2.5">Agenda</th>
                        <th class="px-4 py-2.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs sm:text-sm">
                    @forelse ($kegiatanMendatang as $kegiatan)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-2.5 whitespace-nowrap">{{ $kegiatan->tanggal->format('d/m/Y') }}</td>
                            <td class="px-4 py-2.5 font-medium">{{ $kegiatan->eskul->name ?? '-' }}</td>
                            <td class="px-4 py-2.5">
                                <span class="inline-block rounded-md bg-blue-50 px-2 py-0.5 text-xs font-bold text-blue-700">
                                    {{ $kegiatan->jenis->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 font-medium text-slate-800">{{ $kegiatan->agenda }}</td>
                            <td class="px-4 py-2.5">
                                <a href="{{ route('pembina.kegiatan.show', $kegiatan) }}" class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 transition shadow-2xs" title="Lihat Detail">
                                    <x-icon name="eye" class="h-4 w-4" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-xs text-slate-400">Tidak ada jadwal kegiatan mendatang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.staff')

@section('title', 'Kegiatan Saya')

@section('content')
<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <div>
            <h1 class="text-xl font-bold">Kegiatan Saya</h1>
            <p class="text-sm text-gray-600">Rencana dan pelaksanaan kegiatan eskul yang Anda ampu.</p>
        </div>
        <a href="{{ route('pembina.kegiatan.create') }}" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Tambah Kegiatan</a>
    </div>

    <form method="GET" action="{{ route('pembina.kegiatan.index') }}" class="flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4 text-sm shadow-xs">
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-700">Eskul</label>
            <select name="eskul_id" class="rounded-xl border border-slate-300 pl-3 pr-9 py-2 text-xs sm:text-sm bg-white focus:border-blue-600 focus:ring-blue-600 focus:outline-hidden min-w-[150px]">
                <option value="">Semua eskul</option>
                @foreach ($eskuls as $eskul)
                    <option value="{{ $eskul->id }}" @selected((string) $eskulFilter === (string) $eskul->id)>{{ $eskul->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-700">Jenis Kegiatan</label>
            <select name="jenis" class="rounded-xl border border-slate-300 pl-3 pr-9 py-2 text-xs sm:text-sm bg-white focus:border-blue-600 focus:ring-blue-600 focus:outline-hidden min-w-[140px]">
                <option value="">Semua jenis</option>
                <option value="LATIHAN" @selected($jenisFilter === 'LATIHAN')>Latihan</option>
                <option value="LOMBA" @selected($jenisFilter === 'LOMBA')>Lomba</option>
                <option value="ACARA" @selected($jenisFilter === 'ACARA')>Acara</option>
            </select>
        </div>
        <button type="submit" class="rounded-xl bg-blue-700 px-4 py-2 text-xs font-bold text-white hover:bg-blue-800 transition shadow-2xs">Saring</button>
        @if ($eskulFilter || $jenisFilter)
            <a href="{{ route('pembina.kegiatan.index') }}" class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">Reset</a>
        @endif
    </form>

    {{-- Ringkasan --}}
    <div class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-lg border bg-white p-4 shadow-sm">
            <div class="text-sm text-gray-500">Anggota disetujui</div>
            <div class="text-3xl font-extrabold text-slate-900">{{ $anggota }}</div>
        </div>
        <div class="rounded-lg border bg-white p-4 shadow-sm">
            <div class="text-sm text-gray-500">Pertemuan terselenggara</div>
            <div class="text-3xl font-extrabold text-slate-900">{{ $pertemuan->count() }}</div>
            <p class="text-[11px] text-slate-400">12 tanggal terakhir</p>
        </div>
        <div class="rounded-lg border bg-white p-4 shadow-sm">
            <div class="text-sm text-gray-500 mb-1">Rekap kehadiran</div>
            <div class="flex flex-wrap gap-1">
                @foreach (['HADIR' => 'bg-emerald-100 text-emerald-800', 'IZIN' => 'bg-amber-100 text-amber-800', 'SAKIT' => 'bg-amber-100 text-amber-800', 'ALPA' => 'bg-rose-100 text-rose-800'] as $val => $cls)
                    <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $cls }}">{{ $val }}: {{ $rekap[$val] ?? 0 }}</span>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Persentase per siswa --}}
    <div class="rounded-lg border bg-white p-4 shadow-sm">
        <h2 class="font-semibold mb-2">Persentase Kehadiran per Siswa (30 pertama)</h2>
        <div class="space-y-2">
            @forelse ($persenSiswa as $ps)
                <div>
                    <div class="flex justify-between text-xs">
                        <span class="font-medium truncate">{{ $ps['nama'] }}</span>
                        <span class="text-slate-500">{{ $ps['persen'] }}%</span>
                    </div>
                    <div class="mt-0.5 h-2 overflow-hidden rounded-full bg-slate-100">
                        <div class="{{ $ps['persen'] >= 75 ? 'bg-emerald-500' : ($ps['persen'] >= 50 ? 'bg-amber-400' : 'bg-rose-500') }} h-full rounded-full" style="width: {{ min(100, $ps['persen']) }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada anggota.</p>
            @endforelse
        </div>
    </div>

    {{-- Izin menunggu --}}
    <div class="rounded-lg border bg-white p-4 shadow-sm">
        <h2 class="font-semibold mb-2">Izin Menunggu ({{ $izinMenunggu->count() }})</h2>
        @if ($izinMenunggu->isEmpty())
            <p class="text-sm text-gray-500">Tidak ada pengajuan menunggu.</p>
        @else
            <ul class="space-y-2">
                @foreach ($izinMenunggu as $iz)
                    <li class="rounded-xl border border-slate-100 p-3 text-sm">
                        <div class="flex flex-wrap items-center gap-2">
                            <b>{{ $iz->registration?->user?->name ?? '—' }}</b>
                            <span class="text-slate-500">{{ $iz->tanggal->format('d M Y') }} • {{ $iz->jenis->label() }}</span>
                            <span class="ms-auto flex gap-1.5">
                                <form method="POST" action="{{ route('pembina.izin.approve', $iz) }}">
                                    @csrf
                                    <button class="rounded bg-green-600 px-2.5 py-1 text-xs font-medium text-white hover:bg-green-700">Setujui</button>
                                </form>
                                <form method="POST" action="{{ route('pembina.izin.reject', $iz) }}">
                                    @csrf
                                    <button class="rounded bg-red-600 px-2.5 py-1 text-xs font-medium text-white hover:bg-red-700">Tolak</button>
                                </form>
                            </span>
                        </div>
                        <p class="mt-1 text-slate-600">{{ $iz->alasan }}</p>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Sahkan per pertemuan --}}
    <div class="rounded-lg border bg-white p-4 shadow-sm">
        <h2 class="font-semibold mb-2">Sahkan Absensi per Pertemuan</h2>
        @if ($pertemuan->isEmpty())
            <p class="text-sm text-gray-500">Belum ada pertemuan.</p>
        @else
            <ul class="divide-y divide-slate-100 text-sm">
                @foreach ($pertemuan as $t)
                    <li class="flex items-center gap-2 py-2">
                        <span>{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }} ({{ $t->c }} baris)</span>
                        @if ($t->terkunci)
                            <span class="rounded-full bg-slate-200 px-2 py-0.5 text-[11px] font-semibold text-slate-600">TERKUNCI</span>
                        @else
                            <form method="POST" action="{{ route('pembina.kehadiran.lock', $t->eskul_id) }}" class="ms-auto" onsubmit="return confirm('Sahkan (kunci) absensi tanggal ini? Setelah dikunci tak bisa diubah kecuali Admin.')">
                                @csrf
                                <input type="hidden" name="tanggal" value="{{ \Carbon\Carbon::parse($t->tanggal)->format('Y-m-d') }}">
                                <button class="rounded border px-3 py-1 text-xs font-semibold hover:bg-slate-50">Sahkan</button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="rounded-lg border bg-white p-4 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50 text-left">
                        <th class="px-3 py-2 font-medium">Tanggal</th>
                        <th class="px-3 py-2 font-medium">Eskul</th>
                        <th class="px-3 py-2 font-medium">Jenis</th>
                        <th class="px-3 py-2 font-medium">Agenda</th>
                        <th class="px-3 py-2 font-medium">Lokasi</th>
                        <th class="px-3 py-2 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kegiatans as $kegiatan)
                        <tr class="border-b last:border-0">
                            <td class="px-3 py-2">{{ $kegiatan->tanggal->format('d-m-Y') }}</td>
                            <td class="px-3 py-2">{{ $kegiatan->eskul->name ?? '-' }}</td>
                            <td class="px-3 py-2">
                                <span class="inline-block rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800">{{ $kegiatan->jenis->label() }}</span>
                            </td>
                            <td class="px-3 py-2">{{ $kegiatan->agenda }}</td>
                            <td class="px-3 py-2">{{ $kegiatan->lokasi ?? '-' }}</td>
                            <td class="px-3 py-2">
                                <div class="flex gap-2">
                                    <a href="{{ route('pembina.kegiatan.show', $kegiatan) }}" class="text-blue-600 hover:underline">Detail</a>
                                    <a href="{{ route('pembina.kegiatan.edit', $kegiatan) }}" class="text-amber-600 hover:underline">Ubah</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-4 text-center text-gray-500">Belum ada kegiatan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

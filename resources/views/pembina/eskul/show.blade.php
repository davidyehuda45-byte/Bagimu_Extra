@extends('layouts.staff')

@section('title', 'Detail Eskul — ' . $eskul->name)

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('pembina.eskul.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke Eskul Saya</a>
    </div>

    <div class="rounded-lg border bg-white p-4 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold">{{ $eskul->name }}</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $eskul->description }}</p>
                <div class="mt-2 flex flex-wrap gap-2 text-sm">
                    <span class="rounded bg-slate-100 px-2 py-1">Kuota: {{ $eskul->kuota_aktif ? $eskul->kuota : 'Tanpa batas' }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('pembina.eskul.export', [$eskul, 'format' => 'excel']) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 transition shadow-sm">
                    <x-icon name="chart" class="h-3.5 w-3.5" /> Export Excel
                </a>
                <a href="{{ route('pembina.eskul.export', [$eskul, 'format' => 'csv']) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition shadow-sm">
                    <x-icon name="file" class="h-3.5 w-3.5" /> Export CSV
                </a>
            </div>
        </div>
    </div>

    <div class="rounded-lg border bg-white p-4 shadow-sm">
        <h2 class="mb-3 font-semibold">Jadwal Latihan</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50 text-left">
                        <th class="px-3 py-2 font-medium">Hari</th>
                        <th class="px-3 py-2 font-medium">Jam</th>
                        <th class="px-3 py-2 font-medium">Tempat</th>
                        <th class="px-3 py-2 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jadwals as $jadwal)
                        <tr class="border-b last:border-0" x-data="{ editing: false }">
                            <template x-if="!editing">
                                <td class="px-3 py-2 font-medium">{{ $jadwal->hari }}</td>
                            </template>
                            <template x-if="!editing">
                                <td class="px-3 py-2 font-mono text-xs">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</td>
                            </template>
                            <template x-if="!editing">
                                <td class="px-3 py-2">{{ $jadwal->tempat }}</td>
                            </template>
                            <template x-if="!editing">
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" @click="editing = true" class="inline-flex items-center gap-1 rounded border border-amber-300 bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-800 hover:bg-amber-100 transition shadow-sm">
                                            <x-icon name="pencil" class="h-3 w-3" /> Ubah
                                        </button>
                                        <form method="POST" action="{{ route('pembina.jadwals.destroy', $jadwal) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 rounded border border-rose-300 bg-rose-50 px-2 py-0.5 text-xs font-semibold text-rose-700 hover:bg-rose-100 transition shadow-sm">
                                                <x-icon name="trash" class="h-3 w-3" /> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </template>

                            <template x-if="editing">
                                <td colspan="4" class="p-3 bg-amber-50/60 rounded">
                                    <form method="POST" action="{{ route('pembina.jadwals.update', $jadwal) }}" class="space-y-2">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 text-xs">
                                            <div>
                                                <label class="block font-medium mb-1">Hari</label>
                                                <select name="hari" required class="w-full rounded border px-2 py-1 text-xs bg-white">
                                                    @foreach (['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'MINGGU'] as $hari)
                                                        <option value="{{ $hari }}" @selected($jadwal->hari === $hari)>{{ ucfirst(strtolower($hari)) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block font-medium mb-1">Jam Mulai</label>
                                                <input type="time" name="jam_mulai" value="{{ $jadwal->jam_mulai }}" required class="w-full rounded border px-2 py-1 text-xs bg-white font-mono">
                                            </div>
                                            <div>
                                                <label class="block font-medium mb-1">Jam Selesai</label>
                                                <input type="time" name="jam_selesai" value="{{ $jadwal->jam_selesai }}" required class="w-full rounded border px-2 py-1 text-xs bg-white font-mono">
                                            </div>
                                            <div>
                                                <label class="block font-medium mb-1">Tempat</label>
                                                <input type="text" name="tempat" value="{{ $jadwal->tempat }}" required maxlength="100" class="w-full rounded border px-2 py-1 text-xs bg-white">
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-end gap-2 pt-1">
                                            <button type="button" @click="editing = false" class="rounded border px-2.5 py-1 text-xs text-gray-600 hover:bg-gray-100">Batal</button>
                                            <button type="submit" class="rounded bg-blue-600 px-3 py-1 text-xs font-semibold text-white hover:bg-blue-700">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </td>
                            </template>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-4 text-center text-gray-500">Belum ada jadwal.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h3 class="mb-2 mt-4 font-medium">Tambah Jadwal</h3>
        <form method="POST" action="{{ route('pembina.jadwals.store', $eskul) }}" class="grid gap-3 sm:grid-cols-4">
            @csrf
            <div>
                <label class="mb-1 block text-xs font-medium">Hari</label>
                <select name="hari" class="w-full rounded border px-2 py-1.5 text-sm" required>
                    <option value="">Pilih hari</option>
                    @foreach (['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'MINGGU'] as $hari)
                        <option value="{{ $hari }}" @selected(old('hari') === $hari)>{{ ucfirst(strtolower($hari)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium">Jam Mulai</label>
                <input type="time" name="jam_mulai" value="{{ old('jam_mulai') }}" class="w-full rounded border px-2 py-1.5 text-sm" required>
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium">Jam Selesai</label>
                <input type="time" name="jam_selesai" value="{{ old('jam_selesai') }}" class="w-full rounded border px-2 py-1.5 text-sm" required>
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium">Tempat</label>
                <input type="text" name="tempat" value="{{ old('tempat') }}" maxlength="100" class="w-full rounded border px-2 py-1.5 text-sm" placeholder="Contoh: Lapangan A" required>
            </div>
            <div class="sm:col-span-4">
                <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan Jadwal</button>
            </div>
        </form>
    </div>

    <div class="rounded-lg border bg-white p-4 shadow-sm">
        <div class="mb-3">
            <h2 class="font-semibold">Pendaftar (Periode Aktif)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50 text-left">
                        <th class="px-3 py-2 font-medium">Nama</th>
                        <th class="px-3 py-2 font-medium">Kelas</th>
                        <th class="px-3 py-2 font-medium">Pilihan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendaftar as $daftar)
                        <tr class="border-b align-top last:border-0">
                            <td class="px-3 py-2 font-medium">{{ $daftar->user->name ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $daftar->kelas ?? '-' }}</td>
                            <td class="px-3 py-2 text-gray-600">
                                {{ $daftar->eskul_utama }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-3 py-4 text-center text-gray-500">Tidak ada pendaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-lg border bg-white p-4 shadow-sm">
        <h2 class="font-semibold mb-3">Absensi (hanya anggota disetujui)</h2>
        <form method="GET" action="{{ route('pembina.eskul.show', $eskul) }}" class="mb-3 flex flex-wrap gap-2 items-end text-sm">
            <div>
                <label class="block text-xs font-medium">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="rounded border px-2 py-1.5">
            </div>
            <button class="rounded border px-3 py-1.5 hover:bg-gray-50">Tampilkan</button>
            @if ($locked)
                <span class="text-xs font-semibold text-amber-700">Terkunci — tidak dapat diubah</span>
            @endif
        </form>
        @if ($disetujui->isEmpty())
            <p class="text-sm text-gray-500">Belum ada anggota disetujui.</p>
        @else
            <form method="POST" action="{{ route('pembina.kehadiran.store', $eskul) }}">
                @csrf
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50 text-left">
                                <th class="px-3 py-2 font-medium">Nama</th>
                                <th class="px-3 py-2 font-medium">Kelas</th>
                                <th class="px-3 py-2 font-medium">Status</th>
                                <th class="px-3 py-2 font-medium">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($disetujui as $r)
                                @php($ada = $peta->get($r->id))
                                <tr class="border-b last:border-0">
                                    <td class="px-3 py-2">{{ $r->user?->name }}</td>
                                    <td class="px-3 py-2">{{ $r->kelas }}</td>
                                    <td class="px-3 py-2">
                                        <select name="status[{{ $r->id }}]" class="rounded border px-2 py-1" @disabled($locked)>
                                            @foreach (['HADIR', 'IZIN', 'SAKIT', 'ALPA'] as $s)
                                                <option value="{{ $s }}" @selected(($ada?->status->value ?? 'HADIR') === $s)>{{ $s }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text" name="keterangan[{{ $r->id }}]" value="{{ $ada?->keterangan }}" maxlength="200" class="rounded border px-2 py-1 text-sm w-full" @disabled($locked)>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @unless ($locked)
                    <button class="mt-3 rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan Absensi ({{ $disetujui->count() }})</button>
                @endunless
            </form>
        @endif
    </div>

    <div class="rounded-lg border bg-white p-4 shadow-sm">
        <h2 class="font-semibold mb-1">Penilaian Akhir Anggota ({{ $disetujui->count() }})</h2>
        <p class="text-[11px] text-slate-400 mb-3">Predikat otomatis: A ≥ 85, B ≥ 70, C ≥ 55, D &lt; 55. Baris kosong dilewati.</p>
        <form method="POST" action="{{ route('pembina.nilai.store', $eskul) }}">
            @csrf
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50 text-left">
                            <th class="px-3 py-2 font-medium">Nama</th>
                            <th class="px-3 py-2 font-medium">Kelas</th>
                            <th class="px-3 py-2 font-medium">Nilai</th>
                            <th class="px-3 py-2 font-medium">Predikat</th>
                            <th class="px-3 py-2 font-medium">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($disetujui as $r)
                            <tr class="border-b last:border-0">
                                <td class="px-3 py-2">{{ $r->user?->name }}</td>
                                <td class="px-3 py-2">{{ $r->kelas }}</td>
                                <td class="px-3 py-2"><input type="number" name="nilai[{{ $r->id }}]" min="0" max="100" value="{{ $r->nilai }}" class="w-24 rounded border px-2 py-1"></td>
                                <td class="px-3 py-2">{{ $r->predikat ?? '—' }}</td>
                                <td class="px-3 py-2"><input type="text" name="catatan[{{ $r->id }}]" value="{{ $r->catatan_nilai }}" maxlength="1000" class="w-full rounded border px-2 py-1 text-sm"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button class="mt-3 rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan Semua Nilai</button>
        </form>
    </div>

    <div class="rounded-lg border bg-white p-4 shadow-sm">
        <div class="mb-3">
            <h2 class="font-semibold">Izin Menunggu Eskul Ini</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50 text-left">
                        <th class="px-3 py-2 font-medium">Nama</th>
                        <th class="px-3 py-2 font-medium">Tanggal</th>
                        <th class="px-3 py-2 font-medium">Jenis</th>
                        <th class="px-3 py-2 font-medium">Alasan</th>
                        <th class="px-3 py-2 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($izinPending as $izin)
                        <tr class="border-b last:border-0">
                            <td class="px-3 py-2">{{ $izin->registration->user->name ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $izin->tanggal->format('d-m-Y') }}</td>
                            <td class="px-3 py-2">{{ $izin->jenis instanceof \BackedEnum ? $izin->jenis->value : $izin->jenis }}</td>
                            <td class="px-3 py-2 text-gray-600">
                                {{ \Illuminate\Support\Str::limit($izin->alasan, 60) }}
                                @if ($izin->bukti)
                                    <br><a href="{{ asset('storage/' . $izin->bukti) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:underline mt-1 font-semibold"><x-icon name="file" class="h-3.5 w-3.5" /> Lihat Bukti</a>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <div class="flex gap-1.5">
                                    <form method="POST" action="{{ route('pembina.izin.approve', $izin) }}">
                                        @csrf
                                        <button class="rounded bg-green-600 px-2.5 py-1 text-xs font-medium text-white hover:bg-green-700">Setujui</button>
                                    </form>
                                    <form method="POST" action="{{ route('pembina.izin.reject', $izin) }}">
                                        @csrf
                                        <button class="rounded bg-red-600 px-2.5 py-1 text-xs font-medium text-white hover:bg-red-700">Tolak</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-4 text-center text-gray-500">Tidak ada izin menunggu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

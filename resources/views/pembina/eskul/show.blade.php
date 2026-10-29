@extends('layouts.staff')

@section('title', 'Detail Eskul — ' . $eskul->name)

@section('content')
<div class="space-y-6" x-data="{
    bukaModal: false,
    search: '',
    filterTingkat: 'all',
    filterKelas: 'all',
    selected: [],
    calonList: {{ Js::from($calonSiswa) }},
    get filteredList() {
        return this.calonList.filter(s => {
            if (this.filterTingkat !== 'all') {
                const k = (s.kelas || '').trim();
                if (!k.startsWith(this.filterTingkat)) return false;
            }
            if (this.filterKelas !== 'all') {
                if ((s.kelas || '') !== this.filterKelas) return false;
            }
            if (this.search.trim()) {
                const q = this.search.toLowerCase();
                const nama = (s.nama || '').toLowerCase();
                const nis = (s.nis || '').toLowerCase();
                if (!nama.includes(q) && !nis.includes(q)) return false;
            }
            return true;
        });
    },
    pilihSemua() {
        const visibleIds = this.filteredList.map(s => s.id);
        if (this.isSemuaTerpilih()) {
            this.selected = this.selected.filter(id => !visibleIds.includes(id));
        } else {
            this.selected = [...new Set([...this.selected, ...visibleIds])];
        }
    },
    isSemuaTerpilih() {
        const visibleIds = this.filteredList.map(s => s.id);
        return visibleIds.length > 0 && visibleIds.every(id => this.selected.includes(id));
    }
}">
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
                <button type="button" @click="bukaModal = true" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-blue-700 transition shadow-sm cursor-pointer">
                    <x-icon name="plus" class="h-3.5 w-3.5" />
                    <span>+ Tambah Anggota</span>
                </button>
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
                                        <button type="button" @click="editing = true" class="inline-flex items-center justify-center h-7 w-7 rounded-lg border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100 transition shadow-2xs" title="Ubah Jadwal">
                                            <x-icon name="pencil" class="h-3.5 w-3.5" />
                                        </button>
                                        <form method="POST" action="{{ route('pembina.jadwals.destroy', $jadwal) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center h-7 w-7 rounded-lg border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 transition shadow-2xs cursor-pointer" title="Hapus Jadwal">
                                                <x-icon name="trash" class="h-3.5 w-3.5" />
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
                                                <select name="hari" required class="w-full rounded-lg border border-slate-300 pl-2 pr-7 py-1 text-xs bg-white focus:border-blue-600 focus:outline-hidden">
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
                <select name="hari" class="w-full rounded-xl border border-slate-300 pl-3 pr-8 py-2 text-xs sm:text-sm bg-white focus:border-blue-600 focus:outline-hidden" required>
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
        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="font-semibold">Daftar Pertemuan</h2>
                <p class="text-[11px] text-slate-400">Pertemuan dibuat saat latihan dimulai — tanggal & nomor terisi otomatis hari ini.</p>
            </div>
            <form method="POST" action="{{ route('pembina.pertemuan.store', $eskul) }}" class="flex flex-wrap items-center gap-2">
                @csrf
                <input type="text" name="keterangan" maxlength="200" placeholder="Keterangan (opsional)" class="rounded border px-2 py-1.5 text-sm w-48">
                <button class="rounded-xl bg-emerald-700 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-800">+ Buat Pertemuan Hari Ini</button>
            </form>
        </div>
        @error('tanggal')
            <p class="mb-2 text-xs font-medium text-rose-600">{{ $message }}</p>
        @enderror
        @error('pertemuan')
            <p class="mb-2 text-xs font-medium text-rose-600">{{ $message }}</p>
        @enderror
        @if ($pertemuans->isEmpty())
            <p class="text-sm text-gray-500">Belum ada pertemuan. Buat Pertemuan 1 saat latihan pertama dimulai.</p>
        @else
            <div class="grid gap-2 sm:grid-cols-2">
                @foreach ($pertemuans as $p)
                    <div class="flex items-center gap-3 rounded-2xl border border-slate-200 p-3 {{ $p->isLocked() ? 'bg-slate-50' : 'bg-white' }}">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $p->isLocked() ? 'bg-slate-200 text-slate-500' : 'bg-blue-100 text-blue-800' }} text-sm font-extrabold">
                            {{ $p->nomor }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-slate-900">Pertemuan {{ $p->nomor }}</p>
                            <p class="text-[11px] text-slate-500">{{ $p->tanggal->format('l, d M Y') }}{{ $p->keterangan ? ' • '.$p->keterangan : '' }}</p>
                            <p class="text-[11px] {{ $p->isLocked() ? 'text-slate-400' : 'text-emerald-600' }}">
                                {{ $p->isLocked() ? 'Disahkan' : $p->hadir_count.' hadir' }}
                            </p>
                        </div>
                        <div class="flex shrink-0 flex-col gap-1">
                            @unless ($p->isLocked())
                                <a href="{{ route('pembina.scan.show', ['eskul' => $eskul->id, 'tanggal' => $p->tanggal->toDateString()]) }}" class="inline-flex items-center gap-1 rounded-lg bg-blue-700 px-2.5 py-1 text-[11px] font-bold text-white hover:bg-blue-800">
                                    <x-icon name="qr" class="h-3 w-3" /> Scan
                                </a>
                                <a href="{{ route('pembina.eskul.show', ['eskul' => $eskul->id, 'tanggal' => $p->tanggal->toDateString()]) }}" class="rounded-lg border px-2.5 py-1 text-center text-[11px] font-semibold hover:bg-slate-50">Absen</a>
                            @endunless
                            @if (! $p->isLocked())
                                <form method="POST" action="{{ route('pembina.kehadiran.lock', $eskul) }}" onsubmit="return confirm('Sahkan Pertemuan {{ $p->nomor }}? Data terkunci dan tidak dapat diubah.')">
                                    @csrf
                                    <input type="hidden" name="eskul_id" value="{{ $eskul->id }}">
                                    <input type="hidden" name="tanggal" value="{{ $p->tanggal->toDateString() }}">
                                    <button class="w-full rounded-lg border px-2.5 py-1 text-[11px] font-semibold hover:bg-slate-50">Sahkan</button>
                                </form>
                                <form method="POST" action="{{ route('pembina.pertemuan.destroy', $p) }}" onsubmit="return confirm('Hapus Pertemuan {{ $p->nomor }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="w-full inline-flex items-center justify-center gap-1 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1 text-[11px] font-semibold text-rose-600 hover:bg-rose-100 transition shadow-2xs cursor-pointer" title="Hapus Pertemuan">
                                        <x-icon name="trash" class="h-3 w-3" /> Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Presensi Anggota Eskul (Per Rombel Kelas)</h2>
                <p class="text-[11px] text-slate-500">Urutan siswa dimulai dari Kelas 10 lalu Kelas 11, dikelompokkan per rombel.</p>
            </div>
            <a href="{{ route('pembina.scan.show', $eskul) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-700 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-blue-800">
                <x-icon name="qr" class="h-4 w-4" /> Scan Kartu Pelajar
            </a>
        </div>

        {{-- Ringkasan Status per Rombel --}}
        @if (!empty($rombelStats) && $rombelStats->isNotEmpty())
            <div class="mb-4 p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                <h3 class="text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2">Status Presensi Rombel:</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach ($rombelStats as $stat)
                        <div class="p-2 rounded-lg border bg-white text-xs {{ $stat['lengkap'] ? 'border-emerald-200' : ($stat['terisi'] > 0 ? 'border-amber-200' : 'border-rose-200') }}">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800">{{ $stat['kelas'] }}</span>
                                @if ($stat['lengkap'])
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">Lengkap</span>
                                @else
                                    <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded">{{ $stat['belum'] }} Belum</span>
                                @endif
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Hadir: {{ $stat['hadir'] }}/{{ $stat['total'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="GET" action="{{ route('pembina.eskul.show', $eskul) }}" class="mb-4 flex flex-wrap gap-2 items-end text-xs">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Kegiatan</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="rounded-xl border-slate-300 px-3 py-1.5 text-xs focus:border-blue-500 focus:ring-blue-500">
            </div>
            <button class="rounded-xl bg-slate-900 px-4 py-1.5 text-white font-bold hover:bg-slate-800 cursor-pointer">Tampilkan</button>
            @if ($locked)
                <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2 py-1 rounded-lg border border-amber-200">Presensi Terkunci (Disahkan)</span>
            @endif
        </form>

        @if ($disetujui->isEmpty())
            <p class="text-xs text-slate-400 py-4 text-center">Belum ada anggota yang disetujui untuk eskul ini.</p>
        @else
            <form method="POST" action="{{ route('pembina.kehadiran.store', $eskul) }}">
                @csrf
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-left font-bold text-slate-700">
                            <tr>
                                <th class="px-3 py-2.5">Nama Siswa</th>
                                <th class="px-3 py-2.5">NIS</th>
                                <th class="px-3 py-2.5">Kelas</th>
                                <th class="px-3 py-2.5">Status Presensi</th>
                                <th class="px-3 py-2.5">Keterangan</th>
                                <th class="px-3 py-2.5 text-center w-12">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $grouped = $disetujui->groupBy('kelas');
                            @endphp
                            @foreach ($grouped as $namaKelas => $list)
                                <tr class="bg-slate-100/70">
                                    <td colspan="6" class="px-3 py-1.5 font-bold text-slate-800 text-[11px]">
                                        Rombel: {{ $namaKelas }} ({{ $list->count() }} Siswa)
                                    </td>
                                </tr>
                                @foreach ($list as $r)
                                    @php($ada = $peta->get($r->id))
                                    <tr class="hover:bg-slate-50/60">
                                        <td class="px-3 py-2 font-medium text-slate-900">{{ $r->user?->name }}</td>
                                        <td class="px-3 py-2 text-slate-600 font-mono">{{ $r->nis ?? '-' }}</td>
                                        <td class="px-3 py-2 text-slate-700">{{ $r->kelas }}</td>
                                        <td class="px-3 py-2">
                                            <select name="status[{{ $r->id }}]" class="rounded-xl border-slate-300 py-1.5 pl-2.5 pr-8 text-xs font-bold text-slate-800 bg-white shadow-2xs focus:border-blue-500 focus:ring-blue-500 cursor-pointer" @disabled($locked)>
                                                <option value="HADIR" class="font-bold text-emerald-700" @selected(($ada?->status->value ?? 'HADIR') === 'HADIR')>Hadir</option>
                                                <option value="IZIN" class="font-bold text-blue-700" @selected(($ada?->status->value ?? 'HADIR') === 'IZIN')>Izin</option>
                                                <option value="SAKIT" class="font-bold text-amber-700" @selected(($ada?->status->value ?? 'HADIR') === 'SAKIT')>Sakit</option>
                                                <option value="ALPA" class="font-bold text-rose-700" @selected(($ada?->status->value ?? 'HADIR') === 'ALPA')>Alpa</option>
                                            </select>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="text" name="keterangan[{{ $r->id }}]" value="{{ $ada?->keterangan }}" maxlength="200" placeholder="Keterangan izin/sakit..." class="rounded-xl border-slate-300 px-3 py-1.5 text-xs w-full max-w-xs shadow-2xs focus:border-blue-500 focus:ring-blue-500" @disabled($locked)>
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <button type="button" onclick="confirmHapusAnggota('{{ route('pembina.anggota.destroy', [$eskul, $r]) }}', '{{ addslashes($r->user?->name ?? $r->nis) }}')" class="inline-flex items-center justify-center h-7 w-7 rounded-lg border border-slate-200 bg-white text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 transition shadow-2xs cursor-pointer" title="Keluarkan dari Eskul">
                                                <x-icon name="trash" class="h-3.5 w-3.5" />
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @unless ($locked)
                    <div class="mt-4 flex justify-end">
                        <button class="rounded-xl bg-blue-600 px-6 py-2.5 text-xs font-bold text-white shadow-md shadow-blue-600/20 hover:bg-blue-700 transition-colors cursor-pointer">
                            Simpan Presensi ({{ $disetujui->count() }} Anggota)
                        </button>
                    </div>
                @endunless
            </form>
        @endif
    </div>

    <div class="rounded-lg border bg-white p-4 shadow-sm">
        <h2 class="font-semibold mb-1">Penilaian Akhir Anggota ({{ $disetujui->count() }})</h2>
        <p class="text-[11px] text-slate-400 mb-3">Isi nilai satu per satu lalu tekan Simpan di barisnya. Predikat otomatis: A ≥ 85, B ≥ 70, C ≥ 55, D &lt; 55. Nilai langsung terlihat di panel admin.</p>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50 text-left">
                        <th class="px-3 py-2 font-medium">Nama</th>
                        <th class="px-3 py-2 font-medium">Kelas</th>
                        <th class="px-3 py-2 font-medium">Nilai</th>
                        <th class="px-3 py-2 font-medium">Predikat</th>
                        <th class="px-3 py-2 font-medium">Catatan</th>
                        <th class="px-3 py-2 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($disetujui as $r)
                        <tr class="border-b last:border-0">
                            <td class="px-3 py-2">{{ $r->user?->name }}</td>
                            <td class="px-3 py-2">{{ $r->kelas }}</td>
                            <td class="px-3 py-2"><input type="number" form="nilai-{{ $r->id }}" name="nilai" min="0" max="100" value="{{ $r->nilai }}" placeholder="0–100" class="w-24 rounded border px-2 py-1"></td>
                            <td class="px-3 py-2">{{ $r->predikat ?? '—' }}</td>
                            <td class="px-3 py-2"><input type="text" form="nilai-{{ $r->id }}" name="catatan" value="{{ $r->catatan_nilai }}" maxlength="1000" placeholder="Opsional" class="w-full rounded border px-2 py-1 text-sm"></td>
                            <td class="px-3 py-2">
                                <form id="nilai-{{ $r->id }}" method="POST" action="{{ route('pembina.nilai.store', $eskul) }}">
                                    @csrf
                                    <input type="hidden" name="registration_id" value="{{ $r->id }}">
                                    <button class="rounded bg-blue-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-blue-700">Simpan</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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

    {{-- MODAL + TAMBAH ANGGOTA SISWA (MANUAL) --}}
    <div x-show="bukaModal"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         style="display: none;"
         @keydown.escape.window="bukaModal = false">

        <div class="w-full max-w-3xl rounded-3xl bg-white shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh]"
             @click.outside="bukaModal = false">

            {{-- Header Modal --}}
            <div class="flex items-center justify-between p-5 border-b border-slate-100 bg-slate-50/70">
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                        <x-icon name="user" class="h-5 w-5" />
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Tambah Anggota ke Eskul {{ $eskul->name }}</h3>
                        <p class="text-xs text-slate-500">Pendaftaran manual bagi siswa yang belum memilih eskul (tidak memiliki HP atau kuota).</p>
                    </div>
                </div>
                <button type="button" @click="bukaModal = false" class="rounded-xl p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                    <x-icon name="x" class="h-5 w-5" />
                </button>
            </div>

            {{-- Catatan Panduan --}}
            <div class="p-4 bg-blue-50/70 border-b border-blue-100 text-xs text-blue-900 flex items-start gap-2.5">
                <x-icon name="info" class="h-4 w-4 text-blue-600 shrink-0 mt-0.5" />
                <span><b>Perhatian Pembina:</b> Menambahkan siswa secara manual akan langsung menyetujui pendaftaran siswa ke ekstrakurikuler ini untuk periode aktif saat ini.</span>
            </div>

            {{-- Filter & Search Controls --}}
            <div class="p-4 border-b border-slate-100 bg-white grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tingkat Kelas</label>
                    <select x-model="filterTingkat" class="w-full border-slate-300 rounded-xl py-2 px-3 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:ring-blue-500">
                        <option value="all">Semua Tingkat</option>
                        <option value="10">Kelas 10 (Sepuluh)</option>
                        <option value="11">Kelas 11 (Sebelas)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Rombel / Kelas</label>
                    <select x-model="filterKelas" class="w-full border-slate-300 rounded-xl py-2 px-3 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:ring-blue-500">
                        <option value="all">Semua Kelas</option>
                        @foreach ($daftarKelasCalon as $kc)
                            <option value="{{ $kc }}">{{ $kc }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Cari Nama atau NIS</label>
                    <div class="relative">
                        <input type="text" x-model="search" placeholder="Ketik nama atau NIS..." class="w-full border-slate-300 rounded-xl py-2 pl-8 pr-3 text-xs text-slate-800 focus:border-blue-500 focus:ring-blue-500">
                        <div class="absolute left-2.5 top-2.5 text-slate-400 pointer-events-none">
                            <x-icon name="search" class="h-3.5 w-3.5" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form & Tabel Calon Siswa --}}
            <form method="POST" action="{{ route('pembina.anggota.store', $eskul) }}" class="flex-1 flex flex-col min-h-0">
                @csrf

                <div class="p-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between text-xs">
                    <button type="button" @click="pilihSemua()" class="font-bold text-blue-600 hover:text-blue-800 hover:underline cursor-pointer">
                        <span x-text="isSemuaTerpilih() ? 'Batalkan Pilih Semua' : 'Centang Semua yang Ditampilkan'"></span>
                    </button>
                    <span class="font-bold text-slate-600">
                        <span x-text="selected.length" class="text-blue-600"></span> siswa dipilih
                    </span>
                </div>

                <div class="flex-1 overflow-y-auto p-4 min-h-[220px]">
                    <table class="min-w-full text-xs">
                        <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider font-bold text-[11px] sticky top-0 z-10 shadow-2xs">
                            <tr>
                                <th class="py-2 px-3 text-left">NIS</th>
                                <th class="py-2 px-3 text-left">Nama Siswa</th>
                                <th class="py-2 px-3 text-center">L/P</th>
                                <th class="py-2 px-3 text-left">Kelas</th>
                                <th class="py-2 px-3 text-center w-12">Pilih</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="s in filteredList" :key="s.id">
                                <tr class="hover:bg-blue-50/40 transition-colors" :class="selected.includes(s.id) ? 'bg-blue-50/60 font-semibold' : ''">
                                    <td class="py-2 px-3 font-mono font-bold text-slate-800" x-text="s.nis"></td>
                                    <td class="py-2 px-3 text-slate-900" x-text="s.nama"></td>
                                    <td class="py-2 px-3 text-center text-slate-500 font-bold" x-text="s.gender ? (s.gender.value ? s.gender.value[0] : s.gender[0]) : '-'"></td>
                                    <td class="py-2 px-3">
                                        <span x-show="s.kelas" class="inline-flex items-center px-2 py-0.5 rounded-md font-semibold bg-slate-100 text-slate-600 text-[10px]" x-text="s.kelas"></span>
                                        <span x-show="!s.kelas" class="inline-flex items-center px-2 py-0.5 rounded-md font-semibold bg-amber-50 text-amber-700 text-[10px]">Belum Ada Kelas</span>
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        <input type="checkbox" name="student_ids[]" :value="s.id" x-model="selected" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 h-4 w-4 cursor-pointer">
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="filteredList.length === 0">
                                <td colspan="5" class="py-8 text-center text-slate-400">
                                    Tidak ada data calon siswa yang cocok dengan filter atau pencarian.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Footer Modal --}}
                <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
                    <span class="text-xs text-slate-500">
                        Total calon siswa belum memilih eskul: <b>{{ $calonSiswa->count() }}</b>
                    </span>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="bukaModal = false" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                :disabled="selected.length === 0"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-5 py-2 text-xs font-bold text-white hover:bg-blue-700 transition shadow-xs disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                            <x-icon name="plus" class="h-3.5 w-3.5" />
                            <span>Daftarkan <span x-text="selected.length > 0 ? selected.length : ''"></span> Siswa Terpilih</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Form Hidden untuk Aksi Hapus/Keluarkan Anggota --}}
    <form id="form-hapus-anggota" method="POST" action="" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>

<script>
function confirmHapusAnggota(url, nama) {
    if (confirm(`Keluarkan siswa "${nama}" dari ekstrakurikuler ini?\n\nSiswa akan dapat memilih eskul kembali.`)) {
        const form = document.getElementById('form-hapus-anggota');
        form.action = url;
        form.submit();
    }
}
</script>
@endsection

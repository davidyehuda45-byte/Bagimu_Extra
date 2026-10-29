@extends('layouts.staff')

@section('title', 'Periode & Jendela Pendaftaran')

@section('content')
<div class="space-y-6">
    {{-- Header Halaman & Pemilih Dropdown Periode --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Periode & Jendela Pendaftaran</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola jendela pendaftaran, jadwal otomatis, batas kuota eskul, dan arsip riwayat akademik.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Dropdown Pemilih Periode & Arsip --}}
            <form method="GET" action="{{ route('admin.periodes.index') }}" class="flex items-center gap-2">
                <label for="periode-selector" class="text-xs font-semibold text-slate-600 shrink-0">Pilih Periode / Arsip:</label>
                <div class="relative">
                    <select id="periode-selector" name="periode" onchange="this.form.submit()" class="rounded-xl border-slate-300 py-1.5 pl-3 pr-8 text-xs font-bold text-slate-800 bg-white shadow-2xs focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        @foreach ($allPeriodes as $p)
                            @php
                                $statusBadge = match($p->status->value) {
                                    'DIBUKA' => 'Aktif (Berjalan)',
                                    'DITUTUP' => 'Ditutup',
                                    'DIARSIPKAN' => 'Arsip Riwayat',
                                    'TERJADWAL' => 'Terjadwal',
                                    default => $p->status->label(),
                                };
                            @endphp
                            <option value="{{ $p->periode }}" @selected($selectedPeriode && $selectedPeriode->periode === $p->periode)>
                                {{ $p->periode }} {{ $p->nama ? "({$p->nama})" : '' }} — {{ $statusBadge }} ({{ $p->registrations_count }} siswa)
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            <a href="{{ route('admin.periodes.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-blue-700 transition shadow-2xs cursor-pointer">
                <x-icon name="plus" class="h-3.5 w-3.5" />
                <span>Tambah Periode</span>
            </a>
        </div>
    </div>

    @if ($selectedPeriode)
        @php
            $isAktif = $selectedPeriode->status->value === 'DIBUKA';
            $isArsip = $selectedPeriode->status->value === 'DIARSIPKAN';
            $isDitutup = $selectedPeriode->status->value === 'DITUTUP';
            $buka = $window && $window->exists && $window->isOpen();
        @endphp

        {{-- Detail Periode Terpilih & Kontrol Jendela (Langsung Tampil di Tempat) --}}
        <div class="space-y-4">
            {{-- Panel Utama Periode Terpilih --}}
            <div class="rounded-2xl border bg-white p-5 shadow-sm {{ $isAktif ? 'border-blue-200' : ($isArsip ? 'border-slate-300' : 'border-amber-200') }}">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 border-b border-slate-100">
                    <div class="flex items-start sm:items-center gap-3.5">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $isAktif ? 'bg-blue-600 text-white' : ($isArsip ? 'bg-slate-200 text-slate-700' : 'bg-amber-500 text-white') }} shadow-2xs">
                            <x-icon name="calendar" class="h-5 w-5" />
                        </span>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-base sm:text-lg font-bold text-slate-900">Periode {{ $selectedPeriode->periode }}</h2>
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-bold {{ $isAktif ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($isArsip ? 'bg-slate-200 text-slate-700 border border-slate-300' : 'bg-amber-100 text-amber-800 border border-amber-300') }}">
                                    {{ $isAktif ? 'Periode Aktif' : ($isArsip ? 'Arsip Riwayat' : ($isDitutup ? 'Periode Ditutup' : $selectedPeriode->status->label())) }}
                                </span>
                                @if ($activePeriode && $activePeriode->periode === $selectedPeriode->periode)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-800 border border-blue-200">
                                        Sedang Berjalan
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $selectedPeriode->nama ?? 'Tanpa nama label' }} • Tahun Ajaran: <strong>{{ $selectedPeriode->tahun_ajaran ?? '-' }}</strong> • Semester: <strong>{{ $selectedPeriode->semester ?? '-' }}</strong>
                            </p>
                        </div>
                    </div>

                    {{-- Tombol Aksi Cepat Periode Terpilih --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.rekap.index', ['periode' => $selectedPeriode->periode]) }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition shadow-2xs cursor-pointer">
                            <x-icon name="trophy" class="h-3.5 w-3.5 text-amber-600" />
                            <span>Lihat Rekap Eskul</span>
                        </a>

                        <a href="{{ route('admin.periodes.edit', $selectedPeriode->periode) }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-amber-50 hover:text-amber-700 hover:border-amber-200 transition shadow-2xs cursor-pointer">
                            <x-icon name="pencil" class="h-3.5 w-3.5 text-amber-600" />
                            <span>Ubah Pengaturan</span>
                        </a>

                        @if (! $isAktif)
                            <form method="POST" action="{{ route('admin.periodes.destroy', $selectedPeriode->periode) }}" onsubmit="return confirm('Hapus seluruh riwayat pendaftaran periode {{ $selectedPeriode->periode }}? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100 transition shadow-2xs cursor-pointer">
                                    <x-icon name="trash" class="h-3.5 w-3.5 text-rose-600" />
                                    <span>Hapus Periode</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Informasi Metadata Detail Periode --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 py-3.5 text-xs">
                    <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
                        <span class="block text-slate-400 font-medium">Batas Maks. Eskul</span>
                        <span class="text-sm font-bold text-slate-800 mt-0.5 block">{{ $selectedPeriode->max_eskul_per_siswa }} Eskul / Siswa</span>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
                        <span class="block text-slate-400 font-medium">Tanggal Mulai</span>
                        <span class="text-sm font-bold text-slate-800 mt-0.5 block">{{ $selectedPeriode->tgl_mulai ? $selectedPeriode->tgl_mulai->format('d M Y') : 'Belum diatur' }}</span>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
                        <span class="block text-slate-400 font-medium">Tanggal Selesai</span>
                        <span class="text-sm font-bold text-slate-800 mt-0.5 block">{{ $selectedPeriode->tgl_selesai ? $selectedPeriode->tgl_selesai->format('d M Y') : 'Belum diatur' }}</span>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
                        <span class="block text-slate-400 font-medium">Status Pendaftaran Siswa</span>
                        <span class="text-sm font-bold mt-0.5 block {{ $buka ? 'text-emerald-700' : 'text-rose-600' }}">
                            {{ $buka ? 'Dibuka Sekarang' : 'Terkunci / Ditutup' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Kartu Metrik Statistik Periode Terpilih --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs">
                    <div class="flex items-center justify-between text-slate-500 mb-1">
                        <span class="text-xs font-semibold">Total Pendaftar</span>
                        <x-icon name="users" class="h-4 w-4 text-blue-600" />
                    </div>
                    <p class="text-xl sm:text-2xl font-black text-slate-900">{{ number_format($totalPendaftar) }}</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Siswa terdaftar di periode ini</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs">
                    <div class="flex items-center justify-between text-slate-500 mb-1">
                        <span class="text-xs font-semibold">Disetujui</span>
                        <x-icon name="check" class="h-4 w-4 text-emerald-600" />
                    </div>
                    <p class="text-xl sm:text-2xl font-black text-emerald-700">{{ number_format($totalDisetujui) }}</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Terverifikasi oleh pembina</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs">
                    <div class="flex items-center justify-between text-slate-500 mb-1">
                        <span class="text-xs font-semibold">Eskul Aktif</span>
                        <x-icon name="trophy" class="h-4 w-4 text-amber-600" />
                    </div>
                    <p class="text-xl sm:text-2xl font-black text-slate-900">{{ number_format($totalEskulAktif) }}</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Eskul memiliki pendaftar</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs">
                    <div class="flex items-center justify-between text-slate-500 mb-1">
                        <span class="text-xs font-semibold">Partisipasi Siswa</span>
                        <x-icon name="chart" class="h-4 w-4 text-indigo-600" />
                    </div>
                    <p class="text-xl sm:text-2xl font-black text-indigo-700">{{ $rasioPartisipasi }}%</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Dari {{ number_format($totalSiswaAktif) }} total siswa aktif</p>
                </div>
            </div>

            {{-- Kartu Kontrol Jendela Pendaftaran Periode Terpilih --}}
            <div class="rounded-2xl border p-5 shadow-sm {{ $buka ? 'border-emerald-200 bg-emerald-50/70' : 'border-rose-200 bg-rose-50/70' }}">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex h-2.5 w-2.5 rounded-full {{ $buka ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                            <p class="font-extrabold text-sm sm:text-base {{ $buka ? 'text-emerald-900' : 'text-rose-800' }}">
                                JENDELA PENDAFTARAN {{ $buka ? 'DIBUKA — Siswa dapat memilih eskul' : 'DITUTUP — Formulir terkunci' }}
                            </p>
                        </div>
                        <p class="text-xs mt-1 {{ $buka ? 'text-emerald-700' : 'text-rose-600' }}">
                            Periode: <strong>{{ $selectedPeriode->periode }}</strong> • {{ $window && $window->exists ? $window->label() : 'Jendela belum diatur (status terkunci)' }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('admin.windows.update', $selectedPeriode->periode) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="buka" value="{{ $buka ? '0' : '1' }}">
                        @if ($window && $window->exists)
                            <input type="hidden" name="tgl_buka" value="{{ $window->tgl_buka?->format('Y-m-d\TH:i') }}">
                            <input type="hidden" name="tgl_tutup" value="{{ $window->tgl_tutup?->format('Y-m-d\TH:i') }}">
                        @endif
                        <button type="submit" class="w-full sm:w-auto rounded-xl px-5 py-2.5 text-xs sm:text-sm font-bold text-white shadow-sm transition cursor-pointer {{ $buka ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                            {{ $buka ? 'Tutup Pendaftaran Sekarang' : 'Buka Pendaftaran Sekarang' }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Form Penjadwalan Otomatis (Opsional) --}}
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-5 shadow-2xs">
                <div class="flex items-center gap-2 mb-1">
                    <x-icon name="clock" class="h-4 w-4 text-slate-600" />
                    <h3 class="font-bold text-slate-900 text-sm">Penjadwalan Otomatis Jendela (Periode {{ $selectedPeriode->periode }})</h3>
                </div>
                <p class="text-xs text-slate-500 mb-3">Atur rentang waktu otomatis buka dan tutup formulir pendaftaran siswa. Di luar jadwal ini formulir akan terkunci secara mandiri.</p>

                <form method="POST" action="{{ route('admin.windows.update', $selectedPeriode->periode) }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs sm:text-sm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="buka" value="1">
                    <div class="sm:col-span-2">
                        <label class="block font-medium text-slate-700 mb-1">Buka Mulai</label>
                        <input type="datetime-local" name="tgl_buka" value="{{ old('tgl_buka', $window?->tgl_buka?->format('Y-m-d\TH:i')) }}" class="w-full border-slate-300 rounded-xl text-xs sm:text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block font-medium text-slate-700 mb-1">Tutup Pada</label>
                        <input type="datetime-local" name="tgl_tutup" value="{{ old('tgl_tutup', $window?->tgl_tutup?->format('Y-m-d\TH:i')) }}" class="w-full border-slate-300 rounded-xl text-xs sm:text-sm">
                    </div>
                    <div class="flex items-center gap-3 sm:col-span-4 mt-1">
                        <button type="submit" class="px-4 py-2 rounded-xl bg-blue-700 text-white font-bold hover:bg-blue-800 text-xs shadow-sm transition cursor-pointer">
                            Simpan Jadwal Otomatis
                        </button>
                        @if ($window && $window->exists && ($window->tgl_buka || $window->tgl_tutup))
                            <button type="submit" form="hapus-jadwal-form" class="text-xs font-semibold text-rose-600 hover:underline cursor-pointer">
                                Hapus Jadwal
                            </button>
                        @endif
                    </div>
                </form>

                @if ($window && $window->exists && ($window->tgl_buka || $window->tgl_tutup))
                    <form id="hapus-jadwal-form" method="POST" action="{{ route('admin.windows.update', $selectedPeriode->periode) }}" class="hidden">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="buka" value="{{ $window->buka ? '1' : '0' }}">
                    </form>
                @endif
            </div>
        </div>
    @endif

    {{-- Tabel Ringkasan Komparasi Semua Periode & Riwayat Arsip --}}
    <div class="space-y-3 pt-2">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Riwayat & Daftar Seluruh Periode</h2>
                <p class="text-xs text-slate-500">Daftar arsip seluruh tahun ajaran akademik yang tercatat di sistem.</p>
            </div>
            <a href="{{ route('admin.periodes.create') }}" class="px-3.5 py-1.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 shadow-2xs transition">
                + Tambah Periode Baru
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 text-left text-slate-500">
                            <th class="py-3 px-4 font-semibold">Periode</th>
                            <th class="py-3 px-4 font-semibold">Nama Label</th>
                            <th class="py-3 px-4 font-semibold">Tahun / Semester</th>
                            <th class="py-3 px-4 font-semibold">Status Akademik</th>
                            <th class="py-3 px-4 font-semibold">Jendela Pendaftaran</th>
                            <th class="py-3 px-4 font-semibold">Total Pendaftar</th>
                            <th class="py-3 px-4 font-semibold text-right">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($periodes as $p)
                            @php
                                $isThisSelected = $selectedPeriode && $selectedPeriode->periode === $p->periode;
                                $w = $p->window;
                                $isOpen = $w && $w->isOpen();
                            @endphp
                            <tr class="transition {{ $isThisSelected ? 'bg-blue-50/50 font-semibold' : 'hover:bg-slate-50/70' }}">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2">
                                        @if ($isThisSelected)
                                            <span class="inline-block h-2 w-2 rounded-full bg-blue-600"></span>
                                        @endif
                                        <span class="font-bold text-slate-900">{{ $p->periode }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-slate-700">{{ $p->nama ?? '-' }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ $p->tahun_ajaran ?? '-' }} • {{ $p->semester ?? '-' }}</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold {{ $p->status->value === 'DIBUKA' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : ($p->status->value === 'DIARSIPKAN' ? 'bg-slate-200 text-slate-700 border border-slate-300' : 'bg-amber-100 text-amber-800 border border-amber-200') }}">
                                        {{ $p->status->value === 'DIBUKA' ? 'Aktif' : $p->status->label() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    @if ($isOpen)
                                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Buka
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                            Tutup
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <span class="font-bold text-slate-800">{{ number_format($p->registrations_count) }}</span>
                                    <span class="text-[11px] text-slate-400 font-normal">siswa</span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.periodes.index', ['periode' => $p->periode]) }}" title="Pilih & Tampilkan Rincian Periode Ini" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border text-xs font-bold transition shadow-2xs {{ $isThisSelected ? 'border-blue-300 bg-blue-600 text-white' : 'border-slate-200 bg-white text-slate-600 hover:text-blue-600 hover:bg-blue-50' }}">
                                            <x-icon name="eye" class="h-3.5 w-3.5" />
                                            <span>{{ $isThisSelected ? 'Terpilih' : 'Pilih' }}</span>
                                        </a>

                                        <a href="{{ route('admin.rekap.index', ['periode' => $p->periode]) }}" title="Lihat Rekapitulasi Eskul Periode Ini" class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-amber-600 hover:bg-amber-50 hover:border-amber-200 transition shadow-2xs">
                                            <x-icon name="trophy" class="h-4 w-4" />
                                        </a>

                                        <a href="{{ route('admin.periodes.edit', $p->periode) }}" title="Ubah Pengaturan Periode" class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition shadow-2xs">
                                            <x-icon name="pencil" class="h-4 w-4" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-6 px-4 text-center text-slate-400">Belum ada periode yang terdaftar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($periodes->hasPages())
                <div class="p-4 border-t border-slate-100">{{ $periodes->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

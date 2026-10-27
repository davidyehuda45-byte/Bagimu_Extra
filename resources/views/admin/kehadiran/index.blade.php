@extends('layouts.staff')

@section('title', 'Presensi Siswa')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Presensi Ekstrakurikuler</h1>
            <p class="text-xs text-slate-500">Kelola dan pantau presensi anggota eskul berbasis rombel kelas (dimulai dari Kelas 10 lalu Kelas 11).</p>
        </div>
        @if ($eskul)
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                    <x-icon name="calendar" class="h-3.5 w-3.5" />
                    Periode: {{ $periodeAktif?->periode ?? 'Semua' }}
                </span>
            </div>
        @endif
    </div>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('admin.kehadiran.index') }}" class="flex flex-wrap gap-3 items-end bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
        <div class="flex-1 min-w-[200px]">
            <label for="eskul_id" class="block text-xs font-bold text-slate-700 mb-1">Pilih Ekstrakurikuler</label>
            <select name="eskul_id" id="eskul_id" class="w-full border-slate-300 rounded-xl text-xs py-2 focus:border-blue-500 focus:ring-blue-500 font-medium">
                @foreach ($eskuls as $e)
                    <option value="{{ $e->id }}" @selected($eskul && $eskul->id === $e->id)>{{ $e->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="tanggal" class="block text-xs font-bold text-slate-700 mb-1">Tanggal Kegiatan</label>
            <input type="date" name="tanggal" id="tanggal" value="{{ $tanggal }}" class="border-slate-300 rounded-xl text-xs py-2 focus:border-blue-500 focus:ring-blue-500 font-medium">
        </div>
        <button type="submit" class="px-5 py-2 rounded-xl bg-blue-700 text-white text-xs font-bold hover:bg-blue-800 transition-colors cursor-pointer">
            Tampilkan Data
        </button>
    </form>

    @if (! $eskul)
        <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center text-xs text-slate-500">
            Belum ada data ekstrakurikuler yang aktif.
        </div>
    @else
        {{-- Ringkasan Status Presensi per Rombel Kelas --}}
        @if ($rombelStats->isNotEmpty())
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700">
                            <x-icon name="chart-bar" class="h-4 w-4" />
                        </span>
                        <div>
                            <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Pemantauan Presensi Per Rombel Kelas</h2>
                            <p class="text-[11px] text-slate-400">Menampilkan kelas mana saja yang siswa anggotanya belum melakukan presensi hari ini</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold text-slate-500">{{ $pendaftar->count() }} Total Anggota Terdaftar</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5">
                    @foreach ($rombelStats as $stat)
                        <div class="rounded-xl border p-3 transition-all {{ $stat['lengkap'] ? 'border-emerald-200 bg-emerald-50/50' : ($stat['terisi'] > 0 ? 'border-amber-200 bg-amber-50/40' : 'border-rose-200 bg-rose-50/40') }}">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-xs text-slate-900 font-mono">{{ $stat['kelas'] }}</span>
                                @if ($stat['lengkap'])
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Lengkap</span>
                                @elseif ($stat['terisi'] > 0)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">{{ $stat['belum'] }} Belum</span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Kosong</span>
                                @endif
                            </div>
                            <div class="text-[11px] text-slate-600 flex items-center justify-between">
                                <span>Hadir: <b>{{ $stat['hadir'] }}/{{ $stat['total'] }}</b></span>
                                @if ($stat['belum'] > 0)
                                    <span class="text-rose-600 font-semibold">{{ $stat['belum'] }} tertinggal</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Bar Scan Kartu Pelajar (NIS) Cepat --}}
        <div class="rounded-2xl border border-blue-200 bg-blue-50/60 p-4 shadow-xs flex flex-col sm:flex-row items-center gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white shadow-xs">
                <x-icon name="id-card" class="h-5 w-5" />
            </div>
            <div class="min-w-0 flex-1">
                <label for="scan-nis-input" class="block text-xs font-bold text-blue-950">Scan Kartu Pelajar / Input Cepat NIS</label>
                <p class="text-[11px] text-blue-700/80">Arahkan scanner kartu pelajar atau ketik NIS lalu tekan Enter untuk langsung menandai HADIR.</p>
            </div>
            <div class="w-full sm:w-72">
                <input id="scan-nis-input" type="text" placeholder="Scan barcode NIS lalu tekan Enter..."
                       class="w-full rounded-xl border-blue-300 bg-white text-xs py-2 px-3 focus:border-blue-600 focus:ring-blue-600 font-mono shadow-2xs">
            </div>
        </div>

        @if ($locked)
            <div class="rounded-xl border border-blue-200/80 bg-blue-50/70 px-4 py-3 text-xs text-slate-700 flex items-center gap-2 shadow-2xs">
                <x-icon name="lock" class="h-4 w-4 text-blue-600 shrink-0" />
                <span>Presensi tanggal <b>{{ $tanggal }}</b> sudah <strong>dikunci</strong> dan tidak dapat diubah tanpa izin buka kunci admin.</span>
            </div>
        @endif

        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex flex-wrap gap-2">
                @if (auth()->user()->hasRole(\App\Enums\Role::ADMIN, \App\Enums\Role::OPERATOR, \App\Enums\Role::KEPALA_SEKOLAH))
                    @if (! $locked)
                        <form method="POST" action="{{ route('admin.kehadiran.lock') }}">
                            @csrf
                            <input type="hidden" name="eskul_id" value="{{ $eskul->id }}">
                            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                                <x-icon name="lock" class="h-3.5 w-3.5" />
                                <span>Kunci Presensi</span>
                            </button>
                        </form>
                    @endif
                @endif
                @if (auth()->user()->hasRole(\App\Enums\Role::ADMIN, \App\Enums\Role::OPERATOR))
                    @if ($locked)
                        <form method="POST" action="{{ route('admin.kehadiran.unlock') }}">
                            @csrf
                            <input type="hidden" name="eskul_id" value="{{ $eskul->id }}">
                            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-amber-300 bg-amber-50 text-xs font-bold text-amber-800 hover:bg-amber-100 transition-colors">
                                <x-icon name="lock" class="h-3.5 w-3.5" />
                                <span>Buka Kunci Presensi</span>
                            </button>
                        </form>
                    @endif
                @endif
            </div>

            <div id="scan-feedback" class="text-xs font-bold text-emerald-700 hidden"></div>
        </div>

        @if (!auth()->user()->hasRole(\App\Enums\Role::KEPALA_SEKOLAH))
        <form method="POST" action="{{ route('admin.kehadiran.store') }}" id="form-presensi" class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            @csrf
            <input type="hidden" name="eskul_id" value="{{ $eskul->id }}">
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">

            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-4 py-3 text-left font-bold">Nama Lengkap Siswa</th>
                            <th class="px-4 py-3 text-left font-bold">NIS / Kartu Pelajar</th>
                            <th class="px-4 py-3 text-left font-bold">Kelas</th>
                            <th class="px-4 py-3 text-left font-bold">Status Presensi</th>
                            <th class="px-4 py-3 text-left font-bold">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            $kelompokKelas = $pendaftar->groupBy('kelas');
                        @endphp

                        @forelse ($kelompokKelas as $namaKelas => $anggotaKelas)
                            {{-- Header Per Kelas (Dimulai dari Kelas 10 lalu Kelas 11) --}}
                            <tr class="bg-slate-100/80">
                                <td colspan="5" class="px-4 py-2 font-bold text-slate-800 text-xs">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-block h-2 w-2 rounded-full bg-blue-600"></span>
                                            <span>Rombel: {{ $namaKelas }}</span>
                                            <span class="text-slate-500 font-normal">({{ $anggotaKelas->count() }} Siswa)</span>
                                        </div>
                                        @if (! $locked)
                                            <button type="button" onclick="setSemuaHadir('{{ addslashes($namaKelas) }}')" class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-700 hover:underline cursor-pointer">
                                                <x-icon name="check" class="h-3 w-3" />
                                                <span>Set Semua Hadir di Kelas Ini</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            @foreach ($anggotaKelas as $daftar)
                                @php $ada = $peta[$daftar->id] ?? null; @endphp
                                <tr id="row-{{ $daftar->id }}" data-nis="{{ $daftar->nis }}" data-kelas="{{ $daftar->kelas }}" class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-4 py-2.5 font-semibold text-slate-900">
                                        {{ $daftar->user?->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2.5 text-slate-600 font-mono">
                                        {{ $daftar->nis ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2.5 text-slate-700 font-medium">
                                        {{ $daftar->kelas }}
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="flex items-center gap-2">
                                            <select name="status[{{ $daftar->id }}]" id="select-status-{{ $daftar->id }}"
                                                    class="status-selector rounded-lg border-slate-300 py-1 px-2 text-xs font-semibold focus:border-blue-500 focus:ring-blue-500 {{ ($ada?->status?->value ?? '') === 'HADIR' ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : '' }}"
                                                    @disabled($ada && $ada->isLocked())>
                                                @foreach ($statusList as $st)
                                                    <option value="{{ $st->value }}" @selected(($ada?->status?->value ?? 'HADIR') === $st->value)>
                                                        {{ $st->label() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($ada && $ada->isLocked())
                                                <span class="inline-flex items-center text-[10px] font-bold text-amber-700 bg-amber-100 px-1.5 py-0.5 rounded">Terkunci</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <input type="text" name="keterangan[{{ $daftar->id }}]" value="{{ $ada->keterangan ?? '' }}" maxlength="200" placeholder="Keterangan..."
                                               class="rounded-lg border-slate-300 py-1 px-2 text-xs w-48 focus:border-blue-500 focus:ring-blue-500"
                                               @disabled($ada && $ada->isLocked())>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                    Tidak ada anggota yang disetujui untuk eskul ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($pendaftar->isNotEmpty() && ! $locked)
                <div class="p-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
                    <p class="text-[11px] text-slate-500">Pastikan semua data presensi telah diperiksa sebelum menekan tombol simpan.</p>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-700/20 hover:bg-blue-800 transition-colors cursor-pointer">
                        Simpan Perubahan Presensi
                    </button>
                </div>
            @endif
        </form>
        @endif
    @endif
</div>

<script>
function setSemuaHadir(kelas) {
    const rows = document.querySelectorAll(`tr[data-kelas="${kelas}"]`);
    rows.forEach(r => {
        const sel = r.querySelector('.status-selector');
        if (sel && !sel.disabled) {
            sel.value = 'HADIR';
            sel.classList.add('bg-emerald-50', 'text-emerald-800', 'border-emerald-300');
        }
    });
}

// Quick Scan Barcode Kartu Pelajar
(function() {
    const scanInput = document.getElementById('scan-nis-input');
    const feedback = document.getElementById('scan-feedback');
    if (!scanInput) return;

    scanInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const val = scanInput.value.trim();
            if (!val) return;

            const targetRow = document.querySelector(`tr[data-nis="${val}"]`);
            if (targetRow) {
                const sel = targetRow.querySelector('.status-selector');
                if (sel && !sel.disabled) {
                    sel.value = 'HADIR';
                    sel.classList.add('bg-emerald-50', 'text-emerald-800', 'border-emerald-300');
                }
                targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                targetRow.classList.add('bg-emerald-100');
                setTimeout(() => targetRow.classList.remove('bg-emerald-100'), 2500);

                if (feedback) {
                    feedback.textContent = `Siswa NIS ${val} berhasil ditandai HADIR`;
                    feedback.classList.remove('hidden');
                    setTimeout(() => feedback.classList.add('hidden'), 3500);
                }
                scanInput.value = '';
            } else {
                alert(`Siswa dengan NIS "${val}" tidak ditemukan di daftar anggota eskul ini.`);
                scanInput.select();
            }
        }
    });
})();
</script>
@endsection

@extends('layouts.staff')

@section('title', 'Kenaikan Kelas Per Rombel')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Kenaikan Kelas Per Rombel</h1>
            <p class="text-xs text-slate-500">Proses kenaikan kelas dilakukan per rombel. Copot centang bagi siswa yang tinggal kelas atau dikeluarkan.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.kenaikan.arsip-keluar') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors shadow-2xs">
                <x-icon name="archive-box" class="h-4 w-4 text-amber-600" />
                <span>Lihat Arsip Siswa Keluar ({{ $totalArsipKeluar }})</span>
            </a>
        </div>
    </div>

    {{-- Filter Pemilihan Rombel Kelas Asal --}}
    <form method="GET" action="{{ route('admin.kenaikan.index') }}" class="flex flex-wrap gap-3 items-end bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
        <div class="flex-1 min-w-[200px]">
            <label for="kelas" class="block text-xs font-bold text-slate-700 mb-1">Pilih Rombel Kelas yang Akan Diproses</label>
            <select name="kelas" id="kelas" class="w-full border-slate-300 rounded-xl text-xs py-2 focus:border-blue-500 focus:ring-blue-500 font-semibold" onchange="this.form.submit()">
                @foreach ($availableClasses as $c)
                    <option value="{{ $c }}" @selected($selectedClass === $c)>{{ $c }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="target_kelas" class="block text-xs font-bold text-slate-700 mb-1">Rombel Tujuan (Otomatis)</label>
            <input type="text" name="target_kelas" id="target_kelas" value="{{ $targetKelas }}" placeholder="Contoh: 11 RPL 1"
                   class="border-slate-300 rounded-xl text-xs py-2 px-3 focus:border-blue-500 focus:ring-blue-500 font-bold text-blue-700 bg-blue-50/50">
        </div>
        <button type="submit" class="px-5 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors cursor-pointer">
            Muat Siswa
        </button>
    </form>

    {{-- Formulir Kenaikan Kelas Rombel Terpilih --}}
    @if ($students->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-xs text-slate-400">
            Tidak ada siswa aktif yang terdaftar di kelas <b>{{ $selectedClass }}</b>.
        </div>
    @else
        <form method="POST" action="{{ route('admin.kenaikan.store') }}" id="form-kenaikan" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs space-y-4">
            @csrf
            <input type="hidden" name="kelas_asal" value="{{ $selectedClass }}">
            <input type="hidden" name="kelas_tujuan" value="{{ $targetKelas }}">

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                        <x-icon name="user-group" class="h-4 w-4" />
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Daftar Siswa Kelas {{ $selectedClass }}</h2>
                        <p class="text-[11px] text-slate-400">
                            Target naik ke: <b class="text-blue-700 font-mono">{{ $targetKelas }}</b> • Total: <b>{{ $students->count() }} Siswa</b>
                        </p>
                    </div>
                </div>

                {{-- Tombol Toggle Semua --}}
                <div class="flex items-center gap-2">
                    <button type="button" onclick="centangSemua(true)" class="text-[11px] font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg hover:bg-blue-100 cursor-pointer">
                        Centang Semua (Naik)
                    </button>
                    <button type="button" onclick="centangSemua(false)" class="text-[11px] font-bold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg hover:bg-slate-200 cursor-pointer">
                        Lepas Semua (Tinggal)
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-100">
                <table class="min-w-full text-xs">
                    <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider font-bold text-[11px]">
                        <tr>
                            <th class="py-2.5 px-3 text-center w-12">Naik?</th>
                            <th class="py-2.5 px-4 text-left">NIS</th>
                            <th class="py-2.5 px-4 text-left">Nama Lengkap Siswa</th>
                            <th class="py-2.5 px-3 text-center">L/P</th>
                            <th class="py-2.5 px-4 text-left">Status Kenaikan</th>
                            <th class="py-2.5 px-4 text-right">Opsi Siswa Keluar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($students as $s)
                            <input type="hidden" name="siswa_ids[]" value="{{ $s->id }}">
                            <tr id="row-siswa-{{ $s->id }}" class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-2.5 px-3 text-center">
                                    <input type="checkbox" name="naik_siswa_ids[]" value="{{ $s->id }}"
                                           id="cb-{{ $s->id }}" checked
                                           onchange="updateStatusRow({{ $s->id }})"
                                           class="cb-naik h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                </td>
                                <td class="py-2.5 px-4 font-mono font-bold text-slate-800">
                                    {{ $s->nis }}
                                </td>
                                <td class="py-2.5 px-4 font-semibold text-slate-900">
                                    {{ $s->nama }}
                                </td>
                                <td class="py-2.5 px-3 text-center font-bold text-slate-600">
                                    {{ $s->gender?->short() ?? '-' }}
                                </td>
                                <td class="py-2.5 px-4">
                                    <span id="label-status-{{ $s->id }}" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <x-icon name="arrow-up" class="h-3 w-3" />
                                        Naik ke {{ $targetKelas }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-4 text-right">
                                    <button type="button" onclick="bukaModalKeluarkan({{ $s->id }}, '{{ addslashes($s->nama) }}', '{{ $s->nis }}')"
                                            class="inline-flex items-center gap-1 px-2 py-1 rounded-lg border border-rose-200 text-rose-600 font-semibold text-[11px] hover:bg-rose-50 transition-colors cursor-pointer"
                                            title="Keluarkan / Mutasi siswa ini dan arsipkan datanya ke tabel Siswa Keluar">
                                        <x-icon name="user-minus" class="h-3.5 w-3.5" />
                                        <span>Keluarkan / Mutasi</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Konfirmasi & Submit --}}
            <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-xs text-slate-600">
                    <input type="checkbox" id="konfirmasi-kenaikan" required class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                    <label for="konfirmasi-kenaikan" class="cursor-pointer font-medium">
                        Saya telah memeriksa checklist siswa rombel {{ $selectedClass }}. Siswa yang dicentang akan dinaikkan ke {{ $targetKelas }}, siswa yang tidak dicentang akan tinggal di {{ $selectedClass }}.
                    </label>
                </div>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-700/20 hover:bg-blue-800 transition-colors cursor-pointer shrink-0">
                    Jalankan Kenaikan Kelas Rombel Ini
                </button>
            </div>
        </form>
    @endif
</div>

{{-- Modal Keluarkan / Mutasi Siswa --}}
<div id="modal-keluar" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-xs" onclick="tutupModalKeluar()"></div>
    <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100 text-left">
        <div class="flex items-center gap-2.5 text-rose-600 mb-2">
            <x-icon name="alert-triangle" class="h-6 w-6" />
            <h3 class="text-base font-bold text-slate-900">Keluarkan / Mutasi Siswa</h3>
        </div>
        <p class="text-[11px] text-slate-500 mb-4">
            Data siswa akan dipindahkan dan disimpan permanen ke tabel <b>Siswa Keluar</b> (tidak akan hilang).
        </p>

        <form method="POST" action="{{ route('admin.kenaikan.keluarkan') }}" class="space-y-4 text-xs">
            @csrf
            <input type="hidden" name="student_id" id="modal-keluar-student-id">

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80">
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Siswa:</span>
                <span class="font-bold text-slate-900 text-sm" id="modal-keluar-nama">-</span>
                <span class="text-slate-500 font-mono text-xs block" id="modal-keluar-nis">-</span>
            </div>

            <div>
                <label for="modal-status-keluar" class="block font-bold text-slate-700 mb-1">Jenis Status Keluar <span class="text-rose-500">*</span></label>
                <select name="status_keluar" id="modal-status-keluar" required class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-blue-500 focus:ring-blue-500 font-semibold">
                    <option value="KELUAR">Dikeluarkan dari Sekolah</option>
                    <option value="MUTASI">Pindah Sekolah / Mutasi</option>
                    <option value="DO">Drop Out (DO)</option>
                </select>
            </div>

            <div>
                <label for="modal-alasan-keluar" class="block font-bold text-slate-700 mb-1">Alasan Pengeluaran / Keterangan <span class="text-rose-500">*</span></label>
                <textarea name="alasan" id="modal-alasan-keluar" rows="3" required placeholder="Tuliskan alasan pengeluaran / nomor surat mutasi siswa..."
                          class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-blue-500 focus:ring-blue-500 leading-relaxed"></textarea>
            </div>

            <div class="flex gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="tutupModalKeluar()" class="flex-1 rounded-xl border border-slate-300 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 rounded-xl bg-rose-600 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-rose-700 transition-colors">
                    Keluarkan & Arsipkan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function centangSemua(naik) {
    const checkboxes = document.querySelectorAll('.cb-naik');
    checkboxes.forEach(cb => {
        cb.checked = naik;
        const id = cb.id.replace('cb-', '');
        updateStatusRow(id);
    });
}

function updateStatusRow(id) {
    const cb = document.getElementById('cb-' + id);
    const label = document.getElementById('label-status-' + id);
    const target = @json($targetKelas);
    const asal = @json($selectedClass);

    if (cb.checked) {
        label.className = "inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800";
        label.innerHTML = `↑ Naik ke ${target}`;
    } else {
        label.className = "inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800";
        label.innerHTML = `• Tinggal di ${asal}`;
    }
}

function bukaModalKeluarkan(id, nama, nis) {
    document.getElementById('modal-keluar-student-id').value = id;
    document.getElementById('modal-keluar-nama').textContent = nama;
    document.getElementById('modal-keluar-nis').textContent = 'NIS: ' + nis;
    document.getElementById('modal-alasan-keluar').value = '';

    const modal = document.getElementById('modal-keluar');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function tutupModalKeluar() {
    const modal = document.getElementById('modal-keluar');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endsection

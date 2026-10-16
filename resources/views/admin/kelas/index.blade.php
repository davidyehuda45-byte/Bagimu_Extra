@extends('layouts.staff')

@section('title', 'Master Data Rombel & Wali Kelas')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Master Rombel & Wali Kelas</h1>
            <p class="text-xs text-slate-500">Kelola master rombel kelas, penetapan wali kelas, dan pantau sebaran siswa.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <form method="POST" action="{{ route('admin.kelas.sync') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors shadow-2xs cursor-pointer" title="Ambil semua kelas unik dari direktori siswa yang belum tercatat">
                    <x-icon name="arrow-path" class="h-4 w-4 text-blue-600" />
                    <span>Sinkronkan dari Data Siswa</span>
                </button>
            </form>
            <button type="button" onclick="bukaModalTambahKelas()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-700 text-xs font-bold text-white hover:bg-blue-800 transition-colors shadow-xs cursor-pointer">
                <x-icon name="plus" class="h-4 w-4" />
                <span>Tambah Kelas Baru</span>
            </button>
        </div>
    </div>

    {{-- Tabel Master Rombel Kelas & Wali Kelas --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">
                    <x-icon name="academic-cap" class="h-4 w-4" />
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Rombel & Penugasan Wali Kelas</h2>
                    <p class="text-[11px] text-slate-400">Wali kelas yang ditugaskan dapat login untuk memantau kehadiran dan mencetak nilai rapor siswa.</p>
                </div>
            </div>
            <span class="text-xs font-bold text-slate-600">{{ $masterKelas->count() }} Kelas Terdaftar</span>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-100">
            <table class="min-w-full text-xs">
                <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider font-bold text-[11px]">
                    <tr>
                        <th class="py-2.5 px-4 text-left">Nama Rombel</th>
                        <th class="py-2.5 px-4 text-left">Tingkat</th>
                        <th class="py-2.5 px-4 text-left">Jurusan</th>
                        <th class="py-2.5 px-4 text-left">Nama Wali Kelas</th>
                        <th class="py-2.5 px-4 text-left">Akun Login Tertaut</th>
                        <th class="py-2.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($masterKelas as $mk)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-2.5 px-4 font-bold text-slate-900 font-mono">{{ $mk->nama }}</td>
                            <td class="py-2.5 px-4 font-semibold text-slate-700">Kelas {{ $mk->tingkat ?? '-' }}</td>
                            <td class="py-2.5 px-4">
                                <span class="px-2 py-0.5 rounded-md font-semibold bg-blue-50 text-blue-700 text-[10px]">
                                    {{ $mk->jurusan ?? '-' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 font-medium text-slate-900">
                                {{ $mk->wali_kelas_nama ?: '— Belum diatur —' }}
                            </td>
                            <td class="py-2.5 px-4">
                                @if ($mk->waliKelas)
                                    <span class="inline-flex items-center gap-1 font-semibold text-emerald-700 text-[11px]">
                                        <x-icon name="check-circle" class="h-3.5 w-3.5" />
                                        {{ $mk->waliKelas->name }} ({{ $mk->waliKelas->username ?? $mk->waliKelas->email }})
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px]">— Belum ditautkan akun</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-right space-x-1">
                                <button type="button"
                                        onclick="bukaModalEditKelas({{ json_encode($mk) }})"
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg border border-slate-300 text-[11px] font-semibold text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.kelas.destroy', $mk) }}" class="inline" onsubmit="return confirm('Hapus kelas {{ $mk->nama }} dari master?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2.5 py-1 rounded-lg border border-rose-200 text-[11px] font-semibold text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Belum ada rombel kelas di master. Tekan tombol <b>Sinkronkan dari Data Siswa</b> untuk mengisi otomatis.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Filter Periode & Ringkasan Matriks Sebaran --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Rekap Sebaran Pendaftar Ekstrakurikuler</h2>
                <p class="text-[11px] text-slate-400">Statistik siswa terdaftar eskul per jurusan dan rombel kelas</p>
            </div>
            <form method="GET" action="{{ route('admin.kelas.index') }}" class="flex items-center gap-2">
                <label for="periode" class="text-xs font-bold text-slate-600">Tahun Ajaran:</label>
                <select name="periode" id="periode" class="border-slate-300 rounded-xl px-3 py-1.5 text-xs font-medium focus:border-blue-500 focus:ring-blue-500" onchange="this.form.submit()">
                    @foreach ($periodes as $p)
                        <option value="{{ $p->periode }}" @selected($periode === $p->periode)>{{ $p->periode }}</option>
                    @endforeach
                </select>
                <span class="text-xs font-bold text-blue-700 ml-2">Total: {{ $rincian->sum('total') }} Siswa</span>
            </form>
        </div>

        {{-- Matriks --}}
        <div class="overflow-x-auto rounded-xl border border-slate-100">
            <table class="min-w-full text-xs">
                <thead>
                    <tr class="border-b bg-slate-50 text-left font-bold text-slate-600 text-[11px]">
                        <th class="py-2.5 px-3">Kelas / Rombel</th>
                        @foreach ($jurusans as $j)
                            <th class="py-2.5 px-3 text-center">{{ $j->short() }}</th>
                        @endforeach
                        <th class="py-2.5 px-3 text-right">Total Siswa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($kelasList as $k)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-2 px-3 font-bold text-slate-900 font-mono">{{ $k }}</td>
                            @foreach ($jurusans as $j)
                                <td class="py-2 px-3 text-center">
                                    @if (($matrix[$k][$j->value] ?? 0) > 0)
                                        <span class="inline-block rounded-md bg-blue-100 px-2 py-0.5 font-bold text-blue-800 text-[11px]">{{ $matrix[$k][$j->value] }}</span>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="py-2 px-3 text-right font-bold text-slate-900">{{ array_sum($matrix[$k]) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($jurusans) + 2 }}" class="py-6 text-center text-slate-400">
                                Tidak ada data pendaftaran di periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah / Edit Kelas --}}
<div id="modal-kelas" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-xs" onclick="tutupModalKelas()"></div>
    <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100 text-left">
        <h3 class="text-base font-bold text-slate-900" id="modal-kelas-title">Tambah Rombel Kelas</h3>
        <p class="text-[11px] text-slate-500 mb-4">Pastikan format rombel seragam (contoh: 10 RPL 1).</p>

        <form id="form-modal-kelas" method="POST" action="{{ route('admin.kelas.store') }}" class="space-y-4 text-xs">
            @csrf
            <div id="method-put-wrap"></div>

            <div>
                <label for="input-kelas-nama" class="block font-bold text-slate-700 mb-1">Nama Rombel Kelas <span class="text-rose-500">*</span></label>
                <input type="text" id="input-kelas-nama" name="nama" required placeholder="Contoh: 10 RPL 1 atau 11 DKV 2"
                       class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-blue-500 focus:ring-blue-500 font-semibold">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="input-kelas-tingkat" class="block font-bold text-slate-700 mb-1">Tingkat</label>
                    <select id="input-kelas-tingkat" name="tingkat" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Otomatis</option>
                        <option value="10">Kelas 10</option>
                        <option value="11">Kelas 11</option>
                        <option value="12">Kelas 12</option>
                    </select>
                </div>
                <div>
                    <label for="input-kelas-jurusan" class="block font-bold text-slate-700 mb-1">Jurusan</label>
                    <input type="text" id="input-kelas-jurusan" name="jurusan" placeholder="RPL, DKV, dsb."
                           class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-blue-500 focus:ring-blue-500 font-semibold">
                </div>
            </div>

            <div>
                <label for="input-wali-nama" class="block font-bold text-slate-700 mb-1">Nama Wali Kelas</label>
                <input type="text" id="input-wali-nama" name="wali_kelas_nama" placeholder="Nama lengkap & gelar wali kelas"
                       class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="input-wali-user" class="block font-bold text-slate-700 mb-1">Tautkan Akun Pengguna Wali Kelas (Opsional)</label>
                <select id="input-wali-user" name="wali_kelas_id" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-blue-500 focus:ring-blue-500">
                    <option value="">— Tidak ada / Belum ditautkan —</option>
                    @foreach ($waliUsers as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role->label() }})</option>
                    @endforeach
                </select>
                <p class="mt-1 text-[10px] text-slate-400">User yang ditautkan dapat login menggunakan akunnya untuk mengakses data kelas ini.</p>
            </div>

            <div class="flex gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="tutupModalKelas()" class="flex-1 rounded-xl border border-slate-300 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 rounded-xl bg-blue-700 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-blue-800 transition-colors">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function bukaModalTambahKelas() {
    document.getElementById('modal-kelas-title').textContent = 'Tambah Rombel Kelas';
    const form = document.getElementById('form-modal-kelas');
    form.action = "{{ route('admin.kelas.store') }}";
    document.getElementById('method-put-wrap').innerHTML = '';
    document.getElementById('input-kelas-nama').value = '';
    document.getElementById('input-kelas-tingkat').value = '';
    document.getElementById('input-kelas-jurusan').value = '';
    document.getElementById('input-wali-nama').value = '';
    document.getElementById('input-wali-user').value = '';

    const modal = document.getElementById('modal-kelas');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function bukaModalEditKelas(kelas) {
    document.getElementById('modal-kelas-title').textContent = 'Edit Rombel Kelas: ' + kelas.nama;
    const form = document.getElementById('form-modal-kelas');
    form.action = "{{ url('admin/kelas') }}/" + kelas.id;
    document.getElementById('method-put-wrap').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('input-kelas-nama').value = kelas.nama || '';
    document.getElementById('input-kelas-tingkat').value = kelas.tingkat || '';
    document.getElementById('input-kelas-jurusan').value = kelas.jurusan || '';
    document.getElementById('input-wali-nama').value = kelas.wali_kelas_nama || '';
    document.getElementById('input-wali-user').value = kelas.wali_kelas_id || '';

    const modal = document.getElementById('modal-kelas');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function tutupModalKelas() {
    const modal = document.getElementById('modal-kelas');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endsection

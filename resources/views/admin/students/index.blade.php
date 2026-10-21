@extends('layouts.staff')

@section('title', 'Direktori Siswa')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
    <div>
        <div class="flex items-center gap-2.5">
            <h1 class="text-xl font-bold text-slate-900">Direktori Siswa</h1>
            <span id="total-students-badge" class="inline-flex items-center rounded-xl bg-slate-100 border border-slate-200 px-2.5 py-1 text-xs font-bold text-slate-700 shadow-2xs">
                {{ $students->total() }} siswa
            </span>
        </div>
        <p class="text-xs text-slate-500 mt-1">Kelola data induk siswa, kredensial token login, dan kartu login sekolah.</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        {{-- Tombol Cek Status Pendaftaran Per Rombel (Ala E-Voting) --}}
        <a href="{{ route('admin.students.status-rombel') }}" class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-3.5 py-2.5 text-xs font-bold text-indigo-700 hover:bg-indigo-100 shadow-2xs transition-all" title="Monitoring Progress Pendaftaran Per Rombel">
            <x-icon name="chart" class="h-3.5 w-3.5 text-indigo-600 shrink-0" />
            <span>Cek Status Rombel</span>
        </a>

        {{-- Export Excel & CSV --}}
        <div class="inline-flex rounded-xl border border-slate-300 bg-white p-0.5 shadow-2xs">
            <button type="button" onclick="exportData('xlsx')" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:text-emerald-700 hover:bg-slate-50 rounded-lg transition-colors cursor-pointer" title="Unduh data dalam format Excel (.xlsx)">
                <x-icon name="file" class="h-3.5 w-3.5 text-emerald-600" />
                <span>Export Excel</span>
            </button>
            <div class="w-px bg-slate-200 my-1"></div>
            <button type="button" onclick="exportData('csv')" class="inline-flex items-center gap-1.5 px-2.5 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition-colors cursor-pointer" title="Unduh data dalam format CSV mentah">
                <span>CSV</span>
            </button>
        </div>

        {{-- Tombol Generate Semua Token --}}
        <button type="button" onclick="openBulkTokenModal()" class="inline-flex items-center gap-2 rounded-xl border border-amber-300 bg-amber-50 px-3.5 py-2.5 text-xs font-bold text-amber-800 hover:bg-amber-100 shadow-2xs transition-all cursor-pointer" title="Generate token login otomatis untuk siswa">
            <x-icon name="key" class="h-3.5 w-3.5 text-amber-600 shrink-0" />
            <span>Generate Semua Token</span>
        </button>

        {{-- Tombol WA Blast Massal Fonnte --}}
        <button type="button" onclick="openWaBlastModal('all')" class="inline-flex items-center gap-2 rounded-xl border border-emerald-300 bg-emerald-50 px-3.5 py-2.5 text-xs font-bold text-emerald-800 hover:bg-emerald-100 shadow-2xs transition-all cursor-pointer" title="Kirim pesan WhatsApp massal ke siswa (WA Blast)">
            <x-icon name="send" class="h-3.5 w-3.5 text-emerald-600 shrink-0" />
            <span>WA Blast</span>
        </button>

        {{-- Form Input / Upload Langsung Berkas Siswa Multi-Format (Excel, CSV, PDF) --}}
        <form id="direct-student-import-form" method="POST" action="{{ route('admin.students.import.store') }}" enctype="multipart/form-data" class="inline-block m-0">
            @csrf
            <input type="file" id="direct-student-file-input" name="file" accept=".csv,.txt,.xlsx,.xls,.ods,.pdf" class="hidden" onchange="handleDirectStudentUpload(this)">
            <button type="button" onclick="document.getElementById('direct-student-file-input').click()" id="btn-import-trigger" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-2xs transition-all hover:border-blue-500 hover:text-blue-700 cursor-pointer" title="Impor Data Siswa (Excel, CSV, PDF, dsb.)">
                <x-icon name="download" class="h-3.5 w-3.5 text-blue-600 shrink-0" />
                <span>Impor Siswa</span>
                <span class="hidden sm:inline-flex items-center rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium text-slate-600 border border-slate-200">
                    Excel/CSV/PDF
                </span>
            </button>
        </form>

        <a href="{{ route('admin.students.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-700 px-4 py-2.5 text-xs font-bold text-white hover:bg-blue-800 shadow-2xs transition-colors">
            <x-icon name="plus" class="h-3.5 w-3.5" />
            <span>Tambah Siswa</span>
        </a>
    </div>
</div>

{{-- Bar Notifikasi Flash --}}
@if (session('status'))
    <div class="mb-4 flex items-center justify-between rounded-2xl border border-emerald-200 bg-emerald-50/90 px-4 py-3 text-xs font-medium text-emerald-800 shadow-xs">
        <div class="flex items-center gap-2">
            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-200 text-emerald-800 font-bold">✓</span>
            <span>{{ session('status') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
    </div>
@endif
@if (session('warning'))
    <div class="mb-4 flex items-center justify-between rounded-2xl border border-amber-200 bg-amber-50/90 px-4 py-3 text-xs font-medium text-amber-800 shadow-xs">
        <div class="flex items-center gap-2">
            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-amber-200 text-amber-800 font-bold">!</span>
            <span>{{ session('warning') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-amber-600 hover:text-amber-900 font-bold">&times;</button>
    </div>
@endif
@if (session('error'))
    <div class="mb-4 flex items-center justify-between rounded-2xl border border-rose-200 bg-rose-50/90 px-4 py-3 text-xs font-medium text-rose-800 shadow-xs">
        <div class="flex items-center gap-2">
            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-rose-200 text-rose-800 font-bold">&times;</span>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold">&times;</button>
    </div>
@endif

{{-- Filter & Real-Time Live Search --}}
<form id="filter-siswa-form" method="GET" action="{{ route('admin.students.index') }}" class="rounded-2xl border border-slate-200 bg-white p-4 mb-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-3 text-sm shadow-xs items-end">
    {{-- Pencarian --}}
    <div>
        <label for="q-search-input" class="block font-semibold mb-1 text-slate-700 text-xs">Pencarian (Nama / NIS)</label>
        <div class="relative">
            <input type="text" id="q-search-input" name="q" value="{{ request('q') }}"
                   class="w-full rounded-xl border-slate-300 text-sm pl-9 pr-8 focus:border-blue-500 focus:ring-blue-500"
                   placeholder="Ketik nama / NIS / NISN..." autocomplete="off">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <x-icon name="search" class="h-4 w-4" />
            </div>
            <div id="search-spinner" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-blue-600">
                <svg class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
            </div>
        </div>
    </div>

    {{-- Tingkat Kelas (10, 11, 12) --}}
    <div>
        <label for="tingkat-filter" class="block font-semibold mb-1 text-slate-700 text-xs">Tingkat Kelas</label>
        <select id="tingkat-filter" name="tingkat" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Semua Tingkat</option>
            @foreach ($tingkatList as $t)
                <option value="{{ $t }}" @selected(request('tingkat') === $t)>Kelas {{ $t }}</option>
            @endforeach
        </select>
    </div>

    {{-- Rombel / Kelas Spesifik --}}
    <div>
        <label for="kelas-filter" class="block font-semibold mb-1 text-slate-700 text-xs">Rombel / Kelas</label>
        <select id="kelas-filter" name="kelas" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Semua Kelas</option>
            @foreach ($kelasList as $k)
                <option value="{{ $k }}" @selected(request('kelas') === $k)>{{ $k }}</option>
            @endforeach
        </select>
    </div>

    {{-- Jurusan --}}
    <div>
        <label for="jurusan-filter" class="block font-semibold mb-1 text-slate-700 text-xs">Jurusan</label>
        <select id="jurusan-filter" name="jurusan" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Semua Jurusan</option>
            @foreach ($jurusanList as $j)
                <option value="{{ $j->value }}" @selected(request('jurusan') === $j->value)>{{ $j->label() }}</option>
            @endforeach
        </select>
    </div>

    {{-- Status Pendaftaran Eskul --}}
    <div>
        <label for="eskul-status-filter" class="block font-semibold mb-1 text-slate-700 text-xs">Status Eskul</label>
        <select id="eskul-status-filter" name="eskul_status" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Semua Siswa</option>
            <option value="terdaftar" @selected(request('eskul_status') === 'terdaftar')>Sudah Terdaftar</option>
            <option value="belum" @selected(request('eskul_status') === 'belum')>Belum Mendaftar</option>
        </select>
    </div>

    {{-- Status Siswa --}}
    <div>
        <label for="status-filter" class="block font-semibold mb-1 text-slate-700 text-xs">Status Siswa</label>
        <select id="status-filter" name="status" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Semua Status</option>
            @foreach ($statusList as $st)
                <option value="{{ $st }}" @selected(request('status') === $st)>{{ $st }}</option>
            @endforeach
        </select>
    </div>

    {{-- Tombol Reset --}}
    <div>
        <a href="{{ route('admin.students.index') }}" class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors shadow-2xs h-[38px]" title="Reset semua filter">
            <x-icon name="history" class="h-3.5 w-3.5 text-slate-500" />
            <span>Reset</span>
        </a>
    </div>
</form>

{{-- Action Bar Seleksi Massal (Muncul saat ada checkbox dicentang) --}}
<div id="bulk-selection-bar" class="hidden mb-3 items-center justify-between gap-3 rounded-2xl border border-blue-200 bg-blue-50/90 px-4 py-3 text-xs text-blue-900 shadow-sm transition-all">
    <div class="flex items-center gap-2.5">
        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-600 text-white font-bold text-xs">
            ✓
        </span>
        <span class="font-medium">
            <strong id="selected-count-text" class="font-bold">0</strong> siswa dipilih
        </span>
    </div>
    <div class="flex items-center gap-2">
        <button type="button" onclick="openWaBlastModal('selected')" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3 py-1.5 font-bold text-white hover:bg-emerald-700 transition-colors shadow-2xs cursor-pointer">
            <x-icon name="send" class="h-3.5 w-3.5" />
            <span>WA Blast Terpilih</span>
        </button>
        <button type="button" onclick="confirmBulkTokenSelected()" class="inline-flex items-center gap-1.5 rounded-xl border border-blue-300 bg-white px-3 py-1.5 font-bold text-blue-700 hover:bg-blue-100 transition-colors shadow-2xs cursor-pointer">
            <x-icon name="key" class="h-3.5 w-3.5 text-blue-600" />
            <span>Generate Token Terpilih</span>
        </button>
        <button type="button" onclick="confirmBulkDelete()" class="inline-flex items-center gap-1.5 rounded-xl bg-rose-600 px-3 py-1.5 font-bold text-white hover:bg-rose-700 transition-colors shadow-2xs cursor-pointer">
            <x-icon name="trash" class="h-3.5 w-3.5" />
            <span>Hapus Terpilih</span>
        </button>
        <button type="button" onclick="deselectAll()" class="px-2 py-1 text-slate-500 hover:text-slate-800 font-semibold cursor-pointer">
            Batal
        </button>
    </div>
</div>

{{-- Tabel Direktori Siswa --}}
<div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
    <div class="overflow-x-auto min-h-[300px]">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200 text-left text-xs font-bold uppercase tracking-wider text-slate-400">
                    <th class="py-2.5 px-3 w-10 text-center">
                        <input type="checkbox" id="select-all-students" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer h-4 w-4" title="Pilih Semua di Halaman Ini">
                    </th>
                    <th class="py-2.5 px-3">NIS</th>
                    <th class="py-2.5 px-3">NISN</th>
                    <th class="py-2.5 px-3">Nama Lengkap</th>
                    <th class="py-2.5 px-3">Kelas</th>
                    <th class="py-2.5 px-3">Jurusan</th>
                    <th class="py-2.5 px-3">Status</th>
                    <th class="py-2.5 px-3">Token Login</th>
                    <th class="py-2.5 px-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody id="students-table-body" class="divide-y divide-slate-100">
                @include('admin.students.partials.rows')
            </tbody>
        </table>
    </div>

    <div id="students-pagination" class="mt-4 border-t border-slate-100 pt-3">
        {{ $students->links() }}
    </div>
</div>

{{-- Modal Konfirmasi Standar (Ala E-Voting) --}}
<div id="modal-confirm" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" onclick="closeConfirmModal()"></div>
    <div class="relative w-full max-w-sm rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-2xl">
        <div id="confirm-icon-wrap" class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600">
            <x-icon name="trash" class="h-6 w-6" id="confirm-icon" />
        </div>
        <h2 id="confirm-title" class="text-base font-bold text-slate-900">Konfirmasi Aksi</h2>
        <p id="confirm-message" class="text-xs text-slate-500 mt-1 mb-5 leading-relaxed"></p>
        <div class="flex gap-2">
            <button onclick="closeConfirmModal()" class="flex-1 rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer" type="button">
                Batal
            </button>
            <button id="confirm-ok-btn" class="flex-1 rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white hover:bg-rose-700 transition cursor-pointer" type="button">
                Ya, Lanjutkan
            </button>
        </div>
    </div>
</div>

{{-- Modal Generate Semua Token --}}
<div id="modal-bulk-token" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" onclick="closeBulkTokenModal()"></div>
    <div class="relative w-full max-w-md rounded-2xl border border-slate-200 bg-white p-5 shadow-2xl text-left">
        <div class="flex items-center gap-3 mb-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600 border border-amber-200">
                <x-icon name="key" class="h-5 w-5" />
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900">Generate Token Massal</h3>
                <p class="text-xs text-slate-500">Buat atau perbarui kredensial token login siswa secara otomatis.</p>
            </div>
        </div>

        <form id="form-bulk-token" method="POST" action="{{ route('admin.students.bulk-token') }}" class="mt-4">
            @csrf
            {{-- Parameter filter aktif diteruskan agar bisa generate sesuai filter --}}
            <input type="hidden" name="q" id="bulk-token-q" value="{{ request('q') }}">
            <input type="hidden" name="tingkat" id="bulk-token-tingkat" value="{{ request('tingkat') }}">
            <input type="hidden" name="kelas" id="bulk-token-kelas" value="{{ request('kelas') }}">
            <input type="hidden" name="jurusan" id="bulk-token-jurusan" value="{{ request('jurusan') }}">
            <input type="hidden" name="status" id="bulk-token-status" value="{{ request('status') }}">
            <input type="hidden" name="eskul_status" id="bulk-token-eskul-status" value="{{ request('eskul_status') }}">

            <div class="space-y-3 mb-4">
                <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                    <input type="radio" name="mode" value="untokenized" checked class="mt-0.5 text-blue-600 focus:ring-blue-500">
                    <div>
                        <span class="block text-xs font-bold text-slate-800">Hanya siswa yang belum memiliki token (Direkomendasikan)</span>
                        <span class="block text-[11px] text-slate-500 mt-0.5">Token siswa yang sudah aktif tidak akan diganti atau ter-reset.</span>
                    </div>
                </label>
                <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                    <input type="radio" name="mode" value="all" class="mt-0.5 text-blue-600 focus:ring-blue-500">
                    <div>
                        <span class="block text-xs font-bold text-slate-800">Seluruh siswa (Reset & Terbitkan Token Baru)</span>
                        <span class="block text-[11px] text-slate-500 mt-0.5">Token lama semua siswa akan hangus dan digantikan token baru 8 digit.</span>
                    </div>
                </label>
            </div>

            <div class="rounded-xl bg-blue-50 p-2.5 text-[11px] text-blue-800 border border-blue-100 mb-4 flex items-start gap-2">
                <x-icon name="bulb" class="h-4 w-4 shrink-0 mt-px" />
                <span><strong>Catatan:</strong> Proses ini akan memproses siswa yang sesuai dengan filter yang aktif saat ini.</span>
            </div>

            <div class="flex items-center justify-end gap-2">
                <button type="button" onclick="closeBulkTokenModal()" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="btn-submit-bulk-token" class="rounded-xl bg-amber-600 px-4 py-2 text-xs font-bold text-white hover:bg-amber-700 transition cursor-pointer">
                    Mulai Generate Token
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Kirim Token via WhatsApp (Ala E-Voting) --}}
<div id="modal-wa-token" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" onclick="closeWaModal()"></div>
    <div class="relative w-full max-w-md rounded-2xl border border-slate-200 bg-white p-5 shadow-2xl text-left">
        <div class="flex items-center gap-3 mb-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200">
                <span class="text-xl">💬</span>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900">Kirim Token via WhatsApp</h3>
                <p class="text-xs text-slate-500">Kirim kredensial login langsung ke nomor siswa / orang tua.</p>
            </div>
        </div>

        <div class="space-y-3.5 my-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Siswa</label>
                <div id="wa-student-name" class="rounded-xl bg-slate-50 border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-800"></div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor WhatsApp (Siswa / Wali)</label>
                <input type="tel" id="wa-phone-input" class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 font-mono" placeholder="08xxxxxxxxxx atau 628xxxxxxxxxx">
                <span class="text-[11px] text-slate-400 mt-1 block">Nomor akan otomatis diformat ke format internasional (628...).</span>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Preview Pesan</label>
                <textarea id="wa-message-preview" rows="5" class="w-full rounded-xl border-slate-200 bg-slate-50 text-xs font-mono text-slate-700 focus:ring-0 focus:border-slate-300 resize-none" readonly></textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <button type="button" onclick="closeWaModal()" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                Batal
            </button>
            <button type="button" onclick="submitWaSend()" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700 transition cursor-pointer shadow-xs">
                <span>Buka WhatsApp</span>
                <x-icon name="arrow-right" class="h-3.5 w-3.5" />
            </button>
        </div>
    </div>
</div>

{{-- Modal WA Blast Fonnte --}}
<div id="modal-wa-blast" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" onclick="closeWaBlastModal()"></div>
    <div class="relative w-full max-w-lg rounded-2xl border border-slate-200 bg-white p-5 shadow-2xl text-left max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200">
                    <x-icon name="send" class="h-5 w-5" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">WhatsApp Blast Massal</h3>
                    <p class="text-xs text-slate-500">Kirim pesan WhatsApp otomatis ke siswa via Fonnte Gateway.</p>
                </div>
            </div>
            <button type="button" onclick="closeWaBlastModal()" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600 cursor-pointer">
                <x-icon name="x" class="h-5 w-5" />
            </button>
        </div>

        @if (!($fonnte['connected'] ?? false))
            <div class="mt-4 rounded-xl bg-amber-50 p-3.5 border border-amber-200 text-amber-900 text-xs flex items-start gap-2.5">
                <x-icon name="alert" class="h-5 w-5 text-amber-600 shrink-0 mt-0.5" />
                <div>
                    <p class="font-bold">WhatsApp Gateway Belum Terhubung!</p>
                    <p class="mt-0.5 text-amber-800 leading-relaxed">
                        Anda belum menghubungkan Fonnte API Key. Silakan masukkan dan verifikasi token terlebih dahulu di menu Pengaturan.
                    </p>
                    <a href="{{ route('admin.settings.index') }}" class="mt-2 inline-flex items-center gap-1 font-bold text-blue-700 hover:underline">
                        Buka Pengaturan Fonnte &rarr;
                    </a>
                </div>
            </div>
        @else
            <div class="mt-3 flex items-center justify-between rounded-xl bg-emerald-50/80 p-2.5 border border-emerald-200 text-xs">
                <span class="inline-flex items-center gap-1.5 font-semibold text-emerald-800">
                    <span class="h-2 w-2 rounded-full bg-emerald-600 animate-pulse"></span>
                    Gateway: <b>{{ $fonnte['name'] ?? 'Device' }}</b> ({{ $fonnte['device'] ?? '' }})
                </span>
                <span class="text-[11px] text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md font-medium">Siap Kirim</span>
            </div>
        @endif

        <form id="form-wa-blast" method="POST" action="{{ route('admin.students.wa-blast') }}" class="mt-4 space-y-3.5" onsubmit="return handleWaBlastSubmit(this)">
            @csrf
            {{-- Parameter filter aktif diteruskan --}}
            <input type="hidden" name="q" id="wa-blast-q" value="{{ request('q') }}">
            <input type="hidden" name="tingkat" id="wa-blast-tingkat" value="{{ request('tingkat') }}">
            <input type="hidden" name="kelas" id="wa-blast-kelas" value="{{ request('kelas') }}">
            <input type="hidden" name="jurusan" id="wa-blast-jurusan" value="{{ request('jurusan') }}">
            <input type="hidden" name="status" id="wa-blast-status" value="{{ request('status') }}">
            <input type="hidden" name="eskul_status" id="wa-blast-eskul-status" value="{{ request('eskul_status') }}">

            {{-- Hidden inputs untuk selected student IDs --}}
            <div id="blast-selected-ids-container"></div>

            {{-- Target Mode --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Target Penerima</label>
                <div class="space-y-2">
                    <label class="flex items-start gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                        <input type="radio" name="mode" id="blast-mode-all" value="all" checked class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                        <div class="text-xs">
                            <span class="font-bold text-slate-800">Semua Siswa dengan Nomor WA Terdaftar</span>
                            <span class="block text-[11px] text-slate-500 mt-0.5">Total saat ini: <b>{{ $totalWaStudents ?? 0 }}</b> siswa aktif ber-nomor WA.</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                        <input type="radio" name="mode" id="blast-mode-selected" value="selected" class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                        <div class="text-xs">
                            <span class="font-bold text-slate-800">Hanya Siswa yang Dipilih (Checkbox)</span>
                            <span class="block text-[11px] text-slate-500 mt-0.5"><span id="blast-modal-selected-count">0</span> siswa saat ini dicentang di tabel.</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                        <input type="radio" name="mode" id="blast-mode-untokenized" value="untokenized" class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                        <div class="text-xs">
                            <span class="font-bold text-slate-800">Siswa yang Belum Memiliki Token Login</span>
                            <span class="block text-[11px] text-slate-500 mt-0.5">Sistem akan otomatis menerbitkan token lalu mengirimkannya via WhatsApp.</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Pilihan Template Pesan --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Template Pesan</label>
                <div class="flex flex-wrap gap-1.5 mb-2">
                    <button type="button" onclick="setWaTemplate('token')" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition cursor-pointer">
                        🔑 Kredensial Token Login
                    </button>
                    <button type="button" onclick="setWaTemplate('pengumuman')" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition cursor-pointer">
                        📢 Pembukaan Pendaftaran
                    </button>
                    <button type="button" onclick="setWaTemplate('custom')" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 transition cursor-pointer">
                        ✏️ Pesan Kustom
                    </button>
                </div>

                {{-- Tag variabel yang bisa disisipkan --}}
                <div class="flex flex-wrap items-center gap-1 text-[11px] text-slate-500 mb-1.5">
                    <span class="font-semibold text-slate-600">Sisipkan variabel:</span>
                    <button type="button" onclick="insertWaTag('{nama}')" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono text-[10px] cursor-pointer">{nama}</button>
                    <button type="button" onclick="insertWaTag('{token}')" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono text-[10px] cursor-pointer">{token}</button>
                    <button type="button" onclick="insertWaTag('{nis}')" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono text-[10px] cursor-pointer">{nis}</button>
                    <button type="button" onclick="insertWaTag('{kelas}')" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono text-[10px] cursor-pointer">{kelas}</button>
                    <button type="button" onclick="insertWaTag('{link}')" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono text-[10px] cursor-pointer">{link}</button>
                </div>

                <textarea 
                    name="pesan" 
                    id="wa-blast-message-input" 
                    rows="5" 
                    required 
                    oninput="updateWaBlastPreview()"
                    placeholder="Tuliskan isi pesan WhatsApp di sini..."
                    class="w-full border-slate-300 rounded-xl text-xs sm:text-sm font-sans focus:border-emerald-500 focus:ring-emerald-500 leading-relaxed"></textarea>
            </div>

            {{-- Live Preview Tampilan Pesan --}}
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pratinjau Pesan yang Diterima Siswa:</label>
                <div class="rounded-xl border border-emerald-200 bg-[#eef7ee] p-3 text-xs text-slate-800 whitespace-pre-wrap font-sans shadow-xs leading-relaxed" id="wa-blast-preview-box">
                    (Pratinjau pesan akan muncul di sini...)
                </div>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeWaBlastModal()" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="btn-submit-wa-blast" {{ !($fonnte['connected'] ?? false) ? 'disabled' : '' }} class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700 transition shadow-xs disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                    <x-icon name="send" class="h-3.5 w-3.5" />
                    <span>Mulai Kirim WA Blast</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Form Tersembunyi untuk Eksekusi Hapus Terpilih --}}
<form id="form-bulk-delete" method="POST" action="{{ route('admin.students.bulk-delete') }}" class="hidden">
    @csrf
    <input type="hidden" name="mode" value="selected">
    <div id="bulk-delete-ids-container"></div>
</form>

{{-- Form Tersembunyi untuk Eksekusi Hapus Single --}}
<form id="form-single-delete" method="POST" action="" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
let confirmCallback = null;

function openConfirmModal(title, message, onConfirm, okText = 'Ya, Lanjutkan', isDanger = true) {
    document.getElementById('confirm-title').textContent = title || 'Konfirmasi';
    document.getElementById('confirm-message').textContent = message;
    const okBtn = document.getElementById('confirm-ok-btn');
    okBtn.textContent = okText;

    const iconWrap = document.getElementById('confirm-icon-wrap');
    if (isDanger) {
        okBtn.className = 'flex-1 rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white hover:bg-rose-700 transition cursor-pointer';
        iconWrap.className = 'mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600';
    } else {
        okBtn.className = 'flex-1 rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700 transition cursor-pointer';
        iconWrap.className = 'mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600';
    }

    confirmCallback = onConfirm;
    const modal = document.getElementById('modal-confirm');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeConfirmModal() {
    const modal = document.getElementById('modal-confirm');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    confirmCallback = null;
}

document.getElementById('confirm-ok-btn').addEventListener('click', function () {
    const cb = confirmCallback;
    closeConfirmModal();
    if (cb) cb();
});

function openBulkTokenModal() {
    // Sinkronkan input hidden filter pada modal
    document.getElementById('bulk-token-q').value = document.getElementById('q-search-input').value.trim();
    document.getElementById('bulk-token-tingkat').value = document.getElementById('tingkat-filter').value;
    document.getElementById('bulk-token-kelas').value = document.getElementById('kelas-filter').value;
    document.getElementById('bulk-token-jurusan').value = document.getElementById('jurusan-filter').value;
    document.getElementById('bulk-token-status').value = document.getElementById('status-filter').value;
    const eskulEl = document.getElementById('eskul-status-filter');
    if (eskulEl && document.getElementById('bulk-token-eskul-status')) {
        document.getElementById('bulk-token-eskul-status').value = eskulEl.value;
    }

    const modal = document.getElementById('modal-bulk-token');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeBulkTokenModal() {
    const modal = document.getElementById('modal-bulk-token');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeConfirmModal();
        closeBulkTokenModal();
        closeWaModal();
    }
});

// Single Delete Confirmation
function confirmDeleteSingle(id, nama, nis) {
    openConfirmModal(
        'Hapus Siswa?',
        `Apakah Anda yakin ingin menghapus permanen data siswa "${nama}" (NIS: ${nis})? Tindakan ini tidak dapat dibatalkan.`,
        function() {
            const form = document.getElementById('form-single-delete');
            form.action = "{{ url('admin/students') }}/" + id;
            form.submit();
        },
        'Hapus Permanen',
        true
    );
}

// Bulk Selection Management
function getSelectedCheckboxes() {
    return Array.from(document.querySelectorAll('.student-checkbox:checked'));
}

function updateSelectedCount() {
    const checked = getSelectedCheckboxes();
    const count = checked.length;
    const bar = document.getElementById('bulk-selection-bar');
    const countText = document.getElementById('selected-count-text');
    const selectAll = document.getElementById('select-all-students');

    const totalVisible = document.querySelectorAll('.student-checkbox').length;
    if (selectAll) {
        selectAll.checked = totalVisible > 0 && count === totalVisible;
        selectAll.indeterminate = count > 0 && count < totalVisible;
    }

    if (count > 0) {
        countText.textContent = count;
        bar.classList.remove('hidden');
        bar.classList.add('flex');
    } else {
        bar.classList.add('hidden');
        bar.classList.remove('flex');
    }
}

function deselectAll() {
    document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
    const selectAll = document.getElementById('select-all-students');
    if (selectAll) {
        selectAll.checked = false;
        selectAll.indeterminate = false;
    }
    updateSelectedCount();
}

const selectAllEl = document.getElementById('select-all-students');
if (selectAllEl) {
    selectAllEl.addEventListener('change', function() {
        const isChecked = this.checked;
        document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = isChecked);
        updateSelectedCount();
    });
}

function confirmBulkDelete() {
    const checked = getSelectedCheckboxes();
    const count = checked.length;
    if (count === 0) return;

    openConfirmModal(
        'Hapus ' + count + ' Siswa Terpilih?',
        `Apakah Anda yakin ingin menghapus permanen ${count} data siswa yang dipilih? Tindakan ini tidak dapat dibatalkan.`,
        function() {
            const container = document.getElementById('bulk-delete-ids-container');
            container.innerHTML = '';
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                container.appendChild(input);
            });
            document.getElementById('form-bulk-delete').submit();
        },
        'Ya, Hapus ' + count + ' Data',
        true
    );
}

function confirmBulkTokenSelected() {
    const checked = getSelectedCheckboxes();
    const count = checked.length;
    if (count === 0) return;

    openConfirmModal(
        'Generate Token ' + count + ' Siswa?',
        `Sistem akan menerbitkan/memperbarui kredensial token login untuk ${count} siswa yang dipilih. Lanjutkan?`,
        function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('admin.students.bulk-token') }}";

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            const mode = document.createElement('input');
            mode.type = 'hidden';
            mode.name = 'mode';
            mode.value = 'selected';
            form.appendChild(mode);

            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        },
        'Generate Token',
        false
    );
}

// Export Data Multi-Format (Ala E-Voting)
function getStudentFilterQuery() {
    const params = new URLSearchParams();
    const search = document.getElementById('q-search-input')?.value.trim();
    const tingkat = document.getElementById('tingkat-filter')?.value;
    const kelas = document.getElementById('kelas-filter')?.value;
    const jurusan = document.getElementById('jurusan-filter')?.value;
    const eskul = document.getElementById('eskul-status-filter')?.value;
    const status = document.getElementById('status-filter')?.value;

    if (search) params.set('q', search);
    if (tingkat) params.set('tingkat', tingkat);
    if (kelas) params.set('kelas', kelas);
    if (jurusan) params.set('jurusan', jurusan);
    if (eskul) params.set('eskul_status', eskul);
    if (status) params.set('status', status);

    return params.toString();
}

function exportData(format) {
    const query = getStudentFilterQuery();
    const url = "{{ route('admin.students.export') }}" + `?format=${format}` + (query ? `&${query}` : '');
    window.location.href = url;
}

// Salin token ke clipboard dengan umpan balik icon
function salinToken(btn, token) {
    const ok = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>';
    const asli = btn.innerHTML;
    navigator.clipboard.writeText(token);
    btn.innerHTML = ok;
    setTimeout(() => { btn.innerHTML = asli; }, 1200);
}

// Kirim Token via WhatsApp (Ala E-Voting)
let currentWaPayload = null;

function sendTokenViaWa(nama, nis, token, defaultPhone = '') {
    currentWaPayload = { nama, nis, token };
    document.getElementById('wa-student-name').textContent = `${nama} (NIS: ${nis})`;
    document.getElementById('wa-phone-input').value = defaultPhone || '';

    const loginUrl = "{{ url('/siswa/login') }}";
    const msg = `Halo *${nama}* (NIS: *${nis}*),\n\nBerikut adalah kredensial token login untuk pemilihan Ekstrakurikuler:\n\n*Token Login:* ${token}\n*Tautan Login:* ${loginUrl}\n\nSilakan simpan token ini dengan baik untuk login ke portal siswa.\nTerima kasih.`;

    document.getElementById('wa-message-preview').value = msg;

    const modal = document.getElementById('modal-wa-token');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => document.getElementById('wa-phone-input').focus(), 100);
}

function closeWaModal() {
    const modal = document.getElementById('modal-wa-token');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    currentWaPayload = null;
}

function submitWaSend() {
    let phone = document.getElementById('wa-phone-input').value.trim();
    if (!phone) {
        alert('Silakan masukkan nomor WhatsApp tujuan!');
        document.getElementById('wa-phone-input').focus();
        return;
    }

    phone = phone.replace(/[^0-9]/g, '');
    if (phone.startsWith('0')) {
        phone = '62' + phone.substring(1);
    } else if (phone.startsWith('8')) {
        phone = '62' + phone;
    }

    if (phone.length < 9) {
        alert('Nomor WhatsApp tidak valid. Masukkan nomor yang benar (contoh: 081234567890).');
        return;
    }

    const message = document.getElementById('wa-message-preview').value;
    const waUrl = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;

    window.open(waUrl, '_blank');
    closeWaModal();
}

// Live Search & Filter AJAX with History & Pagination Sync
(function() {
    const searchInput = document.getElementById('q-search-input');
    const tingkatSelect = document.getElementById('tingkat-filter');
    const kelasSelect = document.getElementById('kelas-filter');
    const jurusanSelect = document.getElementById('jurusan-filter');
    const eskulSelect = document.getElementById('eskul-status-filter');
    const statusSelect = document.getElementById('status-filter');
    const tbody = document.getElementById('students-table-body');
    const pagination = document.getElementById('students-pagination');
    const totalBadge = document.getElementById('total-students-badge');
    const spinner = document.getElementById('search-spinner');

    let debounceTimer;

    function buildQuery() {
        return getStudentFilterQuery();
    }

    async function performSearch(targetUrl = null) {
        let url = targetUrl;
        if (!url) {
            const query = buildQuery();
            url = "{{ route('admin.students.index') }}" + (query ? '?' + query : '');
        }

        if (spinner) spinner.classList.remove('hidden');

        try {
            const res = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                const data = await res.json();
                tbody.innerHTML = data.rows;
                if (pagination) pagination.innerHTML = data.pagination;
                if (totalBadge && data.total !== undefined) totalBadge.innerText = data.total + ' siswa';
                window.history.replaceState({}, '', url);

                // Reset seleksi setelah ganti halaman / filter
                deselectAll();
            }
        } catch (err) {
            console.error('Error fetching live search:', err);
        } finally {
            if (spinner) spinner.classList.add('hidden');
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => performSearch(), 260);
        });
    }

    if (tingkatSelect) tingkatSelect.addEventListener('change', () => performSearch());
    if (kelasSelect) kelasSelect.addEventListener('change', () => performSearch());
    if (jurusanSelect) jurusanSelect.addEventListener('change', () => performSearch());
    if (eskulSelect) eskulSelect.addEventListener('change', () => performSearch());
    if (statusSelect) statusSelect.addEventListener('change', () => performSearch());

    // Intercept klik pada pagination agar filter tetap terjaga dan tidak reset!
    if (pagination) {
        pagination.addEventListener('click', function(e) {
            const anchor = e.target.closest('a');
            if (anchor && anchor.href) {
                e.preventDefault();
                performSearch(anchor.href);
            }
        });
    }

    // Menutup dropdown aksi saat klik di luar
    document.addEventListener('click', function(e) {
        if (!e.target.closest('details')) {
            document.querySelectorAll('#students-table-body details[open]').forEach(function(d) {
                d.removeAttribute('open');
            });
        }
    });
})();

function handleDirectStudentUpload(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    const btn = document.getElementById('btn-import-trigger');
    if (btn) {
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        btn.innerHTML = `<svg class="animate-spin h-3.5 w-3.5 text-blue-600 inline mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Mengimpor ${file.name}...`;
    }
    document.getElementById('direct-student-import-form').submit();
}

const WA_TEMPLATES = {
    token: "Halo {nama} ({kelas}),\nBerikut adalah kredensial akun kamu untuk Sistem Ekstrakurikuler SMK Bagimu Negeriku:\n\n*Token Login: {token}*\nNIS: {nis}\n\nSilakan buka tautan berikut untuk memilih ekstrakurikuler:\n{link}\n\nSimpan pesan ini dengan baik. Terima kasih!",
    pengumuman: "Halo {nama} ({kelas}),\nPemberitahuan resmi: Pendaftaran kegiatan ekstrakurikuler SMK Bagimu Negeriku untuk periode baru telah dibuka!\n\nSilakan segera login ke sistem melalui tautan di bawah dan tentukan pilihan eskul terbaikmu:\n{link}\n\nJangan sampai terlambat ya!",
    custom: "Halo {nama},\n\n[Tuliskan pengumuman atau instruksi Anda di sini]\n\nSalam,\nPengurus Ekstrakurikuler SMK Bagimu Negeriku"
};

function openWaBlastModal(mode = 'all') {
    const modal = document.getElementById('modal-wa-blast');
    if (!modal) return;

    const selectedCheckboxes = document.querySelectorAll('.student-checkbox:checked');
    const countSpan = document.getElementById('blast-modal-selected-count');
    if (countSpan) countSpan.textContent = selectedCheckboxes.length;

    if (mode === 'selected') {
        const rad = document.getElementById('blast-mode-selected');
        if (rad) rad.checked = true;
    } else {
        const rad = document.getElementById('blast-mode-all');
        if (rad) rad.checked = true;
    }

    const container = document.getElementById('blast-selected-ids-container');
    if (container) {
        container.innerHTML = '';
        selectedCheckboxes.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            container.appendChild(input);
        });
    }

    const textarea = document.getElementById('wa-blast-message-input');
    if (textarea && !textarea.value.trim()) {
        textarea.value = WA_TEMPLATES.token;
    }
    updateWaBlastPreview();

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeWaBlastModal() {
    const modal = document.getElementById('modal-wa-blast');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function setWaTemplate(type) {
    const textarea = document.getElementById('wa-blast-message-input');
    if (textarea && WA_TEMPLATES[type]) {
        textarea.value = WA_TEMPLATES[type];
        updateWaBlastPreview();
    }
}

function insertWaTag(tag) {
    const textarea = document.getElementById('wa-blast-message-input');
    if (!textarea) return;
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const val = textarea.value;
    textarea.value = val.substring(0, start) + tag + val.substring(end);
    textarea.focus();
    textarea.selectionStart = textarea.selectionEnd = start + tag.length;
    updateWaBlastPreview();
}

function updateWaBlastPreview() {
    const textarea = document.getElementById('wa-blast-message-input');
    const previewBox = document.getElementById('wa-blast-preview-box');
    if (!textarea || !previewBox) return;

    let text = textarea.value;
    text = text.replace(/\{nama\}/g, 'Budi Santoso')
               .replace(/\{nis\}/g, '10234')
               .replace(/\{nisn\}/g, '0081234567')
               .replace(/\{kelas\}/g, '10 RPL 1')
               .replace(/\{token\}/g, 'K7X9PQ')
               .replace(/\{link\}/g, window.location.origin + '/siswa/login')
               .replace(/\{aplikasi\}/g, 'Bagimu Extra');

    previewBox.textContent = text || '(Pratinjau pesan akan muncul di sini...)';
}

function handleWaBlastSubmit(form) {
    const mode = form.querySelector('input[name="mode"]:checked')?.value || 'all';
    const selectedCount = document.querySelectorAll('.student-checkbox:checked').length;
    
    if (mode === 'selected' && selectedCount === 0) {
        alert('Pilih minimal satu data siswa pada tabel dengan mencentang checkbox.');
        return false;
    }

    if (!confirm('Apakah Anda yakin ingin memulai pengiriman WhatsApp Blast ini sekarang?')) {
        return false;
    }

    const btn = document.getElementById('btn-submit-wa-blast');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = `<svg class="animate-spin h-3.5 w-3.5 text-white inline mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Mengirim WA Blast...`;
    }
    return true;
}
</script>
@endsection

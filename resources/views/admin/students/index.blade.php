@extends('layouts.staff')

@section('title', 'Direktori Siswa')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Direktori Siswa</h1>
        <p class="text-xs text-slate-500 mt-0.5">Kelola data induk siswa, kredensial token login, dan kartu login sekolah.</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        {{-- Form Input / Upload Langsung Berkas Siswa Multi-Format (Excel, CSV, PDF) --}}
        <form id="direct-student-import-form" method="POST" action="{{ route('admin.students.import.store') }}" enctype="multipart/form-data" class="inline-block m-0">
            @csrf
            <input type="file" id="direct-student-file-input" name="file" accept=".csv,.txt,.xlsx,.xls,.ods,.pdf" class="hidden" onchange="handleDirectStudentUpload(this)">
            <button type="button" onclick="document.getElementById('direct-student-file-input').click()" id="btn-import-trigger" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-sm transition-all hover:border-blue-500 hover:text-blue-700 cursor-pointer" title="Impor Data Siswa (Excel, CSV, PDF, dsb.)">
                <x-icon name="download" class="h-3.5 w-3.5 text-blue-600 shrink-0" />
                <span>Impor Data Siswa</span>
                <span class="hidden sm:inline-flex items-center rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium text-slate-600 border border-slate-200">
                    Excel, CSV, PDF
                </span>
            </button>
        </form>

        <a href="{{ route('admin.students.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-700 px-4 py-2.5 text-xs font-bold text-white hover:bg-blue-800 shadow-sm transition-colors">
            <x-icon name="plus" class="h-3.5 w-3.5" />
            <span>Tambah Siswa</span>
        </a>
    </div>
</div>

{{-- Filter & Real-Time Live Search --}}
<form id="filter-siswa-form" method="GET" action="{{ route('admin.students.index') }}" class="rounded-2xl border border-slate-200 bg-white p-4 mb-4 grid grid-cols-1 sm:grid-cols-4 gap-3 text-sm shadow-sm">
    <div class="relative">
        <label for="q-search-input" class="block font-semibold mb-1 text-slate-700 text-xs">Pencarian Real-Time (Nama / NIS)</label>
        <div class="relative">
            <input type="text" id="q-search-input" name="q" value="{{ request('q') }}"
                   class="w-full rounded-xl border-slate-300 text-sm pl-9 pr-8 focus:border-blue-500 focus:ring-blue-500"
                   placeholder="Ketik nama atau NIS..." autocomplete="off">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <x-icon name="search" class="h-4 w-4" />
            </div>
            <div id="search-spinner" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-blue-600">
                <svg class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
            </div>
        </div>
    </div>
    <div>
        <label for="kelas-filter" class="block font-semibold mb-1 text-slate-700 text-xs">Kelas</label>
        <input type="text" id="kelas-filter" name="kelas" value="{{ request('kelas') }}"
               class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
               placeholder="Semua kelas (cth: 10 RPL 1)">
    </div>
    <div>
        <label for="jurusan-filter" class="block font-semibold mb-1 text-slate-700 text-xs">Jurusan</label>
        <select id="jurusan-filter" name="jurusan" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 pr-10">
            <option value="">Semua Jurusan</option>
            @foreach ($jurusanList as $j)
                <option value="{{ $j->value }}" @selected(request('jurusan') === $j->value)>{{ $j->short() }} ({{ $j->label() }})</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="status-filter" class="block font-semibold mb-1 text-slate-700 text-xs">Status Siswa</label>
        <div class="flex items-center gap-2">
            <select id="status-filter" name="status" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 pr-10">
                <option value="">Semua Status</option>
                @foreach ($statusList as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ $st }}</option>
                @endforeach
            </select>
            <a href="{{ route('admin.students.index') }}" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors shrink-0" title="Reset filter">
                Reset
            </a>
        </div>
    </div>
</form>

{{-- Tabel Direktori Siswa --}}
<div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <div class="overflow-x-auto min-h-[300px]">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200 text-left text-xs font-bold uppercase tracking-wider text-slate-400">
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

<script>
(function() {
    const searchInput = document.getElementById('q-search-input');
    const kelasInput = document.getElementById('kelas-filter');
    const jurusanSelect = document.getElementById('jurusan-filter');
    const statusSelect = document.getElementById('status-filter');
    const tbody = document.getElementById('students-table-body');
    const pagination = document.getElementById('students-pagination');
    const spinner = document.getElementById('search-spinner');

    let debounceTimer;

    function buildQuery() {
        const params = new URLSearchParams();
        if (searchInput.value.trim()) params.set('q', searchInput.value.trim());
        if (kelasInput.value.trim()) params.set('kelas', kelasInput.value.trim());
        if (jurusanSelect.value) params.set('jurusan', jurusanSelect.value);
        if (statusSelect.value) params.set('status', statusSelect.value);
        return params.toString();
    }

    async function performSearch() {
        const query = buildQuery();
        const url = "{{ route('admin.students.index') }}" + (query ? '?' + query : '');

        if (spinner) spinner.classList.remove('hidden');

        try {
            const res = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            });

            if (res.ok) {
                const html = await res.text();
                tbody.innerHTML = html;
                window.history.replaceState({}, '', url);
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
            debounceTimer = setTimeout(performSearch, 260);
        });
    }

    if (kelasInput) {
        kelasInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(performSearch, 260);
        });
    }

    if (jurusanSelect) jurusanSelect.addEventListener('change', performSearch);
    if (statusSelect) statusSelect.addEventListener('change', performSearch);

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
</script>
@endsection

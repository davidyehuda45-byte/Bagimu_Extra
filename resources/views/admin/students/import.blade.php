@extends('layouts.staff')

@section('title', 'Impor Data Siswa')

@section('content')
<div class="mb-4">
    <h1 class="text-xl font-bold text-slate-900">Impor Data Siswa</h1>
    <p class="text-xs text-slate-500 mt-1">Unggah berkas data siswa dalam format <b>Excel (.xlsx, .xls, .ods)</b>, <b>CSV (.csv, .txt)</b>, atau <b>Dokumen PDF (.pdf)</b>.</p>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 w-full space-y-4 text-sm">
    @if (!$zipAktif)
        <div class="rounded-xl bg-blue-50/70 border border-blue-200/80 p-4 text-xs text-slate-700 leading-relaxed shadow-2xs flex items-start gap-2.5">
            <x-icon name="info" class="h-4 w-4 text-blue-600 shrink-0 mt-0.5" />
            <div>
                <p class="font-bold text-blue-950">Perhatian: ekstensi ZIP PHP belum aktif.</p>
                <p class="mt-1 text-slate-600">File <b>.xlsx</b> tidak bisa dibaca sampai <code class="bg-white px-1 rounded border border-blue-200 font-mono">extension=zip</code> diaktifkan di php.ini lalu web server di-restart. Sementara itu gunakan format <b>CSV</b> atau <b>.xls</b>.</p>
            </div>
        </div>
    @endif

    <div class="rounded-xl bg-blue-50/70 border border-blue-100 p-4 text-xs text-blue-900 leading-relaxed space-y-1.5">
        <p class="font-bold">Format Kolom yang Dikenali Otomatis:</p>
        <p>Header: <code class="bg-white px-1.5 py-0.5 rounded border border-blue-200 font-mono">nis, nisn, nama, kelas, jurusan, gender, no_absen</code></p>
        <p class="text-blue-700">Jurusan: <b>RPL, KULINER, DKV, TP, TKP</b> (boleh dikosongkan bila sudah tercantum di kolom kelas, mis. "10 RPL 1"). Gender diisi <b>L/P</b> (opsional), NISN &amp; no_absen opsional. Judul tabel dan baris kosong dilewati otomatis. Untuk PDF: hanya PDF berisi teks (bukan hasil scan/foto).</p>
        <div class="flex flex-wrap gap-2 pt-1">
            <a href="{{ route('admin.students.import.template', ['format' => 'csv']) }}" class="px-3 py-1.5 rounded-lg bg-white border border-blue-200 font-semibold text-blue-800 hover:bg-blue-100 transition">Unduh Template CSV</a>
            <a href="{{ route('admin.students.import.template', ['format' => 'xlsx']) }}" class="px-3 py-1.5 rounded-lg bg-white border border-blue-200 font-semibold text-blue-800 hover:bg-blue-100 transition">Unduh Template Excel</a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.students.import.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block font-medium text-slate-700 mb-1.5 text-xs">Pilih Berkas (Excel, CSV, atau PDF)</label>
            <input type="file" name="file" accept=".csv,.txt,.xlsx,.xls,.ods,.pdf" required class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:border-blue-600 focus:outline-none">
            <p class="text-[11px] text-slate-400 mt-1">Ukuran maksimal: 10 MB (.xlsx, .xls, .ods, .csv, .txt, .pdf)</p>
            @error('file')
                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex items-center gap-2 pt-2">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-700 font-bold text-white text-xs hover:bg-blue-800 transition shadow-sm">Mulai Impor</button>
            <a href="{{ route('admin.students.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 font-semibold text-slate-600 text-xs hover:bg-slate-50 transition">Kembali ke Direktori</a>
        </div>
    </form>
</div>
@endsection

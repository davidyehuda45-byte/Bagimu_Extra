@extends('layouts.siswa')

@section('title', 'Ketentuan & Aturan Pendaftaran')

@section('content')
<div class="space-y-6 w-full max-w-3xl mx-auto">
    {{-- Header Sambutan Siswa --}}
    <div class="flex items-center justify-between gap-4 bg-white rounded-3xl border border-slate-200/90 p-5 sm:p-6 shadow-xs">
        <div class="flex items-center gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-600 text-white font-black text-lg shadow-md shadow-blue-500/20">
                {{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}
            </span>
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-bold bg-blue-50 text-blue-700 mb-1">
                    Langkah 1 dari 2: Ketentuan & Aturan
                </span>
                <h1 class="text-lg sm:text-xl font-bold text-slate-900 leading-tight">Halo, {{ $user->name }}!</h1>
                <p class="text-xs text-slate-500 mt-0.5 font-mono">NIS: {{ $user->student?->nis ?? $user->username }} • Kelas: {{ $user->student?->kelas ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Kartu Ketentuan --}}
    <div class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xs space-y-6">
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                <x-icon name="clipboard" class="h-5 w-5" />
            </span>
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-900">Ketentuan & Aturan Pendaftaran</h2>
                <p class="text-xs text-slate-500">Harap baca dengan cermat sebelum menentukan pilihan ekstrakurikuler.</p>
            </div>
        </div>

        <ol class="space-y-4 text-sm text-slate-700 pl-4 list-decimal marker:text-blue-600 marker:font-bold leading-relaxed">
            <li class="pl-1">
                <strong class="font-bold text-slate-900">Satu Ekstrakurikuler per Siswa:</strong> Setiap siswa hanya dapat memilih <strong>satu</strong> eskul utama pada periode tahun ajaran aktif ({{ $periode?->periode ?? 'berjalan' }}).
            </li>
            <li class="pl-1">
                <strong class="font-bold text-slate-900">Token Sekali Pakai (One-Time Token):</strong> Token 8 digit yang Anda gunakan untuk masuk bersifat sekali pakai. Setelah data terkirim, token akan <strong>otomatis hangus</strong>.
            </li>
            <li class="pl-1">
                <strong class="font-bold text-slate-900">Data Pendaftaran Terkunci:</strong> Setelah Anda menekan tombol "Kirim Pendaftaran", pilihan eskul resmi tersimpan dan <strong>tidak dapat diubah atau dibatalkan</strong> kembali secara mandiri.
            </li>
            <li class="pl-1">
                <strong class="font-bold text-slate-900">Kuota Terbatas:</strong> Setiap eskul memiliki kapasitas kuota tertentu. Apabila kuota eskul yang Anda inginkan telah penuh, Anda dipersilakan memilih eskul alternatif lain yang masih tersedia.
            </li>
            <li class="pl-1">
                <strong class="font-bold text-slate-900">Logout Otomatis:</strong> Setelah pendaftaran berhasil terkirim, sistem akan menampilkan bukti penerimaan dan otomatis keluar (logout) dalam 5 detik untuk menjaga keamanan akun Anda.
            </li>
        </ol>

        <div class="rounded-2xl border border-amber-200 bg-amber-50/80 p-4 text-xs text-amber-900 flex items-start gap-3">
            <svg class="h-5 w-5 shrink-0 text-amber-600 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <p class="leading-relaxed">
                Pastikan Anda telah memikirkan dengan matang pilihan eskul Anda. Jika Anda mengalami kesulitan atau perlu pergantian darurat setelah mendaftar, hubungi Wali Kelas atau Pembina eskul terkait.
            </p>
        </div>

        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <span class="text-xs text-slate-400">Siap memilih ekstrakurikuler?</span>
            <a href="{{ route('siswa.daftar') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-6 py-3.5 text-sm font-bold text-white hover:bg-blue-700 transition shadow-sm shadow-blue-600/30">
                <span>Mulai Pendaftaran Sekarang</span>
                <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>
    </div>
</div>
@endsection

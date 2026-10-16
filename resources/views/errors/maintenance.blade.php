<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Dalam Pemeliharaan — {{ config('app.name', 'Bagimu Extra') }}</title>
    <x-favicon />
    <meta name="theme-color" content="#1e3a8a">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col justify-between">
@php
    $sekolah = \App\Models\AppSetting::get('school.name', 'SMK Bagimu Negeriku');
    $pesan = $pesan ?? \App\Models\AppSetting::get('app.maintenance_msg', 'Sistem sedang dalam proses pemeliharaan dan optimalisasi data.');
    $eta = $eta ?? \App\Models\AppSetting::get('app.maintenance_eta');
@endphp

    {{-- Header minimalis --}}
    <header class="w-full border-b border-slate-200/80 bg-white/90 backdrop-blur px-4 py-4 sm:px-8">
        <div class="flex w-full items-center justify-between">
            <div class="flex items-center gap-3">
                <x-school-logo :size="36" />
                <div>
                    <p class="font-bold text-slate-900 leading-tight text-sm sm:text-base">{{ config('app.name', 'Bagimu Extra') }}</p>
                    <p class="text-xs text-slate-500">{{ $sekolah }}</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-600/20">
                <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                Mode Perawatan Aktif
            </span>
        </div>
    </header>

    {{-- Konten Utama --}}
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 md:p-10">
        <div class="w-full max-w-xl text-center">
            {{-- Icon Card --}}
            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-3xl bg-blue-50 text-blue-700 ring-8 ring-blue-50/50 shadow-sm">
                <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233l2.844-2.844a2.652 2.652 0 00-3.75-3.75l-2.844 2.844m0 0l-3.03 2.496a2.652 2.652 0 00-.766 1.208" />
                </svg>
            </div>

            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Sistem Sedang Dalam Perawatan
            </h1>
            <p class="mt-3 text-sm sm:text-base text-slate-600 leading-relaxed max-w-md mx-auto">
                Layanan pendaftaran dan portal ekstrakurikuler sementara waktu sedang dalam pemeliharaan berkala untuk peningkatan performa.
            </p>

            {{-- Pesan Penjelasan dari Admin --}}
            <div class="mt-6 rounded-2xl border border-blue-100 bg-white p-5 shadow-sm text-left">
                <div class="flex items-start gap-3">
                    <div class="rounded-xl bg-blue-50 p-2 text-blue-700 shrink-0">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Pemberitahuan Administrator</h2>
                        <p class="mt-1 text-sm font-medium text-slate-800 leading-relaxed whitespace-pre-line">
                            {{ $pesan }}
                        </p>
                    </div>
                </div>

                @if(!empty($eta))
                    <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-500 flex items-center gap-1.5 font-medium">
                            <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Estimasi Selesai:
                        </span>
                        <span class="inline-flex items-center rounded-lg bg-amber-50 px-2.5 py-1 font-bold text-amber-800 ring-1 ring-inset ring-amber-600/20">
                            {{ $eta }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Action buttons --}}
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <button type="button" onclick="window.location.reload()" class="inline-flex items-center gap-2 rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-800 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Muat Ulang Halaman
                </button>
            </div>
        </div>
    </main>

    {{-- Footer dengan link akses rapi --}}
    <footer class="w-full border-t border-slate-200/80 bg-white/70 py-4 px-4 sm:px-8 text-center text-xs text-slate-500">
        <div class="w-full flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>© {{ date('Y') }} {{ config('app.name', 'Bagimu Extra') }} — {{ $sekolah }}</span>
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('home') }}" class="text-slate-400 hover:text-blue-700 hover:underline transition">
                    Beranda
                </a>
                <a href="{{ route('siswa.login') }}" class="text-slate-400 hover:text-blue-700 hover:underline transition">
                    Portal Siswa
                </a>
                <a href="{{ route('verify.form') }}" class="text-slate-400 hover:text-blue-700 hover:underline transition">
                    Verifikasi
                </a>
                <a href="{{ route('admin.login') }}" class="text-slate-400 hover:text-blue-700 hover:underline transition">
                    Pintu Masuk Staf &rarr;
                </a>
            </div>
        </div>
    </footer>
</body>
</html>


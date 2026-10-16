<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-favicon />
    <meta name="theme-color" content="#2563eb">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" media="print" onload="this.media='all'" />
    <noscript><link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" /></noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white">
@php($sekolah = \App\Models\AppSetting::get('school.name', config('app.name', 'Bagimu Extra')))
<main class="flex min-h-screen">
    {{-- Panel Branding Desktop - Bersih & Cerah --}}
    <div class="relative hidden w-1/2 flex-col justify-between overflow-hidden bg-blue-600 p-12 text-white lg:flex">
        <div class="flex items-center gap-3.5">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 border border-white/20 p-1">
                <x-school-logo :size="40" />
            </div>
            <div>
                <p class="text-base font-extrabold tracking-tight">{{ config('app.name', 'Bagimu Extra') }}</p>
                <p class="text-xs font-medium text-blue-100">{{ $sekolah }}</p>
            </div>
        </div>

        <div class="space-y-6 max-w-lg">
            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold bg-white/15 text-white border border-white/20">
                <x-icon name="user" class="h-3.5 w-3.5 text-blue-100" />
                Portal Ekstrakurikuler Siswa
            </span>
            <h1 class="text-3xl xl:text-4xl font-black leading-tight tracking-tight text-white">
                Pendaftaran & Presensi Ekstrakurikuler
                <span class="block text-blue-100 font-extrabold">
                    Digital, Praktis & Cepat.
                </span>
            </h1>
            <p class="text-sm leading-relaxed text-blue-100">
                Pilih eskul favoritmu, dapatkan E-Card digital dengan QR Code unik, dan pantau rekap kehadiran serta nilai akhir langsung dari perangkat ponselmu.
            </p>

            <ul class="space-y-3 pt-2 text-sm text-white">
                <li class="flex items-center gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white">
                        <x-icon name="check" class="h-4 w-4" />
                    </span>
                    <span>Masuk instan menggunakan NIS dan Token Kartu Pelajar</span>
                </li>
                <li class="flex items-center gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white">
                        <x-icon name="check" class="h-4 w-4" />
                    </span>
                    <span>E-Card presensi digital ber-QR Code untuk absensi latihan</span>
                </li>
                <li class="flex items-center gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white">
                        <x-icon name="check" class="h-4 w-4" />
                    </span>
                    <span>Transparansi persentase kehadiran dan sertifikat kegiatan resmi</span>
                </li>
            </ul>
        </div>

        <div class="flex items-center justify-between text-xs text-blue-100 border-t border-white/15 pt-4">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Bagimu Extra') }} — {{ $sekolah }}</p>
            <a href="{{ route('home') }}" class="font-medium text-white hover:underline transition">Halaman Publik &rarr;</a>
        </div>
    </div>

    {{-- Panel Form Kanan --}}
    <div class="flex w-full items-center justify-center bg-slate-50/70 px-4 py-12 lg:w-1/2">
        <div class="w-full max-w-md">
            {{-- Mobile Logo --}}
            <div class="mb-6 flex items-center gap-3 lg:hidden">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-sm p-1">
                    <x-school-logo :size="36" />
                </div>
                <div>
                    <p class="font-bold text-slate-900 leading-tight">{{ config('app.name', 'Bagimu Extra') }}</p>
                    <p class="text-xs text-slate-500">{{ $sekolah }}</p>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-sm">
                <div class="mb-5">
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10 mb-2.5">
                        <x-icon name="user" class="h-3.5 w-3.5" /> Portal Siswa
                    </span>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Masuk Akun Siswa</h2>
                    <p class="mt-1 text-xs sm:text-sm text-slate-500">Gunakan NIS dan Token 8 digit yang tertera di kartu pelajar Anda.</p>
                </div>

                <form method="POST" action="{{ route('siswa.login') }}" class="space-y-4" x-data="{ loading: false }" @submit="loading = true">
                    @csrf

                    <div>
                        <x-input-label for="nis" value="Nomor Induk Siswa (NIS)" />
                        <x-text-input id="nis" class="mt-1 block w-full text-base tracking-wider font-semibold placeholder:font-normal placeholder:tracking-normal" type="text"
                                        inputmode="numeric" autocomplete="username" name="nis" :value="old('nis')"
                                        required autofocus placeholder="cth: 10001" />
                        <x-input-error :messages="$errors->get('nis')" class="mt-2" />
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <x-input-label for="token" value="Token / Kata Sandi" />
                            <span class="text-[11px] text-slate-400">8 digit kartu</span>
                        </div>
                        <x-text-input id="token" class="mt-1 block w-full text-center text-xl tracking-[0.4em] font-mono font-bold placeholder:font-sans placeholder:tracking-normal placeholder:font-normal"
                                        type="text" inputmode="text" autocapitalize="off" spellcheck="false" autocomplete="current-password"
                                        name="token" required maxlength="100" placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('token')" class="mt-2" />
                        <p class="mt-1.5 text-[11px] text-slate-400 leading-normal">
                            Masukkan token 8 karakter dari kartu pelajar atau kata sandi yang pernah Anda atur di menu Profil.
                        </p>
                    </div>

                    <x-primary-button class="w-full justify-center py-3 text-sm font-bold shadow-sm shadow-blue-600/25" ::disabled="loading">
                        <span x-show="! loading" class="inline-flex items-center gap-2">
                            <span>Masuk Sekarang</span>
                            <x-icon name="arrow-right" class="h-4 w-4" />
                        </span>
                        <span x-show="loading" x-cloak class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"/></svg>
                            <span>Memproses Masuk…</span>
                        </span>
                    </x-primary-button>

                    <div class="mt-5 space-y-2 border-t border-slate-100 pt-4 text-center text-xs text-slate-500">
                        <p class="text-slate-400">Lupa token? Hubungi bagian Tata Usaha (TU) sekolah untuk cetak ulang token.</p>
                        <div class="pt-1">
                            <a href="{{ route('home') }}" class="font-medium text-slate-600 hover:text-blue-700 hover:underline">
                                &larr; Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
</body>
</html>

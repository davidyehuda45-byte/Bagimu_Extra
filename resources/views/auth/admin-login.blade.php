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
    {{-- Panel Branding Desktop - Sesuai Mockup Desain Bersih & Cerah --}}
    <div class="relative hidden w-1/2 flex-col justify-between overflow-hidden bg-white p-10 xl:p-14 text-slate-800 lg:flex border-r border-slate-200/80">
        {{-- Ambient Glow & Background Waves --}}
        <div class="pointer-events-none absolute -top-20 -right-20 h-80 w-80 rounded-full bg-blue-100/40 blur-3xl z-0"></div>
        <svg class="pointer-events-none absolute -bottom-6 -left-6 w-96 max-w-full z-0 opacity-60" viewBox="0 0 400 160" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M-20 60 C80 120 180 20 420 110 L420 200 L-20 200 Z" fill="#eff6ff" />
            <path d="M-20 90 C90 140 210 50 420 130 L420 200 L-20 200 Z" fill="#dbeafe" opacity="0.6" />
        </svg>

        {{-- Visual Arch Gedung Sekolah (Sesuai Mockup) --}}
        <div class="pointer-events-none absolute right-0 top-1/2 -translate-y-1/2 h-[70%] xl:h-[78%] max-h-[620px] w-auto select-none z-0 hidden lg:block">
            <img src="{{ asset('login-illustration.png') }}" alt="SMK Bagimu Negeriku" class="h-full w-auto object-contain object-right" />
        </div>

        {{-- Header Sekolah --}}
        <div class="relative z-10 flex items-center gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white border border-slate-200/80 p-1 shadow-xs">
                <x-school-logo :size="40" />
            </div>
            <div class="h-9 w-px bg-slate-200"></div>
            <div>
                <p class="text-sm sm:text-base font-extrabold tracking-tight text-slate-900 uppercase leading-none">{{ $sekolah }}</p>
                <p class="text-[11px] sm:text-xs font-medium text-slate-500 mt-1">4B Berbudi • Berkualitas • Berdaya • Berhasil</p>
            </div>
        </div>

        {{-- Konten Utama (Persis Sesuai Mockup) --}}
        <div class="relative z-10 space-y-6 max-w-[310px] xl:max-w-[360px] my-auto py-6">
            <span class="inline-flex items-center gap-2 rounded-full px-3.5 py-1 text-xs font-semibold bg-blue-50 text-blue-600 border border-blue-100 shadow-xs">
                <x-icon name="shield" class="h-3.5 w-3.5 text-blue-600" />
                Portal Khusus Staf & Pembina
            </span>
            <h1 class="text-2xl sm:text-3xl xl:text-4xl font-black leading-[1.18] tracking-tight text-slate-900">
                Pengelolaan<br>
                <span class="text-blue-600">Ekstrakurikuler</span><br>
                Terpadu & Profesional.
            </h1>
            <p class="text-xs sm:text-sm leading-relaxed text-slate-500 max-w-[300px] xl:max-w-[340px]">
                Akses khusus guru, pembina eskul, dan tim tata usaha untuk verifikasi pendaftaran siswa, rekap presensi QR, penilaian rapor ekstrakurikuler, dan penerbitan sertifikat digital.
            </p>

            <div class="space-y-3.5 pt-1 max-w-[300px] xl:max-w-[340px]">
                <div class="flex items-center gap-3.5">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-blue-50 border border-blue-100 text-blue-600 shadow-xs">
                        <x-icon name="clock" class="h-5 w-5" />
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-slate-600 leading-snug">
                        Monitoring pendaftar dan kuota eskul secara real-time
                    </p>
                </div>
                <div class="flex items-center gap-3.5">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-blue-50 border border-blue-100 text-blue-600 shadow-xs">
                        <x-icon name="qr" class="h-5 w-5" />
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-slate-600 leading-snug">
                        Pemindaian QR presensi dan validasi surat izin otomatis
                    </p>
                </div>
                <div class="flex items-center gap-3.5">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-blue-50 border border-blue-100 text-blue-600 shadow-xs">
                        <x-icon name="award" class="h-5 w-5" />
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-slate-600 leading-snug">
                        Penilaian predikat siswa & cetak e-Sertifikat resmi
                    </p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="relative z-10 flex items-center justify-between text-xs text-slate-400 border-t border-slate-200/80 pt-4">
            <p>&copy; {{ date('Y') }} {{ $sekolah }}</p>
            <a href="{{ route('home') }}" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline flex items-center gap-1 transition">
                <span>Halaman Publik</span>
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    {{-- Panel Form Kanan - Warna Biru Khas Bagimu Extra --}}
    <div class="relative flex w-full items-center justify-center bg-blue-600 px-4 py-12 lg:w-1/2 overflow-hidden">
        {{-- Ambient Glow Dekoratif --}}
        <div class="pointer-events-none absolute -top-24 -right-24 h-96 w-96 rounded-full bg-white/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-blue-900/30 blur-3xl"></div>

        <div class="relative z-10 w-full max-w-md">
            {{-- Mobile Logo --}}
            <div class="mb-6 flex items-center gap-3 lg:hidden">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/15 border border-white/20 p-1">
                    <x-school-logo :size="36" />
                </div>
                <div>
                    <p class="font-bold text-white leading-tight">{{ config('app.name', 'Bagimu Extra') }}</p>
                    <p class="text-xs text-blue-100">{{ $sekolah }}</p>
                </div>
            </div>

            <div class="rounded-3xl border border-white/20 bg-white p-6 sm:p-8 shadow-2xl">
                <div class="mb-5">
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold bg-slate-100 text-slate-800 ring-1 ring-inset ring-slate-700/10 mb-2.5">
                        <x-icon name="shield" class="h-3.5 w-3.5 text-blue-600" /> Portal Staf & Pembina
                    </span>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Login Staf / Pembina</h2>
                    <p class="mt-1 text-xs sm:text-sm text-slate-500">Gunakan username atau alamat email akun dinas Anda.</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="login" value="Username / Alamat Email" />
                        <x-text-input id="login" class="mt-1 block w-full text-sm" type="text"
                                        name="login" :value="old('login')" required autofocus
                                        placeholder="cth: pembina@sekolah.sch.id atau username"
                                        autocomplete="username" />
                        <x-input-error :messages="$errors->get('login')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" value="Kata Sandi (Password)" />
                        <x-text-input id="password" class="mt-1 block w-full text-sm" type="password"
                                        name="password" required
                                        placeholder="••••••••"
                                        autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    @if (Route::has('password.request'))
                        <div class="flex items-center justify-end text-sm pt-0.5">
                            <a class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline" href="{{ route('password.request') }}">
                                Lupa kata sandi?
                            </a>
                        </div>
                    @endif

                    <x-primary-button class="w-full justify-center py-3 text-sm font-bold shadow-sm shadow-blue-600/25">
                        <span class="inline-flex items-center gap-2">
                            <span>Masuk ke Panel Staf</span>
                            <x-icon name="arrow-right" class="h-4 w-4" />
                        </span>
                    </x-primary-button>

                    <div class="mt-5 space-y-2 border-t border-slate-100 pt-4 text-center text-xs text-slate-500">
                        <div class="flex items-center justify-center gap-3">
                            <a href="{{ route('home') }}" class="font-medium text-slate-600 hover:text-blue-700 hover:underline">
                                &larr; Kembali ke Beranda
                            </a>
                            <span class="text-slate-300">•</span>
                            <a href="{{ route('siswa.login') }}" class="font-medium text-blue-600 hover:text-blue-700 hover:underline">
                                Portal Siswa &rarr;
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

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-favicon />
    <meta name="theme-color" content="#1e293b">
    <title>Login Administrator — {{ config('app.name', 'Bagimu Extra') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" media="print" onload="this.media='all'" />
    <noscript><link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" /></noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-slate-900 selection:text-white">
@php
    $sekolah = \App\Models\AppSetting::get('school.name', config('app.name', 'Bagimu Extra'));
@endphp
<main class="flex min-h-screen">
    {{-- Panel Branding Desktop - Dark Corporate Khusus Admin --}}
    <div class="relative hidden w-1/2 flex-col justify-between overflow-hidden bg-gradient-to-b from-slate-900 via-slate-800 to-slate-950 p-10 xl:p-14 text-white lg:flex">
        <div class="pointer-events-none absolute -top-32 -left-32 h-96 w-96 rounded-full bg-blue-500/15 blur-3xl z-0"></div>
        <div class="pointer-events-none absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-slate-700/30 blur-3xl z-0"></div>

        <svg class="pointer-events-none absolute inset-0 h-full w-full opacity-20 z-0" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="dot-grid-admin" width="26" height="26" patternUnits="userSpaceOnUse">
                    <circle cx="2" cy="2" r="1.25" fill="#ffffff" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#dot-grid-admin)" />
        </svg>

        <div class="pointer-events-none absolute bottom-0 right-0 h-[82%] xl:h-[90%] 2xl:h-[95%] max-h-[960px] w-auto select-none z-0 opacity-40">
            <img src="{{ asset('images/gedung-mockup.png') }}?v={{ filemtime(public_path('images/gedung-mockup.png')) }}"
                 alt="Gedung SMK Bagimu Negeriku"
                 class="h-full w-auto object-contain object-right-bottom drop-shadow-2xl grayscale" />
        </div>

        {{-- Header Sekolah --}}
        <div class="relative z-10 flex items-center gap-4">
            <x-school-logo :size="64" class="shadow-lg ring-4 ring-white/10" />
            <div>
                <p class="text-xl xl:text-2xl font-black tracking-tight text-white leading-tight">{{ config('app.name', 'Bagimu Extra') }}</p>
                <p class="text-xs sm:text-sm font-bold text-slate-300 tracking-wide mt-0.5">{{ $sekolah }}</p>
                <p class="text-xs font-medium text-slate-400 mt-1 tracking-wide">
                    Panel Kontrol Administrasi & Sistem
                </p>
            </div>
        </div>

        {{-- Konten Utama --}}
        <div class="relative z-10 my-auto py-8 space-y-6 max-w-lg">
            <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-semibold bg-white/10 text-slate-200 border border-white/15 backdrop-blur-md shadow-xs">
                <x-icon name="shield" class="h-3.5 w-3.5 text-slate-300" />
                Portal Khusus Administrator
            </span>
            <h1 class="text-3xl sm:text-4xl xl:text-5xl font-black leading-[1.12] tracking-tight text-white drop-shadow-xs">
                Pusat Kendali<br>
                Sistem Informasi<br>
                Ekstrakurikuler.
            </h1>
            <p class="text-sm sm:text-base leading-relaxed text-slate-300 max-w-md">
                Akses terproteksi khusus administrator untuk konfigurasi master data eskul, manajemen akun pembina, pengaturan periode, dan audit sistem.
            </p>
        </div>

        {{-- Footer --}}
        <div class="relative z-10 flex items-center justify-between text-xs sm:text-sm text-slate-400 border-t border-slate-700/60 pt-4">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Bagimu Extra') }} — {{ $sekolah }}</p>
            <a href="{{ route('home') }}" class="font-semibold text-slate-300 hover:text-white hover:underline flex items-center gap-1.5 transition">
                <span>Halaman Utama</span>
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    {{-- Panel Form Kanan --}}
    <div class="relative flex w-full items-center justify-center bg-slate-50/70 px-4 py-12 lg:w-1/2">
        <div class="relative z-10 w-full max-w-md">
            {{-- Mobile Logo --}}
            <div class="mb-6 flex items-center gap-3.5 lg:hidden">
                <x-school-logo :size="52" class="shadow-sm" />
                <div>
                    <p class="text-base sm:text-lg font-extrabold text-slate-900 leading-tight">{{ config('app.name', 'Bagimu Extra') }}</p>
                    <p class="text-xs sm:text-sm font-semibold text-slate-600 mt-0.5">{{ $sekolah }}</p>
                    <p class="text-[11px] sm:text-xs font-medium text-slate-500 mt-0.5 tracking-wide">Pusat Kendali Administrator</p>
                </div>
            </div>

            <div class="card relative rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xl shadow-slate-200/50">
                <div class="mb-5">
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold bg-slate-100 text-slate-800 ring-1 ring-inset ring-slate-700/10 mb-2.5">
                        <x-icon name="shield" class="h-3.5 w-3.5 text-slate-700" /> Portal Administrator
                    </span>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Login Administrator</h2>
                    <p class="mt-1 text-xs sm:text-sm text-slate-500">Khusus administrator dan staf pengelola sistem sekolah.</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                @if ($errors->has('login'))
                    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs font-medium text-rose-700">
                        {{ $errors->first('login') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login') }}" id="loginForm" class="space-y-4" x-data="{ loading: false, showPassword: false }">
                    @csrf

                    <div>
                        <x-input-label for="login" value="Username / Alamat Email Admin" />
                        <x-text-input id="login" class="mt-1 block w-full text-sm font-medium" type="text"
                                      name="login" :value="old('login')" autofocus
                                      autocomplete="username" placeholder="cth: admin atau admin@sekolah.sch.id" required />
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <x-input-label for="password" value="Kata Sandi Admin" />
                            @if (Route::has('password.request'))
                                <a class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline" href="{{ route('password.request') }}">
                                    Lupa sandi?
                                </a>
                            @endif
                        </div>
                        <div class="relative mt-1">
                            <input id="password"
                                   :type="showPassword ? 'text' : 'password'"
                                   name="password"
                                   required
                                   autocomplete="current-password"
                                   placeholder="••••••••"
                                   class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 pr-10 text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:border-slate-800 focus:outline-hidden focus:ring-2 focus:ring-slate-800/20" />
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-hidden" tabindex="-1">
                                <svg x-show="!showPassword" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg x-show="showPassword" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9.88 9.88 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-5 py-3.5 text-sm font-bold text-white hover:bg-slate-800 transition shadow-md shadow-slate-900/20 cursor-pointer">
                        <span>Masuk ke Panel Administrator</span>
                        <x-icon name="arrow-right" class="h-4 w-4" />
                    </button>
                </form>

                <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                    <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                        ← Kembali ke Halaman Utama
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>

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
                <x-icon name="shield" class="h-3.5 w-3.5 text-blue-100" />
                Portal Khusus Staf & Pembina
            </span>
            <h1 class="text-3xl xl:text-4xl font-black leading-tight tracking-tight text-white">
                Pengelolaan Ekstrakurikuler
                <span class="block text-blue-100 font-extrabold">
                    Terpadu & Profesional.
                </span>
            </h1>
            <p class="text-sm leading-relaxed text-blue-100">
                Akses khusus guru, pembina eskul, dan tim tata usaha untuk verifikasi pendaftaran siswa, rekap presensi QR, penilaian rapor ekstrakurikuler, dan penerbitan sertifikat digital.
            </p>

            <ul class="space-y-3 pt-2 text-sm text-white">
                <li class="flex items-center gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white">
                        <x-icon name="check" class="h-4 w-4" />
                    </span>
                    <span>Monitoring pendaftar dan kuota eskul secara real-time</span>
                </li>
                <li class="flex items-center gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white">
                        <x-icon name="check" class="h-4 w-4" />
                    </span>
                    <span>Pemindai QR presensi dan validasi surat izin otomatis</span>
                </li>
                <li class="flex items-center gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white">
                        <x-icon name="check" class="h-4 w-4" />
                    </span>
                    <span>Penilaian predikat siswa & cetak e-Sertifikat resmi</span>
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
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-sm p-1">
                    <x-school-logo :size="36" />
                </div>
                <div>
                    <p class="font-bold text-slate-900 leading-tight">{{ config('app.name', 'Bagimu Extra') }}</p>
                    <p class="text-xs text-slate-500">{{ $sekolah }}</p>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-sm">
                {{-- Tab Pilihan Pintu Masuk: Siswa & Pembina --}}
                <div class="grid grid-cols-2 p-1.5 mb-6 bg-slate-100 rounded-2xl text-xs sm:text-sm font-bold text-center border border-slate-200/80">
                    <a href="{{ route('siswa.login') }}" class="py-2.5 rounded-xl text-slate-600 hover:text-blue-700 hover:bg-white transition flex items-center justify-center gap-2">
                        <x-icon name="user" class="h-4 w-4" />
                        <span>Siswa</span>
                    </a>
                    <div class="py-2.5 rounded-xl bg-blue-600 text-white shadow-xs flex items-center justify-center gap-2">
                        <x-icon name="user-group" class="h-4 w-4" />
                        <span>Pembina</span>
                    </div>
                </div>

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

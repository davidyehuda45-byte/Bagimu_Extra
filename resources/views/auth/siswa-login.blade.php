<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Siswa — {{ config('app.name', 'Bagimu Extra') }}</title>
    <meta name="theme-color" content="#1e3a8a">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" media="print" onload="this.media='all'" />
    <noscript><link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" /></noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
@php($sekolah = \App\Models\AppSetting::get('school.name', config('app.name', 'Bagimu Extra')))
<main class="flex min-h-screen">
    {{-- Panel branding (desktop) --}}
    <div class="relative hidden w-1/2 flex-col justify-between overflow-hidden bg-blue-900 p-12 text-white lg:flex">
        <div aria-hidden class="absolute inset-0" style="background-image: radial-gradient(rgba(255,255,255,0.13) 1px, transparent 1px); background-size: 22px 22px;"></div>
        <div class="relative flex items-center gap-3">
            <x-school-logo :size="48" />
            <div>
                <p class="text-lg font-bold leading-tight">{{ config('app.name', 'Bagimu Extra') }}</p>
                <p class="text-xs text-blue-200">{{ $sekolah }}</p>
            </div>
        </div>

        <div class="relative space-y-6">
            <h1 class="text-4xl font-extrabold leading-tight">
                Pendaftaran Ekstrakurikuler
                <br>
                <span class="text-blue-200">Jadi Lebih Mudah.</span>
            </h1>
            <div class="max-w-md space-y-3">
                <p class="text-sm leading-relaxed text-blue-100">
                    Daftar eskul langsung dari HP pakai NIS dan token di kartu pelajar kamu. Tanpa install aplikasi.
                </p>
            </div>
            <ul class="space-y-3 text-sm">
                <li class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/15"><x-icon name="check" class="h-4 w-4" /></span>
                    Login cepat dengan NIS + token kartu pelajar
                </li>
                <li class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/15"><x-icon name="check" class="h-4 w-4" /></span>
                    Satu akun hanya dapat mendaftar satu kali
                </li>
                <li class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/15"><x-icon name="check" class="h-4 w-4" /></span>
                    Pantau status dan kehadiran dari HP
                </li>
            </ul>
        </div>

        <p class="relative text-xs text-blue-200">
            © {{ date('Y') }} {{ config('app.name', 'Bagimu Extra') }} — {{ $sekolah }}
        </p>
    </div>

    {{-- Panel form --}}
    <div class="flex w-full items-center justify-center bg-slate-50 px-4 py-12 lg:w-1/2">
        <div class="w-full max-w-md">
            <div class="mb-4 flex items-center gap-3 lg:hidden">
                <x-school-logo :size="40" />
                <div>
                    <p class="font-bold text-slate-900">{{ config('app.name', 'Bagimu Extra') }}</p>
                    <p class="text-xs text-slate-500">{{ $sekolah }}</p>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">Login Siswa</h2>
                <p class="mt-1 text-sm text-slate-500">Pakai NIS + token dari kartu pelajar.</p>

                <form method="POST" action="{{ route('siswa.login') }}" class="mt-4 space-y-4" x-data="{ loading: false }" @submit="loading = true">
                    @csrf

                    <div>
                        <x-input-label for="nis" value="NIS" />
                        <x-text-input id="nis" class="mt-1 block w-full text-lg tracking-widest" type="text"
                                        inputmode="numeric" autocomplete="username" name="nis" :value="old('nis')"
                                        required autofocus placeholder="cth: 10001" />
                        <x-input-error :messages="$errors->get('nis')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="token" value="Token / Password" />
                        <x-text-input id="token" class="mt-1 block w-full text-center text-2xl tracking-[0.5em] font-mono"
                                        type="text" inputmode="text" autocapitalize="off" spellcheck="false" autocomplete="current-password"
                                        name="token" required maxlength="100" placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('token')" class="mt-2" />
                        <p class="mt-1 text-[11px] text-slate-400">Token 8 digit angka dari kartu pelajar, atau password yang diatur di Profil.</p>
                    </div>

                    <x-primary-button class="w-full justify-center py-3" ::disabled="loading">
                        <span x-show="! loading">Masuk</span>
                        <span x-show="loading" x-cloak class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"/></svg>
                            Memproses…
                        </span>
                    </x-primary-button>

                    <div class="mt-4 space-y-2 border-t border-slate-100 pt-3 text-center text-xs text-slate-500">
                        <p>Lupa token? Minta token baru ke TU — token lama otomatis hangus.</p>
                        <a href="{{ route('home') }}" class="inline-block hover:text-blue-700 hover:underline">
                            &larr; Kembali ke Beranda
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
</body>
</html>

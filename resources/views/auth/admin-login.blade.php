<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Guru — {{ config('app.name', 'Bagimu Extra') }}</title>
    <meta name="theme-color" content="#1e3a8a">
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
                Kelola Ekstrakurikuler
                <br>
                <span class="text-blue-200">Dalam Satu Panel Guru.</span>
            </h1>
            <div class="max-w-md space-y-3">
                <p class="text-sm leading-relaxed text-blue-100">
                    Akses portal khusus guru dan pembina untuk verifikasi pendaftar, absensi latihan, penilaian predikat, dan laporan kegiatan.
                </p>
            </div>
            <ul class="space-y-3 text-sm">
                <li class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/15"><x-icon name="check" class="h-4 w-4" /></span>
                    Pendaftaran langsung siswa & kuota eskul
                </li>
                <li class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/15"><x-icon name="check" class="h-4 w-4" /></span>
                    Absensi digital terkunci & rekap kehadiran
                </li>
                <li class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/15"><x-icon name="check" class="h-4 w-4" /></span>
                    Penilaian siswa & sertifikat ber-QR publik
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

            <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm">
                <div class="mb-5">
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold bg-blue-50 text-blue-800 ring-1 ring-inset ring-blue-700/10 mb-2">
                        <x-icon name="shield" class="h-3.5 w-3.5" /> Portal Khusus Guru
                    </span>
                    <h2 class="text-xl font-bold text-slate-900">Login Guru</h2>
                    <p class="mt-1 text-sm text-slate-500">Masuk menggunakan username atau email akun guru Anda.</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="login" value="Username / Email" />
                        <x-text-input id="login" class="mt-1 block w-full" type="text"
                                        name="login" :value="old('login')" required autofocus
                                        placeholder="cth: guru@sekolah.sch.id atau username"
                                        autocomplete="username" />
                        <x-input-error :messages="$errors->get('login')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" value="Password" />
                        <x-text-input id="password" class="mt-1 block w-full" type="password"
                                        name="password" required
                                        placeholder="••••••••"
                                        autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    @if (Route::has('password.request'))
                        <div class="flex items-center justify-end text-sm">
                            <a class="text-xs font-medium text-blue-600 hover:text-blue-700 hover:underline" href="{{ route('password.request') }}">
                                Lupa password?
                            </a>
                        </div>
                    @endif

                    <x-primary-button class="w-full justify-center py-3 text-sm font-bold shadow-sm">
                        Masuk ke Portal Guru
                    </x-primary-button>

                    <div class="mt-4 flex items-center justify-center text-xs text-slate-500 pt-3 border-t border-slate-100">
                        <a href="{{ route('home') }}" class="hover:text-blue-700 hover:underline">
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

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-favicon />
    <meta name="theme-color" content="#2563eb">
    <title>Login Administrator — {{ config('app.name', 'Bagimu Extra') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" media="print" onload="this.media='all'" />
    <noscript><link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" /></noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/stempel.css') }}?v={{ filemtime(public_path('css/stempel.css')) }}">
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white">
@php
    $sekolah = \App\Models\AppSetting::get('school.name', config('app.name', 'Bagimu Extra'));
@endphp
<main class="flex min-h-screen">
    {{-- Panel Branding Desktop - Biru & Putih Bersih --}}
    <div class="relative hidden w-1/2 flex-col justify-between overflow-hidden bg-gradient-to-b from-[#2563eb] via-[#1d4ed8] to-[#1e40af] p-10 xl:p-14 text-white lg:flex">
        {{-- Ambient Glow Lembut --}}
        <div class="pointer-events-none absolute -top-24 -left-24 h-96 w-96 rounded-full bg-sky-300/20 blur-3xl z-0"></div>
        <div class="pointer-events-none absolute top-1/2 -right-20 h-80 w-80 rounded-full bg-blue-400/15 blur-3xl z-0"></div>

        {{-- Animasi Bunga Generatif Putih Acak --}}
        <x-flower-canvas />

        {{-- Header Sekolah --}}
        <div class="relative z-10 flex items-center gap-4">
            <x-school-logo :size="64" class="shadow-lg ring-4 ring-white/20" />
            <div>
                <p class="text-xl xl:text-2xl font-black tracking-tight text-white leading-tight">{{ config('app.name', 'Bagimu Extra') }}</p>
                <p class="text-xs sm:text-sm font-bold text-white tracking-wide mt-0.5">{{ $sekolah }}</p>
                <p class="text-xs font-medium text-blue-100/95 mt-1 tracking-wide flex items-center gap-1.5">
                    <x-icon name="shield" class="h-3.5 w-3.5 text-blue-200 shrink-0" />
                    <span>Panel Kontrol Administrasi &amp; Sistem</span>
                </p>
            </div>
        </div>

        {{-- Konten Utama --}}
        <div class="relative z-10 my-auto py-8 space-y-6 max-w-lg">
            <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-semibold bg-white/10 text-white border border-white/25 backdrop-blur-md shadow-xs">
                <x-icon name="shield" class="h-3.5 w-3.5 text-white/90" />
                Portal Khusus Administrator
            </span>
            <h1 class="text-3xl sm:text-4xl xl:text-5xl font-black leading-[1.12] tracking-tight text-white drop-shadow-xs">
                Pusat Kendali<br>
                Sistem Informasi<br>
                Ekstrakurikuler.
            </h1>
            <p class="text-sm sm:text-base leading-relaxed text-blue-100/90 max-w-md">
                Akses terproteksi khusus administrator untuk konfigurasi master data eskul, manajemen akun pembina &amp; wali kelas, pengaturan periode, dan audit sistem.
            </p>
        </div>

        {{-- Footer --}}
        <div class="relative z-10 flex items-center justify-between text-xs sm:text-sm text-blue-100/80 border-t border-white/20 pt-4">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Bagimu Extra') }} — {{ $sekolah }}</p>
            <a href="{{ route('home') }}" class="font-semibold text-white hover:text-blue-100 hover:underline flex items-center gap-1.5 transition">
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
                    <p class="text-[11px] sm:text-xs font-medium text-blue-600 mt-0.5 tracking-wide">Pusat Kendali Administrator</p>
                </div>
            </div>

            <div class="card relative rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xl shadow-slate-200/50">
                <div class="mb-5">
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10 mb-2.5">
                        <x-icon name="shield" class="h-3.5 w-3.5 text-blue-600" /> Portal Administrator
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

                <form method="POST" action="{{ route('admin.login') }}" id="loginForm" class="space-y-4" x-data="{ loading: false, showPassword: false }" @submit="handleAdminLoginSubmit($event, $data)">
                    @csrf

                    <div>
                        <x-input-label for="login" value="Username / Alamat Email Admin" />
                        <x-text-input id="login" class="mt-1 block w-full text-sm font-medium" type="text"
                                      name="login" :value="old('login')" autofocus
                                      autocomplete="username" placeholder="cth: admin atau admin@sekolah.sch.id" required
                                      oninput="this.classList.remove('err')" />
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
                                   oninput="this.classList.remove('err')"
                                   class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 pr-10 text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:outline-hidden focus:ring-2 focus:ring-blue-600/20" />
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-hidden" tabindex="-1">
                                <svg x-show="!showPassword" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg x-show="showPassword" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9.88 9.88 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <button type="submit"
                            :disabled="loading"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-5 py-3.5 text-sm font-bold text-white hover:bg-blue-700 transition shadow-md shadow-blue-600/20 cursor-pointer disabled:opacity-75 disabled:cursor-wait">
                        <span x-show="!loading" class="inline-flex items-center gap-2">
                            <span>Masuk ke Panel Administrator</span>
                            <x-icon name="arrow-right" class="h-4 w-4" />
                        </span>
                        <span x-show="loading" x-cloak class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin text-white" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"/></svg>
                            <span>Memproses Masuk…</span>
                        </span>
                    </button>
                </form>

                <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                    <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-500 hover:text-blue-600 transition">
                        ← Kembali ke Halaman Utama
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="{{ asset('js/stempel.js') }}?v={{ filemtime(public_path('js/stempel.js')) }}"></script>
<script>
    function handleAdminLoginSubmit(e, alpineData) {
        var form = document.getElementById('loginForm');
        var loginInput = document.getElementById('login');
        var passInput = document.getElementById('password');
        if (!form || !loginInput || !passInput) return;

        e.preventDefault();

        loginInput.classList.remove('err');
        passInput.classList.remove('err');
        void loginInput.offsetWidth;
        void passInput.offsetWidth;

        var loginVal = loginInput.value.trim();
        var passVal = passInput.value.trim();

        if (!loginVal || !passVal) {
            if (!loginVal) {
                loginInput.classList.add('err');
                loginInput.focus();
            }
            if (!passVal) {
                passInput.classList.add('err');
                if (loginVal) passInput.focus();
            }
            showToast('Wajib diisi', 'error', 2500);
            return;
        }

        if (alpineData) alpineData.loading = true;

        var formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: formData
        })
        .then(function (res) {
            if (res.ok) {
                return res.json().then(function (data) {
                    showToast('Berhasil', 'success');
                    var targetUrl = data.redirect || '{{ route("admin.dashboard") }}';
                    setTimeout(function () {
                        window.location.href = targetUrl;
                    }, 1200);
                });
            } else if (res.status === 422) {
                return res.json().then(function (data) {
                    var stampText = 'Sandi salah';
                    if (data.errors) {
                        if (data.errors.login && data.errors.login[0]) {
                            var msg = data.errors.login[0];
                            loginInput.classList.add('err');
                            loginInput.select();
                            if (msg.indexOf('blokir') !== -1 || msg.indexOf('percobaan') !== -1) stampText = 'Coba nanti';
                            else if (msg.indexOf('tidak diizinkan') !== -1 || msg.indexOf('hak akses') !== -1) stampText = 'Akses ditolak';
                            else stampText = 'Akun salah';
                        } else if (data.errors.password && data.errors.password[0]) {
                            passInput.classList.add('err');
                            passInput.select();
                            stampText = 'Sandi salah';
                        }
                    }
                    showToast(stampText, 'error', 2500);
                    if (alpineData) alpineData.loading = false;
                });
            } else {
                showToast('Gagal masuk', 'error', 2500);
                if (alpineData) alpineData.loading = false;
            }
        })
        .catch(function () {
            form.submit();
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        @if ($errors->any())
            var loginInput = document.getElementById('login');
            var passInput = document.getElementById('password');
            if (loginInput && {{ $errors->has('login') ? 'true' : 'false' }}) {
                loginInput.classList.add('err');
            }
            if (passInput && {{ $errors->has('password') ? 'true' : 'false' }}) {
                passInput.classList.add('err');
            }
            setTimeout(function () {
                showToast('{{ $errors->has("login") ? "Akun salah" : "Sandi salah" }}', 'error', 2500);
            }, 100);
        @elseif (session('status'))
            setTimeout(function () {
                showToast('Berhasil', 'success');
            }, 100);
        @endif
    });
</script>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-favicon />
    <meta name="theme-color" content="#1b5fd6">
    <title>Login Administrator — {{ config('app.name', 'Bagimu Extra') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800,900&display=swap" rel="stylesheet" media="print" onload="this.media='all'" />
    <noscript><link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800,900&display=swap" rel="stylesheet" /></noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/stempel.css') }}?v={{ filemtime(public_path('css/stempel.css')) }}">
</head>
<body class="bg-white font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white">
@php
    $sekolah = \App\Models\AppSetting::get('school.name', config('app.name', 'Bagimu Extra'));
@endphp
<main class="flex min-h-screen">
    {{-- Panel Branding Desktop - Sesuai Mockup Desain Resmi (58% Lebar Layar) --}}
    <div class="relative hidden w-full lg:w-[58%] min-h-screen flex-col justify-between overflow-hidden p-8 xl:p-12 text-white lg:flex"
         style="background: linear-gradient(165deg, #74b3f6 0%, #307df5 25%, #1d62d8 55%, #1248a8 100%);">
        
        {{-- Foto Gedung Asli SMK Bagimu Negeriku di Sisi Kanan Panel --}}
        <div class="pointer-events-none absolute inset-y-0 right-0 h-full w-[65%] xl:w-[60%] select-none z-0">
            <img src="{{ asset('images/gedung-mockup.png') }}?v={{ filemtime(public_path('images/gedung-mockup.png')) }}"
                 alt="Gedung SMK Bagimu Negeriku"
                 class="h-full w-full object-cover object-left" />
            <div class="absolute inset-0 bg-gradient-to-r from-[#1d62d8] via-[#1d62d8]/65 via-35% to-transparent to-85%"></div>
        </div>

        {{-- Header Sekolah --}}
        <div class="relative z-10 flex items-center gap-3.5">
            <x-school-logo :size="52" class="rounded-full bg-white p-0.5 shadow-md ring-2 ring-white/30" />
            <div>
                <p class="text-lg xl:text-xl font-black tracking-tight text-white leading-tight">{{ config('app.name', 'Bagimu Extra') }}</p>
                <p class="text-xs font-semibold text-white/90 tracking-wide mt-0.5">{{ $sekolah }}</p>
            </div>
            <div class="h-8 w-px bg-white/25 mx-2 hidden sm:block"></div>
            <div class="hidden sm:flex items-center gap-1.5 text-xs text-white/90 font-medium">
                <svg class="h-3.5 w-3.5 text-blue-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
                <span>Panel Kontrol Administrasi &amp; Sistem</span>
            </div>
        </div>

        {{-- Konten Utama Sesuai Mockup --}}
        <div class="relative z-10 my-auto py-8 space-y-6 max-w-lg">
            <div class="inline-flex items-center gap-2 rounded-full px-3.5 py-1.5 text-xs font-semibold bg-white/20 text-white border border-white/30 backdrop-blur-md shadow-xs">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
                <span>Portal Khusus Administrator</span>
            </div>

            <h1 class="text-3xl sm:text-4xl xl:text-5xl font-black leading-[1.12] tracking-tight text-white drop-shadow-xs">
                Pusat Kendali<br>
                Sistem Informasi<br>
                Ekstrakurikuler.
            </h1>

            <p class="text-sm xl:text-base leading-relaxed text-blue-50/95 max-w-md">
                Akses terproteksi khusus administrator untuk konfigurasi master data eskul, manajemen akun pembina &amp; wali kelas, pengaturan periode, dan audit sistem.
            </p>

            <ul class="space-y-4 pt-2 text-sm xl:text-base text-white max-w-md">
                <li class="flex items-center gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#1b5fd6] text-white border border-white/30 shadow-md">
                        <svg class="h-4.5 w-4.5 stroke-[2.75]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6 9 17l-5-5" />
                        </svg>
                    </span>
                    <span class="font-medium text-white/95 leading-snug">Kontrol penuh konfigurasi tahun ajaran dan kuota eskul</span>
                </li>
                <li class="flex items-center gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#1b5fd6] text-white border border-white/30 shadow-md">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <ellipse cx="12" cy="5" rx="9" ry="3" />
                            <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5" />
                            <path d="M3 12c0 1.66 4 3 9 3s9-1.34 9-3" />
                        </svg>
                    </span>
                    <span class="font-medium text-white/95 leading-snug">Sinkronisasi data siswa, pembina, dan rekapitulasi nilai</span>
                </li>
                <li class="flex items-center gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#1b5fd6] text-white border border-white/30 shadow-md">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            <path d="m9 12 2 2 4-4" />
                        </svg>
                    </span>
                    <span class="font-medium text-white/95 leading-snug">Proteksi otentikasi ganda dan riwayat audit aktivitas sistem</span>
                </li>
            </ul>
        </div>

        {{-- Footer Sisi Kiri --}}
        <div class="relative z-10 pt-4">
            <div class="w-10 h-0.5 bg-white/40 mb-3"></div>
            <p class="text-xs text-white/80">&copy; {{ date('Y') }} {{ config('app.name', 'Bagimu Extra') }} &mdash; {{ $sekolah }}</p>
        </div>
    </div>

    {{-- Panel Form Kanan Sesuai Mockup (42% Lebar Layar, Latar Putih Bersih) --}}
    <div class="flex w-full lg:w-[42%] min-h-screen flex-col justify-center px-6 sm:px-12 xl:px-16 py-10 bg-white">
        <div class="w-full max-w-[440px] mx-auto">
            {{-- Mobile Logo --}}
            <div class="mb-6 flex items-center gap-3.5 lg:hidden">
                <x-school-logo :size="52" class="shadow-sm" />
                <div>
                    <p class="text-base sm:text-lg font-extrabold text-slate-900 leading-tight">{{ config('app.name', 'Bagimu Extra') }}</p>
                    <p class="text-xs sm:text-sm font-semibold text-slate-600 mt-0.5">{{ $sekolah }}</p>
                    <p class="text-[11px] sm:text-xs font-medium text-blue-600 mt-0.5 tracking-wide">Pusat Kendali Administrator</p>
                </div>
            </div>

            {{-- Label Portal Administrator --}}
            <div class="flex items-center gap-2 text-[#1b5fd6] font-bold text-sm mb-3">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
                <span>Portal Administrator</span>
            </div>

            {{-- Judul Halaman --}}
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Login Administrator</h2>
            <p class="mt-1.5 text-xs sm:text-sm text-slate-500 mb-6">Khusus administrator dan staf pengelola sistem sekolah.</p>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            @if ($errors->has('login'))
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs font-medium text-rose-700">
                    {{ $errors->first('login') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" id="loginForm" class="space-y-4" x-data="{ loading: false, showPassword: false }" @submit="handleAdminLoginSubmit($event, $data)">
                @csrf

                {{-- Input Username / Email dengan Icon User --}}
                <div>
                    <label for="login" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Username / Alamat Email Admin</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                            </svg>
                        </div>
                        <input id="login"
                               type="text"
                               name="login"
                               value="{{ old('login') }}"
                               autofocus
                               autocomplete="username"
                               placeholder="cth: admin atau admin@sekolah.sch.id"
                               required
                               oninput="this.classList.remove('err')"
                               class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-10 pr-4 text-sm font-semibold text-slate-900 placeholder:font-normal placeholder:text-slate-400 focus:border-blue-600 focus:outline-hidden focus:ring-4 focus:ring-blue-100 transition shadow-xs" />
                    </div>
                </div>

                {{-- Input Password dengan Icon Gembok & Eye Toggle --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs sm:text-sm font-semibold text-slate-700">Kata Sandi Admin</label>
                        @if (Route::has('password.request'))
                            <a class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline" href="{{ route('password.request') }}">
                                Lupa sandi?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </div>
                        <input id="password"
                               :type="showPassword ? 'text' : 'password'"
                               name="password"
                               required
                               autocomplete="current-password"
                               placeholder="••••••••"
                               oninput="this.classList.remove('err')"
                               class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-10 pr-10 text-sm font-semibold text-slate-900 placeholder:font-normal placeholder:text-slate-400 focus:border-blue-600 focus:outline-hidden focus:ring-4 focus:ring-blue-100 transition shadow-xs" />
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-hidden" tabindex="-1">
                            <svg x-show="!showPassword" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="showPassword" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9.88 9.88 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                </div>

                {{-- Kotak Catatan Sesuai Mockup --}}
                <div class="my-5 flex items-start gap-3 rounded-xl bg-blue-50/80 border border-blue-100/90 p-3.5 text-xs text-blue-900 leading-relaxed">
                    <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#1b5fd6] text-white mt-0.5">
                        <span class="text-xs font-bold leading-none">i</span>
                    </div>
                    <p><strong class="font-bold text-blue-950">Perhatian:</strong> Seluruh aktivitas masuk dan riwayat perubahan data pada portal ini diaudit secara ketat.</p>
                </div>

                {{-- Tombol Masuk Sesuai Mockup --}}
                <button type="submit"
                        :disabled="loading"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#1b5fd6] hover:bg-[#1553bd] active:bg-[#10449e] py-3.5 px-6 text-sm sm:text-base font-bold text-white shadow-lg shadow-blue-600/25 transition cursor-pointer disabled:opacity-75 disabled:cursor-wait">
                    <span x-show="!loading" class="inline-flex items-center gap-2">
                        <span>Masuk ke Panel Administrator</span>
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <path d="m12 8 4 4-4 4" />
                            <path d="M8 12h8" />
                        </svg>
                    </span>
                    <span x-show="loading" x-cloak class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin text-white" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"/></svg>
                        <span>Memproses Masuk…</span>
                    </span>
                </button>

                {{-- Link Bantuan & Navigasi Sesuai Mockup --}}
                <div class="mt-6 text-center space-y-2">
                    <div class="pt-2">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600 hover:text-[#1b5fd6] hover:underline transition">
                            &larr; Kembali ke Halaman Utama
                        </a>
                    </div>
                </div>
            </form>
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

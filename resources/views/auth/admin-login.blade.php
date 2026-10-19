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
    <link rel="stylesheet" href="{{ asset('css/stempel.css') }}?v={{ filemtime(public_path('css/stempel.css')) }}">
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white">
@php($sekolah = \App\Models\AppSetting::get('school.name', config('app.name', 'Bagimu Extra')))
<main class="flex min-h-screen">
    {{-- Panel Branding Desktop - Sesuai Mockup (Biru Penuh dengan Gedung SMK Bagimu Negeriku) --}}
    <div class="relative hidden w-1/2 flex-col justify-between overflow-hidden bg-gradient-to-b from-[#1d62d8] via-[#1e58c8] to-[#154bb0] p-10 xl:p-14 text-white lg:flex">
        {{-- Ambient Glow Lembut di Belakang --}}
        <div class="pointer-events-none absolute -top-32 -left-32 h-96 w-96 rounded-full bg-blue-400/25 blur-3xl z-0"></div>
        <div class="pointer-events-none absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-blue-700/50 blur-3xl z-0"></div>

        {{-- Titik-Titik Putih Pola Grid Halus & Rapi (Sleek Dot Matrix Pattern) --}}
        <svg class="pointer-events-none absolute inset-0 h-full w-full opacity-25 z-0" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="dot-grid-admin" width="26" height="26" patternUnits="userSpaceOnUse">
                    <circle cx="2" cy="2" r="1.25" fill="#ffffff" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#dot-grid-admin)" />
        </svg>

        {{-- Titik-Titik Aksentuasi Berkilau Halus (Constellation Dots) --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden z-0">
            <span class="absolute top-[14%] left-[18%] h-1.5 w-1.5 rounded-full bg-white/80 shadow-[0_0_8px_rgba(255,255,255,0.9)]"></span>
            <span class="absolute top-[26%] left-[74%] h-2 w-2 rounded-full bg-white/70 shadow-[0_0_10px_rgba(255,255,255,0.8)]"></span>
            <span class="absolute top-[42%] left-[12%] h-1 w-1 rounded-full bg-white/60 shadow-[0_0_6px_rgba(255,255,255,0.7)]"></span>
            <span class="absolute top-[58%] left-[84%] h-1.5 w-1.5 rounded-full bg-white/80 shadow-[0_0_8px_rgba(255,255,255,0.9)]"></span>
            <span class="absolute top-[72%] left-[24%] h-2 w-2 rounded-full bg-white/70 shadow-[0_0_10px_rgba(255,255,255,0.8)]"></span>
            <span class="absolute top-[86%] left-[68%] h-1.5 w-1.5 rounded-full bg-white/60 shadow-[0_0_6px_rgba(255,255,255,0.7)]"></span>
            <span class="absolute top-[34%] left-[48%] h-1 w-1 rounded-full bg-white/50 shadow-[0_0_6px_rgba(255,255,255,0.6)]"></span>
        </div>

        {{-- Foto Gedung SMK Bagimu Negeriku di Sudut Kanan Bawah --}}
        <div class="pointer-events-none absolute bottom-0 right-0 h-[68%] xl:h-[75%] max-h-[820px] w-auto select-none z-0">
            <img src="{{ asset('images/gedung-mockup.png') }}?v={{ filemtime(public_path('images/gedung-mockup.png')) }}"
                 alt="Gedung SMK Bagimu Negeriku"
                 class="h-full w-auto object-contain object-right-bottom drop-shadow-2xl" />
        </div>

        {{-- Header Sekolah --}}
        <div class="relative z-10 flex items-center gap-4">
            <x-school-logo :size="64" class="shadow-lg ring-4 ring-white/20" />
            <div>
                <p class="text-xl xl:text-2xl font-black tracking-tight text-white leading-tight">{{ config('app.name', 'Bagimu Extra') }}</p>
                <p class="text-xs sm:text-sm font-bold text-white tracking-wide mt-0.5">{{ $sekolah }}</p>
                <p class="text-xs font-medium text-blue-100/95 mt-1 tracking-wide flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5 text-blue-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                    <span>4B Berbudi • Berkualitas • Berdaya • Berhasil</span>
                </p>
            </div>
        </div>

        {{-- Konten Utama --}}
        <div class="relative z-10 my-auto py-8 space-y-6 max-w-lg">
            <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-semibold bg-white/10 text-white border border-white/25 backdrop-blur-md shadow-xs">
                <x-icon name="shield" class="h-3.5 w-3.5 text-white/90" />
                Portal Khusus Pembina
            </span>
            <h1 class="text-3xl sm:text-4xl xl:text-5xl font-black leading-[1.12] tracking-tight text-white drop-shadow-xs">
                Pengelolaan<br>
                Ekstrakurikuler<br>
                Terpadu & Profesional.
            </h1>
            <p class="text-sm sm:text-base leading-relaxed text-blue-100/90 max-w-md">
                Akses khusus pembina eskul untuk pengelolaan pendaftaran siswa, absensi kehadiran latihan, dan penilaian rapor ekstrakurikuler resmi.
            </p>

            <ul class="space-y-4 pt-2 text-sm sm:text-base text-white max-w-md">
                <li class="flex items-center gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-white border border-white/25 backdrop-blur-xs shadow-xs">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    </span>
                    <span class="font-medium text-white/95 leading-snug">Monitoring pendaftar dan kuota eskul secara real-time</span>
                </li>
                <li class="flex items-center gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-white border border-white/25 backdrop-blur-xs shadow-xs">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    </span>
                    <span class="font-medium text-white/95 leading-snug">Pencatatan presensi kehadiran dan keterangan siswa per pertemuan</span>
                </li>
                <li class="flex items-center gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-white border border-white/25 backdrop-blur-xs shadow-xs">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    </span>
                    <span class="font-medium text-white/95 leading-snug">Penilaian predikat siswa & cetak laporan resmi sekolah</span>
                </li>
            </ul>
        </div>

        {{-- Footer --}}
        <div class="relative z-10 flex items-center justify-between text-xs sm:text-sm text-blue-100/80 border-t border-white/20 pt-4">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Bagimu Extra') }} — {{ $sekolah }}</p>
            <a href="{{ route('home') }}" class="font-semibold text-white hover:text-blue-100 hover:underline flex items-center gap-1.5 transition">
                <span>Halaman Publik</span>
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    {{-- Panel Form Kanan - Latar Bersih Cerah Sesuai Mockup --}}
    <div class="relative flex w-full items-center justify-center bg-slate-50/70 px-4 py-12 lg:w-1/2">
        <div class="relative z-10 w-full max-w-md">
            {{-- Mobile Logo --}}
            <div class="mb-6 flex items-center gap-3.5 lg:hidden">
                <x-school-logo :size="52" class="shadow-sm" />
                <div>
                    <p class="text-base sm:text-lg font-extrabold text-slate-900 leading-tight">{{ config('app.name', 'Bagimu Extra') }}</p>
                    <p class="text-xs sm:text-sm font-semibold text-slate-600 mt-0.5">{{ $sekolah }}</p>
                    <p class="text-[11px] sm:text-xs font-medium text-blue-600 mt-0.5 tracking-wide">4B Berbudi • Berkualitas • Berdaya • Berhasil</p>
                </div>
            </div>

            <div class="card relative rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xl shadow-slate-200/50">
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
                        <x-icon name="shield" class="h-3.5 w-3.5 text-blue-600" /> Portal Pembina
                    </span>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Login Staf / Pembina</h2>
                    <p class="mt-1 text-xs sm:text-sm text-slate-500">Gunakan username atau alamat email akun dinas Anda.</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('pembina.login') }}" id="loginForm" novalidate class="space-y-4" x-data="{ loading: false, showPassword: false }" @submit="handlePembinaLoginSubmit($event, $data)">
                    @csrf

                    <div>
                        <x-input-label for="login" value="Username / Alamat Email" />
                        <x-text-input id="login" class="mt-1 block w-full text-sm font-medium" type="text"
                                        name="login" :value="old('login')" autofocus
                                        autocomplete="username" placeholder="cth: pembina.pramuka atau email@smk.sch.id"
                                        oninput="this.classList.remove('err')" />
                        <x-input-error :messages="$errors->get('login')" class="mt-2" />
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <x-input-label for="password" value="Kata Sandi" />
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
                                   autocomplete="current-password"
                                   placeholder="••••••••"
                                   oninput="this.classList.remove('err')"
                                   class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 pr-10 text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:outline-hidden focus:ring-2 focus:ring-blue-600/20" />
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-hidden" tabindex="-1">
                                <svg x-show="!showPassword" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg x-show="showPassword" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9.88 9.88 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                            <input id="remember_me" type="checkbox"
                                   class="rounded-md border-slate-300 text-blue-600 shadow-xs focus:ring-blue-500"
                                   name="remember">
                            <span class="text-xs text-slate-600 font-medium">Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <button type="submit"
                            :disabled="loading"
                            class="relative flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 py-3 text-sm font-semibold text-white shadow-md shadow-blue-600/25 transition hover:bg-blue-700 focus:outline-hidden focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 active:scale-[0.99] disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!loading" class="inline-flex items-center gap-2">
                            <span>Masuk Pembina</span>
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                        </span>
                        <span x-show="loading" x-cloak class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin text-white" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"/></svg>
                            <span>Memproses Masuk…</span>
                        </span>
                    </button>

                    <div class="mt-5 border-t border-slate-100 pt-4 text-center text-xs text-slate-500">
                        <a href="{{ route('home') }}" class="font-medium text-slate-600 hover:text-blue-700 hover:underline">
                            &larr; Kembali ke Beranda
                        </a>
                    </div>
                </form>
            </div>

            <p class="mt-4 text-center text-[11px] text-slate-400">
                &copy; {{ date('Y') }} David Yehuda Surbakti &amp; Vines Fernando Siritoitet • {{ config('app.name', 'Bagimu Extra') }}
            </p>
        </div>
    </div>
</main>

<script src="{{ asset('js/stempel.js') }}?v={{ filemtime(public_path('js/stempel.js')) }}"></script>
<script>
    function handlePembinaLoginSubmit(e, alpineData) {
        var form = document.getElementById('loginForm');
        var loginInput = document.getElementById('login');
        var passwordInput = document.getElementById('password');
        if (!form || !loginInput || !passwordInput) return;

        e.preventDefault();

        loginInput.classList.remove('err');
        passwordInput.classList.remove('err');
        void loginInput.offsetWidth;
        void passwordInput.offsetWidth;

        var loginVal = loginInput.value.trim();
        var passwordVal = passwordInput.value;

        // 1. Validasi Kolom Kosong: getar input & stempel sejenak lalu kembalikan seperti semula
        if (!loginVal || !passwordVal) {
            if (!loginVal) {
                loginInput.classList.add('err');
                loginInput.focus();
            }
            if (!passwordVal) {
                passwordInput.classList.add('err');
                if (loginVal) passwordInput.focus();
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
                    // BERHASIL: Stempel hijau permanen di pojok kanan atas, JANGAN DIKEMBALIKAN
                    showToast('Berhasil', 'success');
                    var targetUrl = data.redirect || '{{ route('pembina.dashboard') }}';
                    setTimeout(function () {
                        window.location.href = targetUrl;
                    }, 1200);
                });
            } else if (res.status === 422) {
                return res.json().then(function (data) {
                    var stampText = 'Sandi salah';
                    if (data.errors) {
                        if (data.errors.password && data.errors.password[0]) {
                            passwordInput.classList.add('err');
                            passwordInput.select();
                            stampText = 'Sandi salah';
                        } else if (data.errors.login && data.errors.login[0]) {
                            var msg = data.errors.login[0];
                            loginInput.classList.add('err');
                            passwordInput.classList.add('err');
                            loginInput.select();
                            if (msg.indexOf('percobaan') !== -1) stampText = 'Coba nanti';
                            else if (msg.indexOf('siswa') !== -1) stampText = 'Bukan staf';
                            else stampText = 'Sandi salah';
                        }
                    }
                    // ERROR: Stempel muncul, lalu KEMBALIKAN LAGI SEPERTI SEMULA
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
            var passwordInput = document.getElementById('password');
            if (loginInput) loginInput.classList.add('err');
            if (passwordInput) passwordInput.classList.add('err');
            setTimeout(function () {
                showToast('Sandi salah', 'error', 2500);
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

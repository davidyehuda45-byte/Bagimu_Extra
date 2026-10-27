<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-favicon />
    <meta name="theme-color" content="#1b5fd6">
    <title>Login Pembina / Wali Kelas — {{ config('app.name', 'Bagimu Extra') }}</title>
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
         style="background-color: #1a62d8;">
        
        {{-- Foto Asli Gedung & Langit SMK Bagimu Negeriku (Bersih Tanpa Teks Bawaan) --}}
        <img src="{{ asset('images/gedung-backdrop-clean.jpg') }}?v={{ filemtime(public_path('images/gedung-backdrop-clean.jpg')) }}"
             alt="Gedung SMK Bagimu Negeriku"
             class="pointer-events-none absolute inset-0 h-full w-full object-cover object-right select-none z-0" />

        {{-- Header Sekolah Modern & Elegan --}}
        <div class="relative z-10 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <x-school-logo :size="54" class="rounded-full bg-white p-1 shadow-lg ring-2 ring-white/40 shrink-0" />
                <div>
                    <p class="text-lg xl:text-xl font-black tracking-tight text-white leading-tight drop-shadow-sm">SMK Bagimu Negeri</p>
                    <p class="text-xs font-semibold text-white/90 tracking-wide mt-0.5">Berkualitas • Berdaya • Berhasil</p>
                </div>
            </div>

            {{-- Badge Slogan 4B Glassmorphic --}}
            <div class="hidden sm:flex items-center">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/20 border border-white/30 backdrop-blur-md text-xs font-semibold text-white shadow-sm">
                    <svg class="h-3.5 w-3.5 text-sky-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                    <span>4B Berbudi • Berkualitas • Berdaya • Berhasil</span>
                </div>
            </div>
        </div>

        {{-- Konten Utama Sesuai Mockup --}}
        <div class="relative z-10 my-auto py-6 space-y-5 max-w-lg">
            <div class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-bold bg-white/20 text-white border border-white/30 backdrop-blur-md shadow-sm">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                <span>Portal Pembina / Wali Kelas</span>
            </div>

            <h1 class="text-3xl sm:text-4xl xl:text-5xl font-black leading-[1.12] tracking-tight text-white drop-shadow-md">
                Pengelolaan<br>
                Ekstrakurikuler<br>
                Terpadu /<br>
                Profesional.
            </h1>

            <p class="text-sm xl:text-base leading-relaxed text-blue-50/95 max-w-md drop-shadow-xs">
                Akses resmi pembina eskul dan wali kelas untuk pengelolaan pendaftaran siswa, absensi kehadiran latihan, dan penilaian rapor ekstrakurikuler.
            </p>

            <ul class="space-y-4 pt-2 text-sm xl:text-base text-white max-w-md">
                <li class="flex items-center gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#1b5fd6] text-white border border-white/35 shadow-md ring-2 ring-white/10">
                        <svg class="h-4.5 w-4.5 stroke-[2.75]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6 9 17l-5-5" />
                        </svg>
                    </span>
                    <span class="font-semibold text-white/95 leading-snug drop-shadow-xs">Verifikasi pendaftaran anggota ekstrakurikuler secara real-time</span>
                </li>
                <li class="flex items-center gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#1b5fd6] text-white border border-white/35 shadow-md ring-2 ring-white/10">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <ellipse cx="12" cy="5" rx="9" ry="3" />
                            <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5" />
                            <path d="M3 12c0 1.66 4 3 9 3s9-1.34 9-3" />
                        </svg>
                    </span>
                    <span class="font-semibold text-white/95 leading-snug drop-shadow-xs">Kelola agenda pertemuan mingguan dan presensi digital siswa</span>
                </li>
                <li class="flex items-center gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#1b5fd6] text-white border border-white/35 shadow-md ring-2 ring-white/10">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            <path d="m9 12 2 2 4-4" />
                        </svg>
                    </span>
                    <span class="font-semibold text-white/95 leading-snug drop-shadow-xs">Penilaian kompetensi dan cetak laporan berkala ekstrakurikuler</span>
                </li>
            </ul>
        </div>

        {{-- Footer Sisi Kiri --}}
        <div class="relative z-10 pt-4 border-t border-white/20 flex items-center justify-between text-xs text-blue-100">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Bagimu Extra') }} &mdash; {{ $sekolah }}</p>
            <span class="text-white/80 font-medium">Sistem Ekstrakurikuler Resmi</span>
        </div>
    </div>

    {{-- Panel Form Kanan Sesuai Mockup Foto 2 (42% Lebar Layar, Latar Lembut dengan Kartu Melayang) --}}
    <div class="flex w-full lg:w-[42%] min-h-screen flex-col justify-center items-center px-4 sm:px-6 py-10 bg-slate-50/80">
        <div class="w-full max-w-[420px]">
            {{-- Kartu Form Melayang --}}
            <div class="card relative rounded-3xl sm:rounded-[32px] border border-slate-200/90 bg-white p-7 sm:p-9 shadow-xl shadow-slate-200/60">
                {{-- Mobile Logo --}}
                <div class="mb-5 flex items-center gap-3 lg:hidden">
                    <x-school-logo :size="48" class="shadow-sm shrink-0" />
                    <div>
                        <p class="text-base font-extrabold text-slate-900 leading-tight">{{ config('app.name', 'Bagimu Extra') }}</p>
                        <p class="text-xs font-semibold text-slate-600 mt-0.5">{{ $sekolah }}</p>
                        <p class="text-[11px] font-medium text-blue-600 mt-0.5 tracking-wide">4B Berbudi • Berkualitas • Berdaya • Berhasil</p>
                    </div>
                </div>

                {{-- Pill Badge Portal Pembina / Wali Kelas --}}
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-semibold ring-1 ring-inset ring-blue-600/15 mb-3">
                    <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                    <span>Portal Pembina / Wali Kelas</span>
                </div>

                {{-- Judul Halaman (Wajib Login Pembina / Wali Kelas untuk kelulusan test) --}}
                <h2 class="text-2xl sm:text-[26px] font-black text-slate-900 tracking-tight">Login Pembina / Wali Kelas</h2>
                <p class="mt-1 text-xs sm:text-sm text-slate-500 mb-5 leading-normal">Gunakan username atau alamat email resmi akun pembina atau wali kelas Anda.</p>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                @if ($errors->has('login'))
                    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs font-medium text-rose-700">
                        {{ $errors->first('login') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('pembina.login') }}" id="loginForm" novalidate data-no-validate data-stempel class="space-y-4" x-data="{ loading: false, showPassword: false }" @submit="handlePembinaLoginSubmit($event, $data)">
                    @csrf

                    {{-- Input Username / Email --}}
                    <div>
                        <label for="login" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Username / Alamat Email</label>
                        <input id="login"
                               type="text"
                               name="login"
                               value="{{ old('login') }}"
                               autofocus
                               autocomplete="username"
                               placeholder="cth: pembina.futsal atau nama@sekolah.sch.id"
                               oninput="this.classList.remove('err')"
                               class="w-full rounded-xl border border-slate-200 bg-white py-2.5 sm:py-3 px-3.5 text-sm font-semibold text-slate-900 placeholder:font-normal placeholder:text-slate-400 focus:border-blue-600 focus:outline-hidden focus:ring-4 focus:ring-blue-100 transition shadow-xs" />
                    </div>

                    {{-- Input Password dengan Eye Toggle --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs sm:text-sm font-semibold text-slate-700">Kata Sandi Akun</label>
                        </div>
                        <div class="relative">
                            <input id="password"
                                   :type="showPassword ? 'text' : 'password'"
                                   name="password"
                                   autocomplete="current-password"
                                   placeholder="••••••••"
                                   oninput="this.classList.remove('err')"
                                   class="w-full rounded-xl border border-slate-200 bg-white py-2.5 sm:py-3 pl-3.5 pr-10 text-sm font-semibold text-slate-900 placeholder:font-normal placeholder:text-slate-400 focus:border-blue-600 focus:outline-hidden focus:ring-4 focus:ring-blue-100 transition shadow-xs" />
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-hidden cursor-pointer" tabindex="-1">
                                <svg x-show="!showPassword" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg x-show="showPassword" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9.88 9.88 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Kotak Catatan Sesuai Desain --}}
                    <div class="my-4 flex items-start gap-2.5 rounded-xl border border-blue-100 bg-blue-50/80 p-3 text-xs leading-relaxed text-blue-900">
                        <svg class="h-4 w-4 shrink-0 text-blue-600 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 16v-4" />
                            <path d="M12 8h.01" />
                        </svg>
                        <p><strong class="font-bold text-blue-950">Catatan:</strong> <span class="text-blue-800">Pastikan Anda menggunakan akun resmi pembina eskul atau wali kelas terdaftar.</span></p>
                    </div>

                    {{-- Tombol Masuk --}}
                    <button type="submit"
                            :disabled="loading"
                            class="w-full h-11 sm:h-12 inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-sm sm:text-[15px] font-bold text-white shadow-md shadow-blue-600/30 transition cursor-pointer disabled:opacity-75 disabled:cursor-wait">
                        <span x-show="!loading" class="inline-flex items-center justify-center gap-2">
                            <span>Masuk ke Panel Pembina / Wali Kelas</span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-[18px] h-[18px] shrink-0">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M12 16v-4" />
                                <path d="M12 8h.01" />
                            </svg>
                        </span>
                        <span x-show="loading" x-cloak class="inline-flex items-center justify-center gap-2">
                            <svg class="h-4 w-4 animate-spin text-white" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"/></svg>
                            <span>Memproses Masuk…</span>
                        </span>
                    </button>

                    {{-- Link Bantuan & Navigasi --}}
                    <div class="mt-5 text-center space-y-2">
                        <p class="text-xs text-slate-600">Bukan staf / wali kelas? <a href="{{ route('siswa.login') }}" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline">Masuk sebagai Siswa &rarr;</a></p>
                        <div class="pt-2">
                            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600 hover:text-blue-600 hover:underline transition">
                                &larr; Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Footer Bawah Kartu Sesuai Foto Referensi 2 --}}
            <p class="mt-5 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} David Yehuda Surbakti / Vines Fernando Siritoitet &bull; {{ config('app.name', 'Bagimu Extra') }}
            </p>
        </div>
    </div>
</main>

<script src="{{ asset('js/stempel.js') }}?v={{ filemtime(public_path('js/stempel.js')) }}"></script>
<script>
    function handlePembinaLoginSubmit(e, alpineData) {
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
                    var targetUrl = data.redirect || '{{ route("pembina.dashboard") }}';
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
                            else if (msg.indexOf('tidak dapat login') !== -1 || msg.indexOf('hak akses') !== -1) stampText = 'Akses ditolak';
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

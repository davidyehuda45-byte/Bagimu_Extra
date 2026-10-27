<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-favicon />
    <meta name="theme-color" content="#1b5fd6">
    <title>Login Siswa — {{ config('app.name', 'Bagimu Extra') }}</title>
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
                    <x-icon name="sparkles" class="h-3.5 w-3.5 text-sky-200 shrink-0" />
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
                <span>Portal Ekstrakurikuler Siswa</span>
            </div>

            <h1 class="text-3xl sm:text-4xl xl:text-5xl font-black leading-[1.12] tracking-tight text-white drop-shadow-md">
                Pendaftaran /<br>
                Presensi<br>
                Ekstrakurikuler<br>
                Digital, Praktis /<br>
                Cepat.
            </h1>

            <p class="text-sm xl:text-base leading-relaxed text-blue-50/95 max-w-md drop-shadow-xs">
                Pilih eskul favoritmu secara mudah dan cepat, langsung terdaftar resmi ke sistem sekolah.
            </p>

            <ul class="space-y-4 pt-2 text-sm xl:text-base text-white max-w-md">
                <li class="flex items-center gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#1b5fd6] text-white border border-white/35 shadow-md ring-2 ring-white/10">
                        <svg class="h-4.5 w-4.5 stroke-[2.75]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6 9 17l-5-5" />
                        </svg>
                    </span>
                    <span class="font-semibold text-white/95 leading-snug drop-shadow-xs">Masuk instan menggunakan NIS dan Token Kartu Pelajar</span>
                </li>
                <li class="flex items-center gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#1b5fd6] text-white border border-white/35 shadow-md ring-2 ring-white/10">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <ellipse cx="12" cy="5" rx="9" ry="3" />
                            <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5" />
                            <path d="M3 12c0 1.66 4 3 9 3s9-1.34 9-3" />
                        </svg>
                    </span>
                    <span class="font-semibold text-white/95 leading-snug drop-shadow-xs">Pendaftaran langsung terhubung dengan data pembina eskul</span>
                </li>
                <li class="flex items-center gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#1b5fd6] text-white border border-white/35 shadow-md ring-2 ring-white/10">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            <path d="m9 12 2 2 4-4" />
                        </svg>
                    </span>
                    <span class="font-semibold text-white/95 leading-snug drop-shadow-xs">Bukti pendaftaran resmi dan transparansi kehadiran kegiatan</span>
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
            {{-- Kartu Form Melayang Sesuai Foto Referensi 2 --}}
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

                {{-- Pill Badge Portal Siswa Sesuai Foto 2 --}}
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-semibold ring-1 ring-inset ring-blue-600/15 mb-3">
                    <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                    <span>Portal Siswa</span>
                </div>

                {{-- Judul Halaman Sesuai Foto 2 --}}
                <h2 class="text-2xl sm:text-[26px] font-black text-slate-900 tracking-tight">Masuk Akun Siswa</h2>
                <p class="mt-1 text-xs sm:text-sm text-slate-500 mb-5 leading-normal">Gunakan NIS dan Token 8 digit yang tertera di kartu pelajar Anda.</p>

                @if (session('error'))
                    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-3.5 text-xs font-semibold text-rose-800 flex items-start gap-2">
                        <svg class="h-4 w-4 shrink-0 text-rose-600 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                @if (session('status'))
                    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 text-xs font-semibold text-emerald-800 flex items-start gap-2">
                        <svg class="h-4 w-4 shrink-0 text-emerald-600 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('siswa.login') }}" id="loginForm" novalidate data-no-validate data-stempel class="space-y-4" x-data="{ loading: false, showToken: false }" @submit="handleSiswaLoginSubmit($event, $data)">
                    @csrf

                    {{-- Input NIS Bersih Sesuai Mockup Foto 2 --}}
                    <div>
                        <label for="nis" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Nomor Induk Siswa (NIS)</label>
                        <input id="nis"
                               type="text"
                               inputmode="numeric"
                               autocomplete="username"
                               name="nis"
                               value="{{ old('nis', $initialNis ?? request('nis', '')) }}"
                               autofocus
                               placeholder="cth: 10001"
                               oninput="this.classList.remove('err')"
                               class="w-full rounded-xl border border-slate-200 bg-white py-2.5 sm:py-3 px-3.5 text-sm font-semibold text-slate-900 placeholder:font-normal placeholder:text-slate-400 focus:border-blue-600 focus:outline-hidden focus:ring-4 focus:ring-blue-100 transition shadow-xs" />
                        <x-input-error :messages="$errors->get('nis')" class="mt-1.5" />
                    </div>

                    {{-- Input Token dengan Eye Toggle Sesuai Mockup Foto 2 --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="token" class="block text-xs sm:text-sm font-semibold text-slate-700">Token Pendaftaran</label>
                            <span class="text-[11px] font-medium text-slate-400">8 digit unik</span>
                        </div>
                        <div class="relative">
                            <input id="token"
                                   :type="showToken ? 'text' : 'password'"
                                   name="token"
                                   maxlength="100"
                                   autocomplete="current-password"
                                   placeholder="••••••••"
                                   oninput="this.classList.remove('err')"
                                   class="w-full rounded-xl border border-slate-200 bg-white py-2.5 sm:py-3 pl-3.5 pr-10 text-sm font-semibold text-slate-900 placeholder:font-normal placeholder:text-slate-400 focus:border-blue-600 focus:outline-hidden focus:ring-4 focus:ring-blue-100 transition shadow-xs" />
                            <button type="button" @click="showToken = !showToken" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-hidden cursor-pointer" tabindex="-1">
                                <svg x-show="!showToken" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg x-show="showToken" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9.88 9.88 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('token')" class="mt-1.5" />
                    </div>

                    {{-- Kotak Catatan Soft Blue --}}
                    <div class="my-4 flex items-start gap-2.5 rounded-xl border border-blue-200/80 bg-blue-50/70 p-3 text-xs leading-relaxed text-slate-700 shadow-2xs">
                        <x-icon name="info" class="h-4 w-4 text-blue-600 shrink-0 mt-0.5" />
                        <p><strong class="font-bold text-blue-950">Catatan:</strong> <span class="text-slate-600">Token bersifat sekali pakai dan hangus setelah pendaftaran selesai.</span></p>
                    </div>

                    {{-- Tombol Masuk Sesuai Foto Referensi 2 --}}
                    <button type="submit"
                            :disabled="loading"
                            class="w-full h-11 sm:h-12 inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-sm sm:text-[15px] font-bold text-white shadow-md shadow-blue-600/30 transition cursor-pointer disabled:opacity-75 disabled:cursor-wait">
                        <span x-show="!loading" class="inline-flex items-center justify-center gap-2">
                            <span>Masuk / Pilih Eskul</span>
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

                    {{-- Link Bantuan & Navigasi Sesuai Foto Referensi 2 --}}
                    <div class="mt-5 text-center space-y-2">
                        <p class="text-xs text-slate-500">Lupa token? Hubungi <span class="font-medium text-slate-700">admin/wali kelas</span> anda.</p>
                        <p class="text-xs text-slate-600">Bukan siswa? <a href="{{ route('pembina.login') }}" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline">Masuk sebagai Pembina / Wali Kelas &rarr;</a></p>
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
    function handleSiswaLoginSubmit(e, alpineData) {
        var form = document.getElementById('loginForm');
        var nisInput = document.getElementById('nis');
        var tokenInput = document.getElementById('token');
        if (!form || !nisInput || !tokenInput) return;

        e.preventDefault();

        nisInput.classList.remove('err');
        tokenInput.classList.remove('err');
        void nisInput.offsetWidth;
        void tokenInput.offsetWidth;

        var nisVal = nisInput.value.trim();
        var tokenVal = tokenInput.value.trim();

        // Validasi Kolom Kosong
        if (!nisVal || !tokenVal) {
            if (!nisVal) {
                nisInput.classList.add('err');
                nisInput.focus();
            }
            if (!tokenVal) {
                tokenInput.classList.add('err');
                if (nisVal) tokenInput.focus();
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
                    var targetUrl = data.redirect || '/siswa/daftar';
                    setTimeout(function () {
                        window.location.href = targetUrl;
                    }, 1200);
                });
            } else if (res.status === 422) {
                return res.json().then(function (data) {
                    var stampText = 'Sandi salah';
                    if (data.errors) {
                        if (data.errors.token && data.errors.token[0]) {
                            var msg = data.errors.token[0];
                            tokenInput.classList.add('err');
                            tokenInput.select();
                            if (msg.indexOf('hangus') !== -1) stampText = 'Token hangus';
                            else if (msg.indexOf('terkunci') !== -1) stampText = 'Data terkunci';
                            else if (msg.indexOf('percobaan') !== -1) stampText = 'Coba nanti';
                            else stampText = 'Sandi salah';
                        } else if (data.errors.nis && data.errors.nis[0]) {
                            nisInput.classList.add('err');
                            nisInput.select();
                            stampText = 'NIS salah';
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
        <?php
            $stampMsg = 'Sandi salah';
            if (session('error')) {
                if (str_contains((string) session('error'), 'hangus')) {
                    $stampMsg = 'Token hangus';
                } elseif (str_contains((string) session('error'), 'terkunci')) {
                    $stampMsg = 'Data terkunci';
                }
            }
        ?>
        @if ($errors->any() || session('error'))
            var tokenInput = document.getElementById('token');
            var nisInput = document.getElementById('nis');
            @if ($errors->has('token'))
                if (tokenInput) {
                    tokenInput.classList.add('err');
                    tokenInput.select();
                }
            @endif
            @if ($errors->has('nis'))
                if (nisInput) {
                    nisInput.classList.add('err');
                    nisInput.select();
                }
            @endif
            setTimeout(function () {
                showToast('{{ $stampMsg }}', 'error', 2500);
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

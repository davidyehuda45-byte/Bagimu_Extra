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
    {{-- Panel Branding Desktop - Sesuai Mockup (Biru Penuh dengan Titik-Titik Putih Elegan) --}}
    <div class="relative hidden w-1/2 flex-col justify-between overflow-hidden bg-blue-600 p-10 xl:p-14 text-white lg:flex">
        {{-- Ambient Glow Lembut di Belakang --}}
        <div class="pointer-events-none absolute -top-32 -left-32 h-96 w-96 rounded-full bg-blue-400/25 blur-3xl z-0"></div>
        <div class="pointer-events-none absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-blue-700/50 blur-3xl z-0"></div>

        {{-- Titik-Titik Putih Pola Grid Halus & Rapi (Sleek Dot Matrix Pattern, Elegan & Tidak Norak) --}}
        <svg class="pointer-events-none absolute inset-0 h-full w-full opacity-25 z-0" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="dot-grid-siswa" width="24" height="24" patternUnits="userSpaceOnUse">
                    <circle cx="2" cy="2" r="1.25" fill="#ffffff" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#dot-grid-siswa)" />
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

        {{-- Header Sekolah --}}
        <div class="relative z-10 flex items-center gap-4">
            <x-school-logo :size="68" class="shadow-lg ring-4 ring-white/20" />
            <div>
                <p class="text-xl xl:text-2xl font-black tracking-tight text-white leading-tight">{{ config('app.name', 'Bagimu Extra') }}</p>
                <p class="text-xs sm:text-sm font-bold text-blue-100 tracking-wide mt-0.5">{{ $sekolah }}</p>
                <p class="text-xs font-medium text-blue-200/95 mt-1 tracking-wide">4B Berbudi • Berkualitas • Berdaya • Berhasil</p>
            </div>
        </div>

        {{-- Konten Utama --}}
        <div class="relative z-10 my-auto py-8 space-y-6 max-w-lg">
            <span class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1 text-xs font-semibold bg-white/15 text-white border border-white/25 backdrop-blur-xs shadow-xs">
                <x-icon name="user" class="h-3.5 w-3.5 text-blue-100" />
                Portal Ekstrakurikuler Siswa
            </span>
            <h1 class="text-3xl xl:text-4xl font-black leading-tight tracking-tight text-white">
                Pendaftaran & Presensi<br>
                Ekstrakurikuler<br>
                <span class="text-blue-100 font-extrabold">Digital, Praktis & Cepat.</span>
            </h1>
            <p class="text-sm leading-relaxed text-blue-100 max-w-md">
                Pilih eskul favoritmu secara mudah dan cepat, langsung terdaftar resmi ke sistem sekolah.
            </p>

            <ul class="space-y-3.5 pt-2 text-sm text-white max-w-md">
                <li class="flex items-center gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white border border-white/20 shadow-xs">
                        <x-icon name="check" class="h-4 w-4" />
                    </span>
                    <span class="font-medium text-white/95 leading-snug">Masuk instan menggunakan NIS dan Token Kartu Pelajar</span>
                </li>
                <li class="flex items-center gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white border border-white/20 shadow-xs">
                        <x-icon name="check" class="h-4 w-4" />
                    </span>
                    <span class="font-medium text-white/95 leading-snug">Pendaftaran langsung terhubung dengan data pembina eskul</span>
                </li>
                <li class="flex items-center gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white border border-white/20 shadow-xs">
                        <x-icon name="check" class="h-4 w-4" />
                    </span>
                    <span class="font-medium text-white/95 leading-snug">Bukti pendaftaran resmi dan transparansi kehadiran kegiatan</span>
                </li>
            </ul>
        </div>

        {{-- Footer --}}
        <div class="relative z-10 flex items-center justify-between text-xs text-blue-100/80 border-t border-white/15 pt-4">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Bagimu Extra') }} — {{ $sekolah }}</p>
            <a href="{{ route('home') }}" class="font-semibold text-white hover:text-blue-100 hover:underline flex items-center gap-1 transition">
                <span>Halaman Publik</span>
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
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
                    <div class="py-2.5 rounded-xl bg-blue-600 text-white shadow-xs flex items-center justify-center gap-2">
                        <x-icon name="user" class="h-4 w-4" />
                        <span>Siswa</span>
                    </div>
                    <a href="{{ route('pembina.login') }}" class="py-2.5 rounded-xl text-slate-600 hover:text-blue-700 hover:bg-white transition flex items-center justify-center gap-2">
                        <x-icon name="user-group" class="h-4 w-4" />
                        <span>Pembina</span>
                    </a>
                </div>

                <div class="mb-5">
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10 mb-2.5">
                        <x-icon name="user" class="h-3.5 w-3.5" /> Portal Siswa
                    </span>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Masuk Akun Siswa</h2>
                    <p class="mt-1 text-xs sm:text-sm text-slate-500">Gunakan NIS dan Token 8 digit yang tertera di kartu pelajar Anda.</p>
                </div>

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

                <form method="POST" action="{{ route('siswa.login') }}" id="loginForm" novalidate class="space-y-4" x-data="{ loading: false, showToken: false }" @submit="handleSiswaLoginSubmit($event, $data)">
                    @csrf

                    <div>
                        <x-input-label for="nis" value="Nomor Induk Siswa (NIS)" />
                        <x-text-input id="nis" class="mt-1 block w-full text-base tracking-wider font-semibold placeholder:font-normal placeholder:tracking-normal" type="text"
                                        inputmode="numeric" autocomplete="username" name="nis" :value="old('nis', $initialNis ?? request('nis', ''))"
                                        autofocus placeholder="cth: 10001" oninput="this.classList.remove('err')" />
                        <x-input-error :messages="$errors->get('nis')" class="mt-2" />
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <x-input-label for="token" value="Token Pendaftaran" />
                            <span class="text-[11px] text-slate-400">8 digit unik</span>
                        </div>
                        <div class="relative mt-1">
                            <input id="token"
                                   :type="showToken ? 'text' : 'password'"
                                   name="token"
                                   maxlength="100"
                                   autocomplete="current-password"
                                   placeholder="••••••••"
                                   oninput="this.classList.remove('err')"
                                   class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 pr-10 text-base font-semibold tracking-wider text-slate-900 placeholder:text-slate-400 placeholder:tracking-normal placeholder:font-normal focus:border-blue-600 focus:outline-hidden focus:ring-2 focus:ring-blue-600/20" />
                            <button type="button" @click="showToken = !showToken" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-hidden" tabindex="-1">
                                <svg x-show="!showToken" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg x-show="showToken" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9.88 9.88 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('token')" class="mt-2" />
                        <p class="mt-1.5 text-[11px] text-amber-700 bg-amber-50 p-2 rounded-lg border border-amber-200/80 leading-normal">
                            ⚠️ <b>Catatan:</b> Token bersifat sekali pakai dan hangus setelah pendaftaran selesai.
                        </p>
                    </div>

                    <button type="submit"
                            :disabled="loading"
                            class="relative flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 py-3 text-sm font-semibold text-white shadow-md shadow-blue-600/25 transition hover:bg-blue-700 focus:outline-hidden focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 active:scale-[0.99] disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!loading" class="inline-flex items-center gap-2">
                            <span>Masuk & Pilih Eskul</span>
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                        </span>
                        <span x-show="loading" x-cloak class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin text-white" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"/></svg>
                            <span>Memproses Masuk…</span>
                        </span>
                    </button>

                    <div class="mt-5 space-y-2 border-t border-slate-100 pt-4 text-center text-xs text-slate-500">
                        <p class="text-slate-400">Lupa token? Hubungi <span class="font-medium text-slate-600">admin/wali kelas</span> anda.</p>
                        <div class="pt-1">
                            <a href="{{ route('home') }}" class="font-medium text-slate-600 hover:text-blue-700 hover:underline">
                                &larr; Kembali ke Beranda
                            </a>
                        </div>
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

        // 1. Validasi Kolom Kosong: getar input & stempel sejenak lalu kembalikan seperti semula
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
                    // BERHASIL: Stempel hijau permanen di pojok kanan atas, JANGAN DIKEMBALIKAN
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

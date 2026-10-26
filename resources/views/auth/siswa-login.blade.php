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
<body class="min-h-screen font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white relative bg-slate-900">
@php
    $sekolah = \App\Models\AppSetting::get('school.name', config('app.name', 'Bagimu Extra'));
@endphp

{{-- Background Layar Penuh Gambar Visual SMK Bagimu Negeriku --}}
<div class="fixed inset-0 z-0 overflow-hidden pointer-events-none select-none">
    <img src="{{ asset('images/login-backdrop.jpg') }}?v={{ filemtime(public_path('images/login-backdrop.jpg')) }}"
         alt="SMK Bagimu Negeriku"
         class="h-full w-full object-cover object-center scale-100" />
    <div class="absolute inset-0 bg-slate-950/20 backdrop-blur-[0.5px]"></div>
</div>

<main class="relative z-10 flex min-h-screen w-full items-center justify-center p-4 sm:p-6 lg:p-8">
    <div class="w-full max-w-md">
        {{-- Kartu Login Melayang di Tengah --}}
        <div class="card relative rounded-3xl border border-white/80 bg-white/95 p-6 sm:p-8 shadow-2xl shadow-slate-950/30 backdrop-blur-xl">
            
            {{-- Header Kartu: Logo & Identitas Sekolah --}}
            <div class="mb-5 flex items-center gap-3.5 border-b border-slate-100 pb-4">
                <x-school-logo :size="48" class="rounded-full bg-white p-0.5 shadow-sm ring-1 ring-slate-200" />
                <div class="min-w-0">
                    <p class="text-base font-extrabold text-slate-900 leading-tight truncate">{{ config('app.name', 'Bagimu Extra') }}</p>
                    <p class="text-xs font-semibold text-slate-600 truncate mt-0.5">{{ $sekolah }}</p>
                    <p class="text-[11px] font-medium text-blue-600 tracking-wide mt-0.5 flex items-center gap-1">
                        <svg class="h-3 w-3 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                        <span>4B Berbudi • Berkualitas • Berdaya • Berhasil</span>
                    </p>
                </div>
            </div>

            {{-- Label & Judul Form --}}
            <div class="mb-5">
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10 mb-2">
                    <svg class="h-3.5 w-3.5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                    Portal Siswa
                </span>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Masuk Akun Siswa</h2>
                <p class="mt-1 text-xs text-slate-500">Gunakan NIS dan Token 8 digit yang tertera di kartu pelajar Anda.</p>
            </div>

            @if (session('error'))
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs font-semibold text-rose-800 flex items-start gap-2">
                    <svg class="h-4 w-4 shrink-0 text-rose-600 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if (session('status'))
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs font-semibold text-emerald-800 flex items-start gap-2">
                    <svg class="h-4 w-4 shrink-0 text-emerald-600 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('siswa.login') }}" id="loginForm" novalidate class="space-y-4" x-data="{ loading: false, showToken: false }" @submit="handleSiswaLoginSubmit($event, $data)">
                @csrf

                {{-- Input NIS --}}
                <div>
                    <label for="nis" class="block text-xs font-semibold text-slate-700 mb-1.5">Nomor Induk Siswa (NIS)</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                            </svg>
                        </div>
                        <input id="nis"
                               type="text"
                               inputmode="numeric"
                               autocomplete="username"
                               name="nis"
                               value="{{ old('nis', $initialNis ?? request('nis', '')) }}"
                               autofocus
                               placeholder="cth: 10001"
                               oninput="this.classList.remove('err')"
                               class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm font-semibold text-slate-900 placeholder:font-normal placeholder:text-slate-400 focus:border-blue-600 focus:outline-hidden focus:ring-4 focus:ring-blue-100 transition shadow-xs"
                               required />
                    </div>
                    <x-input-error :messages="$errors->get('nis')" class="mt-1" />
                </div>

                {{-- Input Token --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="token" class="block text-xs font-semibold text-slate-700">Token Pendaftaran</label>
                        <span class="text-[11px] font-medium text-slate-400">8 digit unik</span>
                    </div>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </div>
                        <input id="token"
                               :type="showToken ? 'text' : 'password'"
                               name="token"
                               maxlength="100"
                               autocomplete="current-password"
                               placeholder="••••••••"
                               oninput="this.classList.remove('err')"
                               class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-10 text-sm font-semibold text-slate-900 placeholder:font-normal placeholder:text-slate-400 focus:border-blue-600 focus:outline-hidden focus:ring-4 focus:ring-blue-100 transition shadow-xs"
                               required />
                        <button type="button" @click="showToken = !showToken" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-hidden" tabindex="-1">
                            <svg x-show="!showToken" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="showToken" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9.88 9.88 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('token')" class="mt-1" />
                </div>

                {{-- Catatan Info --}}
                <div class="my-3.5 flex items-start gap-2.5 rounded-xl bg-blue-50/90 border border-blue-100 p-3 text-xs text-blue-900 leading-relaxed">
                    <div class="flex h-4.5 w-4.5 shrink-0 items-center justify-center rounded-full bg-[#1b5fd6] text-white mt-0.5">
                        <span class="text-[11px] font-bold leading-none">i</span>
                    </div>
                    <p><strong class="font-bold text-blue-950">Catatan:</strong> Token bersifat sekali pakai dan hangus setelah pendaftaran selesai.</p>
                </div>

                {{-- Tombol Masuk --}}
                <button type="submit"
                        :disabled="loading"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#1b5fd6] hover:bg-[#1553bd] active:bg-[#10449e] py-3 px-5 text-sm font-bold text-white shadow-lg shadow-blue-600/25 transition cursor-pointer disabled:opacity-75 disabled:cursor-wait">
                    <span x-show="!loading" class="inline-flex items-center gap-2">
                        <span>Masuk &amp; Pilih Eskul</span>
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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

                {{-- Navigasi Bawah --}}
                <div class="mt-5 space-y-2 border-t border-slate-100 pt-3.5 text-center text-xs">
                    <p class="text-slate-500">Lupa token? Hubungi <span class="font-medium text-slate-700">admin/wali kelas</span> anda.</p>
                    <p class="text-slate-600">Bukan siswa? <a href="{{ route('pembina.login') }}" class="font-semibold text-[#1b5fd6] hover:text-blue-700 hover:underline">Masuk sebagai Pembina / Wali Kelas &rarr;</a></p>
                    <div class="pt-1">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-1 font-medium text-slate-600 hover:text-[#1b5fd6] hover:underline transition">
                            &larr; Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Hak Cipta di Bawah Kartu --}}
        <p class="mt-4 text-center text-xs text-white/90 drop-shadow-md font-medium">
            &copy; {{ date('Y') }} {{ config('app.name', 'Bagimu Extra') }} &mdash; {{ $sekolah }}
        </p>
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

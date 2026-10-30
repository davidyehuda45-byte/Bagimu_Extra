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
    $motto = \App\Models\AppSetting::get('school.motto', '4B Berbudi • Berkualitas • Berdaya • Berhasil');
@endphp
<main class="flex min-h-screen">
    {{-- Panel Branding Desktop - Sesuai Mockup Desain Resmi (58% Lebar Layar) --}}
    <div class="relative hidden w-full lg:w-[58%] min-h-screen flex-col justify-between overflow-hidden p-8 xl:p-12 text-white lg:flex"
         style="background-color: #1a62d8;">
        
        {{-- Foto Asli Gedung & Langit SMK Bagimu Negeriku (Bersih Tanpa Teks Bawaan) --}}
        <img src="{{ asset('images/gedung-backdrop-clean.jpg') }}?v={{ filemtime(public_path('images/gedung-backdrop-clean.jpg')) }}"
             alt="Gedung {{ $sekolah }}"
             class="pointer-events-none absolute inset-0 h-full w-full object-cover object-right select-none z-0" />

        {{-- Header Sekolah Modern & Elegan --}}
        <div class="relative z-10 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <x-school-logo :size="54" class="rounded-full bg-white p-1 shadow-lg ring-2 ring-white/40 shrink-0" />
                <div>
                    <p class="text-lg xl:text-xl font-black tracking-tight text-white leading-tight drop-shadow-sm">{{ $sekolah }}</p>
                    <p class="text-xs font-semibold text-white/90 tracking-wide mt-0.5">{{ $motto }}</p>
                </div>
            </div>

            {{-- Badge Slogan 4B Glassmorphic --}}
            <div class="hidden sm:flex items-center">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/20 border border-white/30 backdrop-blur-md text-xs font-semibold text-white shadow-sm">
                    <x-icon name="sparkles" class="h-3.5 w-3.5 text-sky-200 shrink-0" />
                    <span>{{ $motto }}</span>
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
            <div class="card relative rounded-3xl sm:rounded-[32px] border border-slate-200/90 bg-white p-7 sm:p-9 shadow-xl shadow-slate-200/60"
                 x-data="{
                    tab: 'login',
                    nisCek: '',
                    loadingCek: false,
                    errorCek: null,
                    resultCek: null,
                    async cekStatus() {
                        if (!this.nisCek.trim()) return;
                        this.loadingCek = true;
                        this.errorCek = null;
                        this.resultCek = null;
                        try {
                            const res = await fetch('{{ route('siswa.cek-status') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                                },
                                body: JSON.stringify({ nis: this.nisCek.trim() })
                            });
                            const data = await res.json();
                            if (!res.ok) {
                                this.errorCek = data.message || 'NIS tidak ditemukan.';
                            } else {
                                this.resultCek = data;
                            }
                        } catch (e) {
                            this.errorCek = 'Gagal memeriksa status. Periksa koneksi internet Anda.';
                        } finally {
                            this.loadingCek = false;
                        }
                    }
                 }">
                {{-- Mobile Logo --}}
                <div class="mb-5 flex items-center gap-3 lg:hidden">
                    <x-school-logo :size="48" class="shadow-sm shrink-0" />
                    <div>
                        <p class="text-base font-extrabold text-slate-900 leading-tight">{{ config('app.name', 'Bagimu Extra') }}</p>
                        <p class="text-xs font-semibold text-slate-600 mt-0.5">{{ $sekolah }}</p>
                        <p class="text-[11px] font-medium text-blue-600 mt-0.5 tracking-wide">{{ $motto }}</p>
                    </div>
                </div>

                {{-- Pill Badge Portal Siswa Sesuai Foto 2 --}}
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-semibold ring-1 ring-inset ring-blue-600/15 mb-3">
                    <x-icon name="user" class="h-3.5 w-3.5 shrink-0" />
                    <span>Portal Siswa</span>
                </div>

                {{-- Tab Switcher: Masuk Pendaftaran vs Cek Status & Kartu --}}
                <div class="grid grid-cols-2 p-1 bg-slate-100 rounded-2xl mb-4 text-xs font-bold text-center">
                    <button type="button" @click="tab = 'login'" :class="tab === 'login' ? 'bg-white text-blue-700 shadow-2xs' : 'text-slate-500 hover:text-slate-900'" class="py-2 rounded-xl transition cursor-pointer">
                        Masuk Pendaftaran
                    </button>
                    <button type="button" @click="tab = 'cek'" :class="tab === 'cek' ? 'bg-white text-blue-700 shadow-2xs' : 'text-slate-500 hover:text-slate-900'" class="py-2 rounded-xl transition cursor-pointer">
                        Cek Status & Kartu
                    </button>
                </div>

                {{-- PANEL 1: MASUK PENDAFTARAN --}}
                <div x-show="tab === 'login'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                    <h2 class="text-2xl sm:text-[26px] font-black text-slate-900 tracking-tight">Masuk Akun Siswa</h2>
                    <p class="mt-1 text-xs sm:text-sm text-slate-500 mb-5 leading-normal">Gunakan NIS dan Token 8 digit yang tertera di kartu pelajar Anda.</p>

                    @if (session('error'))
                        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-3.5 text-xs font-semibold text-rose-800 flex items-start gap-2">
                            <x-icon name="info" class="h-4 w-4 shrink-0 text-rose-600 mt-0.5" />
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif
                    @if (session('status'))
                        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 text-xs font-semibold text-emerald-800 flex items-start gap-2">
                            <x-icon name="check" class="h-4 w-4 shrink-0 text-emerald-600 mt-0.5" />
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
                                    <span x-show="!showToken"><x-icon name="eye" class="h-4 w-4" /></span>
                                    <span x-show="showToken" x-cloak><x-icon name="eye-off" class="h-4 w-4" /></span>
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
                                <x-icon name="arrow-right" class="h-4 w-4" />
                            </span>
                            <span x-show="loading" x-cloak class="inline-flex items-center justify-center gap-2">
                                <span>Memproses Masuk…</span>
                            </span>
                        </button>
                    </form>
                </div>

                {{-- PANEL 2: PORTAL CEK STATUS & CETAK KARTU (CUKUP NIS) --}}
                <div x-show="tab === 'cek'" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
                    <div>
                        <h2 class="text-2xl sm:text-[26px] font-black text-slate-900 tracking-tight">Cek Status & Kartu</h2>
                        <p class="mt-1 text-xs sm:text-sm text-slate-500 leading-normal">Cukup masukkan Nomor Induk Siswa (NIS) untuk mengecek pilihan eskul atau mencetak ulang kartu Anda.</p>
                    </div>

                    <form @submit.prevent="cekStatus()" class="space-y-3">
                        <div>
                            <label for="nisCek" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Nomor Induk Siswa (NIS)</label>
                            <div class="flex gap-2">
                                <input id="nisCek"
                                       type="text"
                                       inputmode="numeric"
                                       x-model="nisCek"
                                       placeholder="cth: 2012038"
                                       class="flex-1 rounded-xl border border-slate-200 bg-white py-2.5 px-3.5 text-sm font-semibold text-slate-900 placeholder:font-normal placeholder:text-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition shadow-xs" />
                                <button type="submit"
                                        :disabled="loadingCek || !nisCek.trim()"
                                        class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-xs disabled:opacity-50 cursor-pointer">
                                    <span x-show="!loadingCek">Periksa</span>
                                    <span x-show="loadingCek" x-cloak>Cek...</span>
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Pesan Error Cek --}}
                    <div x-show="errorCek" x-cloak class="rounded-xl border border-rose-200 bg-rose-50 p-3.5 text-xs font-semibold text-rose-800 flex items-start gap-2">
                        <x-icon name="info" class="h-4 w-4 shrink-0 text-rose-600 mt-0.5" />
                        <span x-text="errorCek"></span>
                    </div>

                    {{-- Hasil Cek: Belum Terdaftar --}}
                    <div x-show="resultCek && resultCek.status === 'BELUM_MENDAFTAR'" x-cloak class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-xs space-y-3 shadow-2xs">
                        <div class="flex items-start gap-2.5">
                            <x-icon name="info" class="h-4 w-4 shrink-0 text-amber-700 mt-0.5" />
                            <div>
                                <p class="font-bold text-amber-900 text-sm" x-text="resultCek ? resultCek.nama + ' (' + resultCek.kelas + ')' : ''"></p>
                                <p class="text-amber-800 mt-0.5" x-text="resultCek?.message"></p>
                            </div>
                        </div>
                        <button type="button" @click="tab = 'login'" class="w-full py-2 rounded-xl bg-amber-600 text-white font-bold hover:bg-amber-700 transition cursor-pointer text-center">
                            Beralih ke Form Masuk & Pilih Eskul &rarr;
                        </button>
                    </div>

                    {{-- Hasil Cek: Terdaftar Resmi --}}
                    <div x-show="resultCek && resultCek.status === 'TERDAFTAR'" x-cloak class="rounded-2xl border border-emerald-200 bg-white p-4 text-xs space-y-3 shadow-sm">
                        <div class="flex items-start justify-between border-b border-slate-100 pb-3">
                            <div>
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    <x-icon name="check-circle" class="h-3 w-3" />
                                    <span>TERDAFTAR RESMI</span>
                                </span>
                                <h3 class="font-extrabold text-slate-900 text-base mt-1" x-text="resultCek?.nama"></h3>
                                <p class="text-slate-500 text-xs">NIS: <strong x-text="resultCek?.nis"></strong> &bull; Kelas: <strong x-text="resultCek?.kelas"></strong></p>
                            </div>
                        </div>

                        <div class="rounded-xl bg-blue-50/70 border border-blue-200/80 p-3 space-y-1.5">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-blue-700">Ekstrakurikuler Terdaftar</span>
                            <p class="text-sm font-black text-blue-950" x-text="resultCek?.eskul_utama"></p>
                            <p class="text-slate-600 text-[11px]">Guru Pembina: <strong x-text="resultCek?.pembina_nama"></strong></p>
                            <p class="text-slate-600 text-[11px]">Jadwal: <span x-text="resultCek?.jadwal"></span></p>
                        </div>

                        <a :href="resultCek?.kartu_url" target="_blank" class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition shadow-xs cursor-pointer">
                            <x-icon name="printer" class="h-4 w-4" />
                            <span>Lihat / Cetak Kartu Peserta ID-1</span>
                        </a>
                    </div>
                </div>

                {{-- Link Bantuan Bawah --}}
                <div class="mt-5 text-center space-y-2">
                    <p class="text-xs text-slate-500">Lupa token? Hubungi <span class="font-medium text-slate-700">admin/wali kelas</span> anda.</p>
                    <p class="text-xs text-slate-600">Bukan siswa? <a href="{{ route('pembina.login') }}" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline">Masuk sebagai Pembina / Wali Kelas &rarr;</a></p>
                    <div class="pt-2">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600 hover:text-blue-600 hover:underline transition">
                            &larr; Kembali ke Beranda
                        </a>
                    </div>
                </div>
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

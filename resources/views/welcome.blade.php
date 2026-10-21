<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Bagimu Extra') }} — Pendaftaran Ekstrakurikuler Digital</title>
    <x-favicon />
    <meta name="theme-color" content="#2563eb">
    <meta name="description" content="Pendaftaran ekstrakurikuler digital SMK Bagimu Negeriku — jelajahi eskul, lihat jadwal mingguan, dan daftar mudah dengan NIS dan token kartu pelajar.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" media="print" onload="this.media='all'" />
    <link rel="stylesheet" href="{{ asset('splash.css') }}">
    <script>
        (function() {
            try {
                if (sessionStorage.getItem('bagimu_splash_shown') === '1') {
                    document.documentElement.classList.add('splash-skip');
                }
            } catch (e) {}
        })();
    </script>
    <style>
        #app-content { opacity: 0; }
        #app-content.show { animation: muncul .7s ease forwards; }
        @keyframes muncul { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }
        html.splash-skip #splash { display: none !important; }
        html.splash-skip #app-content { opacity: 1 !important; transform: none !important; animation: none !important; }
    </style>
    <noscript>
        <style>
            #splash { display: none !important; }
            #app-content { opacity: 1 !important; transform: none !important; }
        </style>
    </noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white" x-data="{
    detailModal: false,
    selectedEskul: null,
    bukaDetail(eskul) {
        this.selectedEskul = eskul;
        this.detailModal = true;
    }
}">
@php
    $sekolah = \App\Models\AppSetting::get('school.name', config('app.name', 'Bagimu Extra'));
@endphp

<!-- ====== SPLASH SCREEN (Micro-Brand Reveal) ====== -->
<div id="splash" role="status" aria-label="Selamat Datang di Bagimu Extra" tabindex="0">
    <div class="sp-ambient" aria-hidden="true"></div>
    <div class="sp-content">
        <div class="sp-badge">
            <img src="{{ asset('splash-logo.png') }}" alt="Logo {{ $sekolah }}" width="150" height="150">
        </div>
        <div class="sp-brand">
            <span class="sp-tag">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <span>Portal Resmi Ekstrakurikuler</span>
            </span>
            <h1 class="sp-name">{{ $sekolah }}</h1>
            <p class="sp-tagline">"Mewadahi Talenta, Mengukir Prestasi"</p>
            <p class="sp-motto">4B · Berbudi · Berkualitas · Berdaya · Berhasil</p>
        </div>
        <div class="sp-loader" aria-hidden="true">
            <div class="sp-bar"></div>
        </div>
        <div class="sp-status">
            <span>Menyiapkan beranda...</span>
        </div>
    </div>
</div>
<!-- ================================================ -->

<div id="app-content" class="w-full max-w-7xl mx-auto space-y-10 px-4 sm:px-6 lg:px-8 py-6 sm:py-10">
    {{-- Top Navbar Mini --}}
    <header class="flex items-center justify-between pb-2 border-b border-slate-200/70">
        <div class="flex items-center gap-3">
            <x-school-logo :size="42" />
            <div>
                <span class="text-sm font-extrabold text-slate-900 tracking-tight block">{{ config('app.name', 'Bagimu Extra') }}</span>
                <span class="text-[11px] font-medium text-slate-500 block truncate max-w-[200px] sm:max-w-none">{{ $sekolah }}</span>
            </div>
        </div>
        <div class="flex items-center gap-2 sm:gap-3">
            @if (auth()->check())
                <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700 transition shadow-xs shadow-blue-600/20">
                    <span>Ke Dashboard</span>
                    <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                </a>
            @else
                <a href="{{ route('pembina.login') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:border-slate-400 transition">
                    <x-icon name="user-group" class="h-3.5 w-3.5 text-slate-500" />
                    <span>Masuk Pembina</span>
                </a>
                <a href="{{ route('siswa.login') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700 transition shadow-xs shadow-blue-600/20">
                    <x-icon name="user" class="h-3.5 w-3.5" />
                    <span>Masuk Siswa</span>
                </a>
            @endif
        </div>
    </header>

    {{-- Hero Section - Bersih, Cerah, Profesional --}}
    <section class="rounded-3xl bg-blue-600 px-6 py-10 sm:px-12 sm:py-14 text-white shadow-sm">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold bg-white/15 text-white border border-white/20 mb-4">
                <span class="flex h-2 w-2 rounded-full bg-white"></span>
                <span>{{ $sekolah }}</span>
            </div>

            <h1 class="text-3xl font-black leading-tight sm:text-5xl tracking-tight text-white">
                Pendaftaran Ekstrakurikuler Digital & Terorganisir
            </h1>

            <p class="mt-4 text-sm sm:text-base leading-relaxed text-blue-100 max-w-2xl">
                Jelajahi beragam kegiatan ekstrakurikuler unggulan, periksa jadwal latihan mingguan, dan lakukan pendaftaran langsung menggunakan NIS dan token kartu pelajar dari ponsel Anda.
            </p>

            <div class="mt-8 flex flex-wrap items-center gap-3.5">
                @if (auth()->check())
                    <a href="{{ url('/dashboard') }}" class="group inline-flex h-12 items-center gap-2.5 rounded-2xl border border-transparent bg-white px-6 text-sm font-bold text-blue-700 shadow-md shadow-blue-950/20 hover:bg-blue-50/95 hover:shadow-lg active:scale-[0.98] transition-all duration-150">
                        <x-icon name="home" class="h-4 w-4 text-blue-600 shrink-0 transition-transform group-hover:scale-110" />
                        <span>Buka Dashboard</span>
                        <x-icon name="arrow-right" class="h-3.5 w-3.5 text-blue-500 shrink-0 transition-transform group-hover:translate-x-0.5" />
                    </a>
                @else
                    <a href="{{ route('siswa.login') }}" class="group inline-flex h-12 items-center gap-2.5 rounded-2xl border border-transparent bg-white px-5 sm:px-6 text-sm font-bold text-blue-700 shadow-md shadow-blue-950/20 hover:bg-blue-50/95 hover:shadow-lg active:scale-[0.98] transition-all duration-150">
                        <x-icon name="user" class="h-4 w-4 text-blue-600 shrink-0 transition-transform group-hover:scale-110" />
                        <span>Masuk Siswa (NIS + Token)</span>
                    </a>
                    <a href="{{ route('pembina.login') }}" class="group inline-flex h-12 items-center gap-2.5 rounded-2xl border border-white/35 bg-white/15 backdrop-blur-md px-5 sm:px-6 text-sm font-bold text-white shadow-xs hover:bg-white/25 hover:border-white/50 active:scale-[0.98] transition-all duration-150">
                        <x-icon name="user-group" class="h-4 w-4 text-white shrink-0 transition-transform group-hover:scale-110" />
                        <span>Masuk Pembina</span>
                    </a>
                @endif
                <a href="#eskul" class="group inline-flex h-12 items-center gap-2.5 rounded-2xl border border-white/25 bg-white/10 backdrop-blur-md px-5 sm:px-6 text-sm font-bold text-white/95 shadow-xs hover:bg-white/20 hover:border-white/40 hover:text-white active:scale-[0.98] transition-all duration-150">
                    <x-icon name="arrow-down" class="h-4 w-4 text-blue-100 shrink-0 transition-transform group-hover:translate-y-0.5" />
                    <span>Lihat Katalog Eskul</span>
                </a>
            </div>
        </div>
    </section>

    {{-- Katalog Ringkas --}}
    <section id="eskul" class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Katalog Ekstrakurikuler</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Pilihan kegiatan pengembangan minat dan bakat siswa</p>
            </div>
        </div>

        @if ($eskuls->isEmpty())
            <x-card><x-empty-state icon="trophy" title="Belum ada eskul aktif" message="Katalog ekstrakurikuler akan segera muncul setelah disahkan oleh pihak sekolah." /></x-card>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($eskuls as $eskul)
                    @php
                        $detailJson = [
                            'id' => $eskul->id,
                            'name' => $eskul->name,
                            'description' => $eskul->description ?? 'Tidak ada deskripsi tambahan.',
                            'pembina' => $eskul->pembina?->name ?? 'Belum ditentukan',
                            'kuota' => $eskul->kuota,
                            'kuota_aktif' => (bool) $eskul->kuota_aktif,
                            'jadwals' => $eskul->jadwals->map(fn ($j) => [
                                'hari' => ucfirst(strtolower($j->hari)),
                                'jam' => $j->jam_mulai.'–'.$j->jam_selesai,
                                'tempat' => $j->tempat,
                            ])->values()->all(),
                        ];
                    @endphp
                    <div class="flex flex-col justify-between h-full rounded-2xl border border-slate-200/90 bg-white p-5 sm:p-6 shadow-xs hover:border-blue-300 hover:shadow-sm transition">
                        <div>
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="font-bold text-base sm:text-lg text-slate-900">{{ $eskul->name }}</h3>
                                @if ($eskul->kuota_aktif)
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-bold text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                        Kuota {{ $eskul->kuota }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                        Kuota Terbuka
                                    </span>
                                @endif
                            </div>

                            @if ($eskul->description)
                                <p class="mt-2 line-clamp-2 text-xs sm:text-sm text-slate-600 leading-relaxed">{{ $eskul->description }}</p>
                            @endif

                            <div class="mt-3 flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                                <x-icon name="user" class="h-3.5 w-3.5 text-slate-400" />
                                <span>Pembina: <strong class="text-slate-800 font-semibold">{{ $eskul->pembina?->name ?? 'Belum ditentukan' }}</strong></span>
                            </div>

                            @if ($eskul->jadwals->isNotEmpty())
                                <div class="mt-3 pt-3 border-t border-slate-100">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Jadwal Latihan</p>
                                    <ul class="space-y-1 text-xs text-slate-600">
                                        @foreach ($eskul->jadwals->take(2) as $j)
                                            <li class="flex items-center gap-1.5">
                                                <span class="inline-flex items-center rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold text-slate-700">
                                                    {{ ucfirst(strtolower($j->hari)) }}
                                                </span>
                                                <span>{{ $j->jam_mulai }}–{{ $j->jam_selesai }}</span>
                                                <span class="text-slate-400">•</span>
                                                <span class="text-slate-700 truncate">{{ $j->tempat }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div class="mt-5 pt-3 border-t border-slate-100">
                            <button type="button" @click="bukaDetail(@js($detailJson))"
                                    class="w-full inline-flex items-center justify-center gap-1.5 rounded-xl border border-blue-600/30 bg-blue-50/60 px-4 py-2.5 text-xs font-bold text-blue-700 hover:bg-blue-600 hover:text-white transition shadow-2xs">
                                <span>Detail Eskul</span>
                                <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Jadwal Mingguan (Pertahankan, tombol jadwal lengkap dihapus) --}}
    <section class="space-y-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Jadwal Latihan Mingguan</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Waktu dan lokasi latihan rutin ekstrakurikuler</p>
        </div>

        @if ($jadwalMingguan->isEmpty())
            <x-card><x-empty-state icon="calendar" title="Jadwal belum diisi" message="Jadwal sesi latihan mingguan akan segera dipublikasikan di sini." /></x-card>
        @else
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($jadwalMingguan as $hari => $baris)
                    <div class="rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <span class="inline-flex items-center rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700">
                                {{ ucfirst(strtolower($hari)) }}
                            </span>
                            <span class="text-[11px] font-medium text-slate-400">{{ count($baris) }} sesi latihan</span>
                        </div>
                        <ul class="mt-3 space-y-2 text-xs text-slate-600">
                            @foreach ($baris as $b)
                                <li class="flex flex-col gap-0.5 rounded-xl bg-slate-50/80 p-2.5 border border-slate-100">
                                    <span class="font-bold text-slate-800 text-sm">{{ $b['eskul'] }}</span>
                                    <div class="flex items-center gap-2 text-slate-500 text-[11px]">
                                        <span class="font-medium text-slate-700">{{ $b['jam'] }}</span>
                                        <span>•</span>
                                        <span>{{ $b['tempat'] }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Bantuan & FAQ (Rapi dan Tidak Terlihat Kosong) --}}
    <section class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xs">
        <div class="flex items-center gap-3 mb-6">
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                <x-icon name="help-circle" class="h-5 w-5" />
            </span>
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-900">Pusat Bantuan & Pertanyaan Umum (FAQ)</h2>
                <p class="text-xs text-slate-500">Informasi seputar login kartu pelajar dan pendaftaran eskul</p>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-2xl bg-slate-50/80 p-5 border border-slate-100 space-y-2">
                <div class="flex items-center gap-2">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700 text-xs font-bold">1</span>
                    <h3 class="font-bold text-slate-900 text-sm">Bagaimana jika lupa atau belum memiliki token kartu pelajar?</h3>
                </div>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed pl-8">
                    Silakan melapor langsung ke bagian Tata Usaha (TU) sekolah. Petugas TU akan menerbitkan token login 8 digit baru pada kartu pelajar Anda dan token lama otomatis dihanguskan.
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50/80 p-5 border border-slate-100 space-y-2">
                <div class="flex items-center gap-2">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700 text-xs font-bold">2</span>
                    <h3 class="font-bold text-slate-900 text-sm">NIS tidak terdaftar saat mencoba masuk ke sistem?</h3>
                </div>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed pl-8">
                    Pastikan nomor induk siswa (NIS) sudah sesuai dengan data resmi sekolah. Jika masih terkendala, hubungi Wali Kelas untuk memastikan data Anda sudah aktif pada data pokok siswa.
                </p>
            </div>
        </div>
    </section>

    {{-- Footer Bersih (Hanya Lisensi dan Info Sekolah) --}}
    <footer class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xs text-sm text-slate-600">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <x-school-logo :size="36" />
                <div>
                    <p class="font-bold text-slate-900 text-sm leading-tight">{{ $sekolah }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ \App\Models\AppSetting::get('school.address', 'Semarang') }}{{ \App\Models\AppSetting::get('school.contact') ? ' • '.\App\Models\AppSetting::get('school.contact') : '' }}
                    </p>
                </div>
            </div>
            <div class="text-xs text-slate-400 text-center sm:text-right">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Bagimu Extra') }}. Seluruh hak cipta dilindungi.</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Aplikasi Pendaftaran Ekstrakurikuler Sekolah</p>
            </div>
        </div>
    </footer>
</div>

{{-- MODAL DETAIL ESKUL (Alpine.js) --}}
<div x-show="detailModal"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
     style="display: none;"
     @keydown.escape.window="detailModal = false">

    <div class="w-full max-w-lg rounded-3xl bg-white shadow-2xl border border-slate-200 overflow-hidden"
         @click.outside="detailModal = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">

        {{-- Modal Header --}}
        <div class="flex items-start justify-between p-6 pb-4 border-b border-slate-100">
            <div>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold"
                      :class="selectedEskul?.kuota_aktif ? 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20' : 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20'">
                    <span x-text="selectedEskul?.kuota_aktif ? 'Kapasitas Kuota: ' + selectedEskul?.kuota + ' Siswa' : 'Kuota Terbuka'"></span>
                </span>
                <h3 class="text-xl font-black text-slate-900 mt-1.5" x-text="selectedEskul?.name"></h3>
            </div>
            <button type="button" @click="detailModal = false" class="rounded-xl p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto text-sm text-slate-700">
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Deskripsi Kegiatan</h4>
                <p class="text-sm text-slate-600 leading-relaxed" x-text="selectedEskul?.description"></p>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-4 space-y-2.5 text-xs sm:text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Guru Pembina</span>
                    <span class="font-bold text-slate-900" x-text="selectedEskul?.pembina"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Kapasitas Kuota</span>
                    <span class="font-semibold text-slate-800" x-text="selectedEskul?.kuota_aktif ? selectedEskul?.kuota + ' Peserta' : 'Tidak Dibatasi'"></span>
                </div>
            </div>

            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Jadwal Latihan Mingguan</h4>
                <template x-if="selectedEskul?.jadwals && selectedEskul.jadwals.length > 0">
                    <ul class="space-y-2">
                        <template x-for="jadwal in selectedEskul.jadwals" :key="jadwal.hari + jadwal.jam">
                            <li class="flex items-center justify-between rounded-xl border border-slate-100 bg-white p-3 shadow-2xs">
                                <span class="font-bold text-slate-800 text-xs" x-text="jadwal.hari"></span>
                                <div class="text-right text-xs">
                                    <p class="font-semibold text-blue-700" x-text="jadwal.jam"></p>
                                    <p class="text-slate-400" x-text="'@ ' + jadwal.tempat"></p>
                                </div>
                            </li>
                        </template>
                    </ul>
                </template>
                <template x-if="!selectedEskul?.jadwals || selectedEskul.jadwals.length === 0">
                    <p class="text-xs text-slate-400 italic">Jadwal latihan akan ditentukan oleh pihak pembina.</p>
                </template>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="p-6 pt-3 bg-slate-50/70 border-t border-slate-100 flex items-center justify-end gap-3">
            <button type="button" @click="detailModal = false" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                Tutup
            </button>
            <a :href="'{{ route('siswa.login') }}'"
               class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700 transition shadow-xs shadow-blue-600/20">
                <span>Daftar Eskul Ini</span>
                <x-icon name="arrow-right" class="h-3.5 w-3.5" />
            </a>
        </div>
    </div>
</div>

<script src="{{ asset('splash.js') }}"></script>
<script>
    document.addEventListener('splash:selesai', function () {
        var app = document.getElementById('app-content');
        if (app) {
            app.classList.add('show');
        }
    });
</script>
</body>
</html>

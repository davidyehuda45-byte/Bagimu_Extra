<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Bagimu Extra') }} — Pendaftaran Ekstrakurikuler Digital</title>
    <x-favicon />
    <meta name="theme-color" content="#2563eb">
    <meta name="description" content="Pendaftaran ekstrakurikuler digital — jelajahi eskul, lihat jadwal, daftar dengan NIS dan token kartu pelajar.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" media="print" onload="this.media='all'" />
    <link rel="stylesheet" href="{{ asset('splash.css') }}">
    <style>
        #app-content { opacity: 0; }
        #app-content.show { animation: muncul .7s ease forwards; }
        @keyframes muncul { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }
    </style>
    <noscript>
        <style>
            #splash { display: none !important; }
            #app-content { opacity: 1 !important; transform: none !important; }
        </style>
    </noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white">
@php($sekolah = \App\Models\AppSetting::get('school.name', config('app.name', 'Bagimu Extra')))

<!-- ====== SPLASH SCREEN ====== -->
<div id="splash" role="status" aria-label="Memuat aplikasi">
  <div class="sp-badge"><img src="{{ asset('splash-logo.png') }}" alt="Logo {{ $sekolah }}"></div>
  <div class="sp-ekskul" id="sp-ekskul"></div>
  <h2 class="sp-name">{{ $sekolah }}</h2>
  <p class="sp-motto">Berbudi · Berkualitas · Berdaya · Berhasil</p>
  <div class="sp-pct"><b id="sp-num">1</b>%</div>
</div>
<!-- =========================== -->

<div id="app-content" class="mx-auto max-w-6xl space-y-10 px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    {{-- Top Navbar Mini --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <x-school-logo :size="38" />
            <div>
                <span class="text-sm font-extrabold text-slate-900 tracking-tight block">{{ config('app.name', 'Bagimu Extra') }}</span>
                <span class="text-[11px] font-medium text-slate-500 block truncate max-w-[200px] sm:max-w-none">{{ $sekolah }}</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if (auth()->check())
                <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700 transition shadow-xs shadow-blue-600/20">
                    <span>Ke Dashboard</span>
                    <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                </a>
            @else
                <a href="{{ route('siswa.login') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700 transition shadow-xs shadow-blue-600/20">
                    <span>Masuk Siswa</span>
                    <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                </a>
            @endif
        </div>
    </div>

    {{-- Hero Section - Bersih, Cerah, Profesional --}}
    <header class="rounded-3xl bg-blue-600 px-6 py-12 text-white sm:px-12 sm:py-16 shadow-sm">
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

            <div class="mt-8 flex flex-wrap items-center gap-3">
                @if (auth()->check())
                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-blue-700 hover:bg-blue-50 transition shadow-sm">
                        <span>Buka Dashboard</span>
                        <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                @else
                    <a href="{{ route('siswa.login') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-blue-700 hover:bg-blue-50 transition shadow-sm">
                        <x-icon name="user" class="h-4 w-4 text-blue-600" />
                        <span>Masuk Siswa (NIS + Token)</span>
                    </a>
                @endif
                <a href="#eskul" class="inline-flex items-center gap-2 rounded-xl border border-white/30 bg-white/10 px-5 py-3 text-sm font-semibold text-white hover:bg-white/20 transition">
                    <span>Lihat Katalog Eskul</span>
                    <x-icon name="arrow-down" class="h-4 w-4 text-blue-100" />
                </a>
            </div>
        </div>
    </header>

    {{-- Katalog Ringkas --}}
    <section id="eskul" class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Katalog Ekstrakurikuler</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Pilihan kegiatan pengembangan minat dan bakat siswa</p>
            </div>
            <a href="{{ route('publik.eskul') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-blue-600 hover:text-blue-700">
                <span>Semua eskul</span>
                <x-icon name="arrow-right" class="h-3.5 w-3.5" />
            </a>
        </div>

        @if ($eskuls->isEmpty())
            <x-card><x-empty-state icon="trophy" title="Belum ada eskul aktif" message="Katalog ekstrakurikuler akan segera muncul setelah disahkan oleh pihak sekolah." /></x-card>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($eskuls->take(6) as $eskul)
                    <x-eskul-card :eskul="$eskul" :action-url="route('siswa.daftar', ['eskul' => $eskul->name])" action-label="Pilih Eskul Ini" />
                @endforeach
            </div>
        @endif
    </section>

    {{-- Jadwal Mingguan --}}
    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Jadwal Latihan Mingguan</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Waktu dan lokasi latihan rutin ekstrakurikuler</p>
            </div>
            <a href="{{ route('publik.jadwal') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-blue-600 hover:text-blue-700">
                <span>Jadwal lengkap</span>
                <x-icon name="arrow-right" class="h-3.5 w-3.5" />
            </a>
        </div>

        @if ($jadwalMingguan->isEmpty())
            <x-card><x-empty-state icon="calendar" title="Jadwal belum diisi" message="Jadwal sesi latihan mingguan akan segera dipublikasikan di sini." /></x-card>
        @else
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($jadwalMingguan as $hari => $baris)
                    <div class="rounded-2xl sm:rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs">
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

    {{-- Aturan & FAQ --}}
    <section class="grid gap-5 md:grid-cols-2">
        <div class="rounded-2xl sm:rounded-3xl border border-slate-200/90 bg-white p-6 shadow-xs">
            <div class="flex items-center gap-2.5 mb-4">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <x-icon name="clipboard" class="h-4 w-4" />
                </span>
                <h2 class="text-base font-bold text-slate-900">Ketentuan & Aturan Pendaftaran</h2>
            </div>
            <ol class="space-y-2.5 text-xs sm:text-sm text-slate-600 pl-4 list-decimal marker:text-blue-600 marker:font-bold leading-relaxed">
                <li>Setiap siswa hanya dapat memilih satu ekstrakurikuler utama per periode tahun ajaran.</li>
                <li>Pendaftaran dilakukan mandiri dengan NIS dan Token 8 digit yang tertera pada kartu pelajar.</li>
                <li>Apabila kuota eskul utama telah penuh, siswa dipersilakan memilih alternatif eskul lainnya.</li>
                <li>Setelah pendaftaran terkirim, data siswa resmi terdaftar dan terkunci untuk periode berjalan.</li>
            </ol>
            <div class="mt-5 pt-4 border-t border-slate-100">
                @if (auth()->check())
                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700">
                        <span>Buka dashboard akun Anda</span>
                        <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                    </a>
                @else
                    <a href="{{ route('siswa.login') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700">
                        <span>Mulai pendaftaran sekarang</span>
                        <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                    </a>
                @endif
            </div>
        </div>

        <div class="rounded-2xl sm:rounded-3xl border border-slate-200/90 bg-white p-6 shadow-xs">
            <div class="flex items-center gap-2.5 mb-4">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                    <x-icon name="help-circle" class="h-4 w-4" />
                </span>
                <h2 class="text-base font-bold text-slate-900">Pertanyaan yang Sering Diajukan (FAQ)</h2>
            </div>
            <div class="space-y-3 text-xs sm:text-sm text-slate-600 leading-relaxed">
                <div class="rounded-xl bg-slate-50/70 p-3 border border-slate-100">
                    <p class="font-bold text-slate-900 text-xs sm:text-sm">Bagaimana jika lupa token kartu pelajar?</p>
                    <p class="mt-1 text-xs text-slate-500">Silakan lapor ke bagian Tata Usaha (TU) sekolah. Petugas TU akan mencetak token baru dan token lama otomatis dinonaktifkan.</p>
                </div>
                <div class="rounded-xl bg-slate-50/70 p-3 border border-slate-100">
                    <p class="font-bold text-slate-900 text-xs sm:text-sm">NIS tidak terdaftar saat mencoba masuk?</p>
                    <p class="mt-1 text-xs text-slate-500">Pastikan NIS Anda sudah diinput ke data pokok siswa oleh pihak sekolah. Konfirmasi ke wali kelas bila data belum tercatat.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer Sekolah --}}
    <footer class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xs text-sm text-slate-600">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-3">
                <x-school-logo :size="40" />
                <div>
                    <p class="font-bold text-slate-900 text-base leading-tight">{{ $sekolah }}</p>
                    <p class="mt-1 text-xs text-slate-500 max-w-xl">
                        {{ \App\Models\AppSetting::get('school.address', '') }}{{ \App\Models\AppSetting::get('school.contact') ? ' • Hubungi: '.\App\Models\AppSetting::get('school.contact') : '' }}
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('siswa.login') }}" class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Portal Siswa
                </a>
                <a href="{{ route('verify.form') }}" class="rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition">
                    Verifikasi Sertifikat Resmi
                </a>
            </div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-slate-100 pt-4 text-xs text-slate-400">
            <span>&copy; {{ date('Y') }} {{ config('app.name', 'Bagimu Extra') }} — David Yehuda Surbakti &amp; Vines Fernando Siritoitet. Seluruh hak cipta dilindungi.</span>
            <div class="flex items-center gap-3">
                <a href="{{ route('verify.form') }}" class="hover:text-blue-600 transition">Verifikasi Dokumen</a>
                <span>•</span>
                <a href="{{ route('publik.eskul') }}" class="hover:text-blue-600 transition">Katalog Eskul</a>
            </div>
        </div>
    </footer>
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

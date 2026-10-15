<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Bagimu Extra') }} — Pendaftaran Eskul</title>
    <meta name="theme-color" content="#1e3a8a">
    <meta name="description" content="Pendaftaran ekstrakurikuler digital — jelajahi eskul, lihat jadwal, daftar dengan NIS dan token.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" media="print" onload="this.media='all'" />
    <noscript><link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" /></noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
@php($sekolah = \App\Models\AppSetting::get('school.name', config('app.name', 'Bagimu Extra')))
<div class="w-full space-y-8 px-4 sm:px-8 py-10">
    {{-- Hero --}}
    <header class="overflow-hidden rounded-3xl bg-blue-900 px-6 py-10 text-white sm:px-10">
        <div class="flex items-center gap-3">
            <x-school-logo :size="44" />
            <p class="text-xs font-semibold uppercase tracking-widest text-blue-300">
                {{ $sekolah }}
            </p>
        </div>
        <h1 class="mt-4 text-3xl font-extrabold leading-tight sm:text-4xl">
            {{ config('app.name', 'Bagimu Extra') }} — Pendaftaran Ekstrakurikuler Digital
        </h1>
        <p class="mt-3 w-full text-sm leading-relaxed text-blue-100">
            Jelajahi eskul, lihat jadwal latihan, lalu daftar dengan NIS dan token
            kartu dari sekolah. Langsung dari HP, tanpa install aplikasi.
        </p>
        <div class="mt-6 flex flex-wrap gap-3">
            @if (auth()->check())
                <a href="{{ url('/dashboard') }}" class="rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-blue-900 hover:bg-blue-50">
                    Ke Dashboard
                </a>
            @else
                <a href="{{ route('siswa.login') }}" class="rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-blue-900 hover:bg-blue-50">
                    Masuk Siswa
                </a>
            @endif
            <a href="#eskul" class="rounded-xl border border-white/30 px-5 py-2.5 text-sm font-semibold text-white hover:bg-white/10">
                Lihat Katalog Eskul
            </a>
        </div>
    </header>

    {{-- Katalog ringkas --}}
    <section id="eskul">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-900">Katalog Eskul</h2>
            <a href="{{ route('publik.eskul') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                Semua eskul →
            </a>
        </div>
        @if ($eskuls->isEmpty())
            <x-card><x-empty-state icon="trophy" title="Belum ada eskul aktif" message="Katalog akan muncul setelah TU mengaktifkan eskul." /></x-card>
        @else
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach ($eskuls->take(6) as $eskul)
                    <x-eskul-card :eskul="$eskul" :action-url="route('siswa.daftar', ['eskul' => $eskul->name])" action-label="Pilih eskul ini" />
                @endforeach
            </div>
        @endif
    </section>

    {{-- Jadwal mingguan --}}
    <section>
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-900">Jadwal Latihan Mingguan</h2>
            <a href="{{ route('publik.jadwal') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                Lihat jadwal latihan →
            </a>
        </div>
        @if ($jadwalMingguan->isEmpty())
            <x-card><x-empty-state icon="calendar" title="Jadwal belum diisi" message="Jadwal latihan akan muncul di sini." /></x-card>
        @else
            <div class="space-y-2">
                @foreach ($jadwalMingguan as $hari => $baris)
                    <x-card>
                        <div class="font-bold text-slate-900">{{ ucfirst(strtolower($hari)) }}</div>
                        <ul class="mt-2 space-y-1 text-sm text-slate-600">
                            @foreach ($baris as $b)
                                <li>{{ $b['eskul'] }} • {{ $b['jam'] }} • {{ $b['tempat'] }}</li>
                            @endforeach
                        </ul>
                    </x-card>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Aturan + FAQ --}}
    <section class="grid gap-3 md:grid-cols-2">
        <x-card pad="p-5">
            <h2 class="font-bold text-slate-900">Aturan Pendaftaran</h2>
            <ol class="mt-2 list-decimal space-y-1 pl-5 text-sm text-slate-600">
                <li>Satu akun mendaftar satu kali per tahun ajaran.</li>
                <li>Pilih satu eskul; bila kuota penuh pilih eskul lain.</li>
                <li>Pendaftaran hanya saat jendela dibuka sekolah.</li>
                <li>Status dapat dipantau di halaman Status masing-masing.</li>
            </ol>
            @if (auth()->check())
                <a href="{{ url('/dashboard') }}" class="mt-3 inline-block text-sm font-medium text-blue-600 hover:text-blue-700">
                    Ke dashboard →
                </a>
            @else
                <a href="{{ route('siswa.login') }}" class="mt-3 inline-block text-sm font-medium text-blue-600 hover:text-blue-700">
                    Daftar sekarang →
                </a>
            @endif
        </x-card>
        <x-card pad="p-5">
            <h2 class="font-bold text-slate-900">FAQ</h2>
            <div class="mt-2 space-y-2 text-sm text-slate-600">
                <p><b class="text-slate-800">Lupa token kartu?</b> Minta token baru ke TU — token lama otomatis hangus.</p>
                <p><b class="text-slate-800">NIS tidak terdaftar?</b> Pastikan data sudah diinput TU atau hubungi TU.</p>
                <p><b class="text-slate-800">Butuh bantuan?</b> Hubungi kontak sekolah di bawah.</p>
            </div>
        </x-card>
    </section>

    {{-- Kontak --}}
    <footer class="rounded-2xl bg-slate-100 p-5 text-sm text-slate-600">
        <p class="font-bold text-slate-800">{{ $sekolah }}</p>
        <p>
            {{ \App\Models\AppSetting::get('school.address', '') }}{{ \App\Models\AppSetting::get('school.contact') ? ' • '.\App\Models\AppSetting::get('school.contact') : '' }}
        </p>
        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-3 text-xs text-slate-500">
            <span>&copy; {{ date('Y') }} {{ config('app.name', 'Bagimu Extra') }} — {{ $sekolah }}</span>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('siswa.login') }}" class="font-medium text-slate-600 hover:text-blue-700 hover:underline">Portal Siswa</a>
                <a href="{{ route('verify.form') }}" class="font-medium text-slate-600 hover:text-blue-700 hover:underline">Verifikasi E-Card / Sertifikat</a>
            </div>
        </div>
    </footer>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Bagimu Extra') }} — Pendaftaran Eskul</title>
    <meta name="theme-color" content="#1e3a8a">
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
            <p class="text-sm text-slate-500">Belum ada eskul aktif.</p>
        @else
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach ($eskuls->take(6) as $eskul)
                    <div class="rounded-2xl border border-slate-200 bg-white text-slate-900 shadow-sm">
                        <div class="p-4">
                            <div class="flex items-center justify-between gap-2">
                                <p class="font-bold text-slate-900">{{ $eskul->name }}</p>
                                @if ($eskul->kuota_aktif)
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold whitespace-nowrap bg-amber-100 text-amber-800 ring-1 ring-inset ring-amber-600/20">Kuota {{ $eskul->kuota }}</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold whitespace-nowrap bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-600/20">Bebas</span>
                                @endif
                            </div>
                            @if ($eskul->description)
                                <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ $eskul->description }}</p>
                            @endif
                            @if ($eskul->jadwals->isNotEmpty())
                                <p class="mt-2 text-xs text-slate-500">
                                    {{ $eskul->jadwals->take(2)->map(fn ($j) => ucfirst(strtolower($j->hari)).' '.$j->jam_mulai.'–'.$j->jam_selesai)->join(' • ') }}{{ $eskul->jadwals->count() > 2 ? ' +'.($eskul->jadwals->count() - 2).' sesi' : '' }}
                                </p>
                            @endif
                        </div>
                    </div>
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
            <p class="text-sm text-slate-500">Jadwal latihan belum diisi.</p>
        @else
            <div class="space-y-2">
                @foreach ($jadwalMingguan as $hari => $baris)
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="font-bold text-slate-900">{{ ucfirst(strtolower($hari)) }}</div>
                        <ul class="mt-2 space-y-1 text-sm text-slate-600">
                            @foreach ($baris as $b)
                                <li>{{ $b['eskul'] }} • {{ $b['jam'] }} • {{ $b['tempat'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Aturan + FAQ --}}
    <section class="grid gap-3 md:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white text-slate-900 shadow-sm">
            <div class="p-5">
                <h2 class="font-bold text-slate-900">Aturan Pendaftaran</h2>
                <ol class="mt-2 list-decimal space-y-1 pl-5 text-sm text-slate-600">
                    <li>Satu akun mendaftar satu kali per tahun ajaran.</li>
                    <li>Pilih eskul utama; cadangan dipakai bila kuota utama penuh.</li>
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
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white text-slate-900 shadow-sm">
            <div class="p-5">
                <h2 class="font-bold text-slate-900">FAQ</h2>
                <div class="mt-2 space-y-2 text-sm text-slate-600">
                    <p><b class="text-slate-800">Lupa token kartu?</b> Minta token baru ke TU — token lama otomatis hangus.</p>
                    <p><b class="text-slate-800">NIS tidak terdaftar?</b> Pastikan data sudah diinput TU atau hubungi TU.</p>
                    <p><b class="text-slate-800">Butuh bantuan?</b> Hubungi kontak sekolah di bawah.</p>
                </div>
            </div>
        </div>
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

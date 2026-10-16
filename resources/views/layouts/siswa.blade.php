<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Siswa') — {{ config('app.name', 'Bagimu Extra') }}</title>
    <x-favicon />
    <meta name="theme-color" content="#1e3a8a">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" media="print" onload="this.media='all'" />
    <noscript><link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" /></noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('layouts.partials.session-guard')
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white">
<a href="#konten-utama" class="skip-link">Lewati ke konten utama</a>
@php
    $pill = fn ($active) => 'flex items-center gap-2 rounded-xl px-3.5 py-2 text-xs sm:text-sm font-bold transition-all duration-150 '
        .($active ? 'bg-blue-600 text-white shadow-sm shadow-blue-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 active:scale-[0.98]');
@endphp
<header class="no-print sticky top-0 z-30 border-b border-slate-200/80 bg-white/95 backdrop-blur-md">
    <div class="flex h-16 w-full items-center justify-between gap-3 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
        <a href="{{ route('siswa.dashboard') }}" class="flex shrink-0 items-center gap-2.5 group">
            <x-school-logo :size="36" />
            <div class="leading-tight">
                <p class="text-sm font-extrabold text-slate-900 tracking-tight group-hover:text-blue-700 transition-colors">{{ config('app.name', 'Bagimu Extra') }}</p>
                <p class="hidden text-[10px] font-medium text-slate-400 sm:block">{{ \App\Models\AppSetting::get('school.name', 'Pendaftaran Eskul') }}</p>
            </div>
        </a>

        <nav class="flex min-w-0 items-center gap-1.5 overflow-x-auto py-1" aria-label="Navigasi siswa">
            <a href="{{ route('siswa.dashboard') }}" class="{{ $pill(Route::is('siswa.dashboard') || Route::is('siswa.pengumuman')) }}"><x-icon name="home" class="h-4 w-4 shrink-0" /><span>Beranda</span></a>
            <a href="{{ route('siswa.daftar') }}" class="{{ $pill(Route::is('siswa.daftar*')) }}"><x-icon name="clipboard" class="h-4 w-4 shrink-0" /><span>Pilih Eskul</span></a>
            <a href="{{ route('siswa.status') }}" class="{{ $pill(Route::is('siswa.status')) }}"><x-icon name="id-card" class="h-4 w-4 shrink-0" /><span>E-Card</span></a>
            <a href="{{ route('siswa.profil') }}" class="{{ $pill(Route::is('siswa.profil')) }}"><x-icon name="user" class="h-4 w-4 shrink-0" /><span class="hidden sm:inline">Saya</span></a>
        </nav>

        <div class="flex shrink-0 items-center gap-2">
            <x-popover label="Notifikasi" panelClass="w-80">
                <x-slot:trigger>
                    <button type="button" class="relative flex h-9 w-9 items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100" aria-label="Notifikasi" aria-haspopup="menu">
                        <x-icon name="bell" class="h-4 w-4" />
                        @php($belum = \App\Support\Notifier::belumDibaca(auth()->id()))
                        @if ($belum > 0)
                            <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-rose-600 px-1 text-[10px] font-bold text-white shadow-sm ring-2 ring-white">
                                {{ $belum > 9 ? '9+' : $belum }}
                            </span>
                        @endif
                    </button>
                </x-slot:trigger>
                @php($notifs = auth()->user()->inappNotifications()->latest()->take(6)->get())
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                        <p class="text-sm font-bold text-slate-900">Notifikasi</p>
                        @if ($belum > 0)
                            <span class="rounded-full bg-rose-50 px-2 py-0.5 text-[11px] font-semibold text-rose-600">{{ $belum }} baru</span>
                        @endif
                    </div>
                    <div class="max-h-80 divide-y divide-slate-100 overflow-y-auto py-1">
                        @forelse ($notifs as $n)
                            <div class="flex items-start justify-between gap-2 rounded-xl p-2.5 {{ $n->dibaca_at ? 'hover:bg-slate-50' : 'bg-blue-50/60' }}">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5">
                                        @if (! $n->dibaca_at)
                                            <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                                        @endif
                                        <p class="truncate text-xs font-bold text-slate-900">{{ $n->judul }}</p>
                                    </div>
                                    <p class="mt-1 line-clamp-2 text-[11px] leading-relaxed text-slate-600">{{ $n->isi }}</p>
                                    <p class="mt-1 text-[10px] text-slate-400">{{ $n->created_at->format('d M Y H:i') }}</p>
                                </div>
                                @if (! $n->dibaca_at && Route::has('siswa.notifikasi.baca'))
                                    <form method="POST" action="{{ route('siswa.notifikasi.baca', $n) }}">
                                        @csrf
                                        <button title="Tandai telah dibaca" class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-lg text-slate-400 hover:bg-white hover:text-blue-600">
                                            <x-icon name="check" class="h-3.5 w-3.5" />
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <div class="py-6 text-center text-xs text-slate-400">Belum ada notifikasi.</div>
                        @endforelse
                    </div>
                    <div class="border-t border-slate-100 pt-2 text-center">
                        <a href="{{ route('siswa.profil') }}" class="text-[11px] font-medium text-slate-500 hover:text-blue-600">Lihat semua di Profil →</a>
                    </div>
            </x-popover>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Keluar" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 hover:bg-rose-50 hover:text-rose-600">
                    <x-icon name="logout" class="h-4 w-4" />
                </button>
            </form>
        </div>
    </div>
</header>

<main id="konten-utama" class="mx-auto w-full max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
    @include('layouts.partials.flash-messages')
    @yield('content')
</main>
</body>
</html>

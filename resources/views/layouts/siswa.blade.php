<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Pendaftaran Siswa') — {{ config('app.name', 'Bagimu Extra') }}</title>
    <x-favicon />
    <meta name="theme-color" content="#1e3a8a">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" media="print" onload="this.media='all'" />
    <noscript><link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" /></noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('layouts.partials.session-guard')
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white">
<a href="#konten-utama" class="skip-link">Lewati ke konten utama</a>

<header class="no-print sticky top-0 z-30 border-b border-slate-200/80 bg-white/95 backdrop-blur-md">
    <div class="flex h-16 w-full items-center justify-between gap-3 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto">
        <div class="flex shrink-0 items-center gap-2.5">
            <x-school-logo :size="36" />
            <div class="leading-tight">
                <p class="text-sm font-extrabold text-slate-900 tracking-tight">{{ config('app.name', 'Bagimu Extra') }}</p>
                <p class="text-[10px] font-medium text-slate-400">{{ \App\Models\AppSetting::get('school.name', 'Pendaftaran Eskul') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            @if (auth()->check())
                <span class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600">
                    <x-icon name="user" class="h-3.5 w-3.5 text-slate-400" />
                    <span>{{ auth()->user()->name }}</span>
                </span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 hover:border-rose-200 transition">
                        <x-icon name="logout" class="h-3.5 w-3.5" />
                        <span>Keluar</span>
                    </button>
                </form>
            @endif
        </div>
    </div>
</header>

<main id="konten-utama" class="w-full px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    @yield('content')
</main>

</body>
</html>

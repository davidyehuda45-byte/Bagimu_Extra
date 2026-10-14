<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel') — {{ config('app.name', 'Bagimu Extra') }}</title>
    <meta name="theme-color" content="#1e3a8a">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('layouts.partials.session-guard')
</head>
<body>
@php
    $user = auth()->user();
    $isAdminOnly = $user && $user->hasRole(\App\Enums\Role::ADMIN);
    $isAdmin = $user && $user->hasRole(\App\Enums\Role::ADMIN, \App\Enums\Role::OPERATOR);
    $isPembina = $user && $user->hasRole(\App\Enums\Role::PEMBINA);
    $isKepsek = $user && $user->hasRole(\App\Enums\Role::KEPALA_SEKOLAH);
    $link = fn ($active) => 'flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition-colors '
        .($active ? 'bg-blue-700 text-white shadow-sm font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900');
@endphp
<div class="min-h-screen lg:flex">
    {{-- Sidebar desktop --}}
    <aside class="no-print fixed inset-y-0 left-0 z-30 hidden w-64 flex-col border-r border-slate-200 bg-white lg:flex">
        <div class="flex items-center gap-3 border-b border-slate-100 px-4 py-5">
            <x-school-logo :size="40" />
            <div class="leading-tight">
                <p class="text-sm font-bold text-slate-900">{{ config('app.name', 'Bagimu Extra') }}</p>
                <p class="text-[11px] text-slate-500">{{ \App\Models\AppSetting::get('school.name', 'Admin Panel') }}</p>
            </div>
        </div>

        <nav id="staff-sidebar-nav" class="min-h-0 flex-1 overflow-y-auto px-3 py-3">
            @include('layouts.partials.staff-nav')
        </nav>

        <div class="border-t border-slate-200 p-3">
            <div class="flex items-center gap-3 rounded-xl px-2 py-2">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-800">
                    {{ strtoupper(substr($user?->name ?? 'A', 0, 1)) }}
                </span>
                <div class="min-w-0 leading-tight">
                    <p class="truncate text-sm font-medium text-slate-800">{{ $user?->name }}</p>
                    <p class="truncate text-[11px] text-slate-400">{{ $user?->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="mt-2 flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-500 hover:bg-rose-50 hover:text-rose-600">
                    <x-icon name="logout" class="h-4 w-4" />
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 min-w-0 lg:ps-64">
        {{-- Topbar mobile --}}
        <header class="no-print sticky top-0 z-30 flex h-14 items-center justify-between border-b border-slate-200 bg-white px-4 lg:hidden">
            <div class="flex items-center gap-2">
                <x-school-logo :size="32" />
                <span class="text-sm font-bold text-slate-900">{{ config('app.name', 'Bagimu Extra') }}</span>
            </div>
            <details class="relative">
                <summary class="flex h-9 w-9 list-none items-center justify-center rounded-lg text-slate-600 hover:bg-slate-100 [&::-webkit-details-marker]:hidden" aria-label="Menu">
                    <x-icon name="menu" class="h-5 w-5" />
                </summary>
                <nav class="absolute right-0 mt-2 max-h-[75vh] w-64 overflow-y-auto rounded-2xl border border-slate-200 bg-white p-3 shadow-xl">
                    @include('layouts.partials.staff-nav')
                </nav>
            </details>
        </header>

        <main class="w-full p-4 lg:p-8">
            @include('layouts.partials.flash-messages')
            @yield('content')
        </main>
    </div>
</div>
<script>
(function() {
    const nav = document.getElementById('staff-sidebar-nav');
    if (!nav) return;

    // 1. Pulihkan posisi scroll yang tersimpan
    const savedPos = sessionStorage.getItem('bagimu_staff_sidebar_scroll');
    if (savedPos !== null) {
        nav.scrollTop = parseInt(savedPos, 10);
    }

    // 2. Pastikan menu aktif terlihat
    const activeLink = nav.querySelector('a.bg-blue-700');
    if (activeLink) {
        const navRect = nav.getBoundingClientRect();
        const linkRect = activeLink.getBoundingClientRect();
        if (linkRect.top < navRect.top || linkRect.bottom > navRect.bottom) {
            activeLink.scrollIntoView({ block: 'nearest', behavior: 'auto' });
        }
    }

    // 3. Simpan posisi scroll saat di-scroll
    nav.addEventListener('scroll', function() {
        sessionStorage.setItem('bagimu_staff_sidebar_scroll', nav.scrollTop);
    }, { passive: true });
})();
</script>
</body>
</html>

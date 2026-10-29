<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel') — {{ config('app.name', 'Bagimu Extra') }}</title>
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
@php
    $user = auth()->user();
    $isAdminOnly = $user && $user->hasRole(\App\Enums\Role::ADMIN);
    $isAdmin = $user && $user->hasRole(\App\Enums\Role::ADMIN, \App\Enums\Role::OPERATOR);
    $isPembina = $user && $user->hasRole(\App\Enums\Role::PEMBINA);
    $isKepsek = $user && $user->hasRole(\App\Enums\Role::KEPALA_SEKOLAH);
    $isWaliKelas = $user && $user->hasRole(\App\Enums\Role::WALI_KELAS);
    $link = fn ($active) => 'flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-bold transition-all duration-150 '
        .($active ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-600 hover:bg-slate-100/90 hover:text-slate-900 active:scale-[0.99]');
@endphp
<div class="min-h-screen lg:flex bg-slate-50">
    {{-- Sidebar desktop --}}
    <aside class="no-print fixed inset-y-0 left-0 z-30 hidden w-64 flex-col border-r border-slate-200/80 bg-white/95 backdrop-blur-sm lg:flex">
        <div class="flex items-center gap-3 border-b border-slate-100/90 px-5 py-5">
            <x-school-logo :size="38" />
            <div class="leading-tight min-w-0">
                <p class="text-sm font-extrabold text-slate-900 tracking-tight truncate">{{ config('app.name', 'Bagimu Extra') }}</p>
                <p class="text-[11px] text-slate-400 font-medium truncate">{{ \App\Models\AppSetting::get('school.name', 'Panel Guru / Staf') }}</p>
            </div>
        </div>

        <nav id="staff-sidebar-nav" class="min-h-0 flex-1 overflow-y-auto px-3.5 py-4">
            @include('layouts.partials.staff-nav')
        </nav>

        <div class="border-t border-slate-100 p-3.5">
            <div class="flex items-center gap-3 rounded-2xl border border-slate-200/70 bg-slate-50/80 p-2.5 shadow-2xs">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-xs font-black text-white shadow-xs">
                    {{ strtoupper(substr($user?->name ?? 'A', 0, 1)) }}
                </span>
                <div class="min-w-0 flex-1 leading-tight">
                    <p class="truncate text-xs font-bold text-slate-900">{{ $user?->name }}</p>
                    <p class="truncate text-[10px] text-slate-400 capitalize font-medium">{{ str_replace('_', ' ', $user?->role?->value ?? ($user?->role ?? 'Staf')) }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-all cursor-pointer">
                    <x-icon name="logout" class="h-3.5 w-3.5" />
                    Keluar Sesi
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 min-w-0 lg:ps-64">
        {{-- Topbar mobile --}}
        <header class="no-print sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200/80 bg-white/90 backdrop-blur-md px-4 sm:px-6 lg:hidden">
            <div class="flex items-center gap-2.5">
                <x-school-logo :size="32" />
                <span class="text-sm font-extrabold text-slate-900 tracking-tight">{{ config('app.name', 'Bagimu Extra') }}</span>
            </div>
            <x-popover label="Menu navigasi" panelClass="w-64 max-h-[75vh] overflow-y-auto">
                <x-slot:trigger>
                    <button type="button" class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition-colors" aria-label="Menu navigasi" aria-haspopup="menu">
                        <x-icon name="menu" class="h-5 w-5" />
                    </button>
                </x-slot:trigger>
                @include('layouts.partials.staff-nav')
            </x-popover>
        </header>

        <main id="konten-utama" class="w-full p-4 sm:p-6 lg:p-8">
            @include('layouts.partials.flash-messages')
            @yield('content')
        </main>
    </div>
</div>

{{-- Global Alpine.js Confirm Modal --}}
<div x-data="{
    open: false,
    title: '',
    message: '',
    confirmText: 'Ya, Lanjutkan',
    targetForm: null,
    showConfirm(title, message, confirmText, form) {
        this.title = title || 'Konfirmasi Tindakan';
        this.message = message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
        this.confirmText = confirmText || 'Ya, Lanjutkan';
        this.targetForm = form;
        this.open = true;
    },
    doConfirm() {
        if (this.targetForm) {
            this.targetForm.removeAttribute('data-confirm');
            this.targetForm.submit();
        }
        this.open = false;
    }
}"
@app-confirm.window="showConfirm($event.detail.title, $event.detail.message, $event.detail.confirmText, $event.detail.form)"
x-show="open"
x-cloak
class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs"
style="display: none;"
@keydown.escape.window="open = false">
    <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl border border-slate-200" @click.outside="open = false">
        <div class="flex items-start gap-3.5 mb-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </span>
            <div>
                <h3 class="font-bold text-slate-900 text-sm" x-text="title">Konfirmasi Tindakan</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed" x-text="message"></p>
            </div>
        </div>
        <div class="mt-5 flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
            <button type="button" @click="open = false" class="rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                Batal
            </button>
            <button type="button" @click="doConfirm()" class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white hover:bg-rose-700 shadow-sm transition cursor-pointer">
                <span x-text="confirmText">Ya, Lanjutkan</span>
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    // Intercept form with data-confirm
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (form.hasAttribute('data-confirm')) {
            e.preventDefault();
            window.dispatchEvent(new CustomEvent('app-confirm', {
                detail: {
                    title: form.getAttribute('data-confirm-title') || 'Konfirmasi Tindakan',
                    message: form.getAttribute('data-confirm'),
                    confirmText: form.getAttribute('data-confirm-btn') || 'Ya, Lanjutkan',
                    form: form
                }
            }));
        }
    });

    const nav = document.getElementById('staff-sidebar-nav');
    if (!nav) return;

    // 1. Pulihkan posisi scroll yang tersimpan
    const savedPos = sessionStorage.getItem('bagimu_staff_sidebar_scroll');
    if (savedPos !== null) {
        nav.scrollTop = parseInt(savedPos, 10);
    }

    // 2. Pastikan menu aktif terlihat
    const activeLink = nav.querySelector('a.bg-blue-600, a.bg-blue-700');
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

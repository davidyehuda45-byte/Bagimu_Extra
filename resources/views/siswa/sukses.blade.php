@extends('layouts.siswa')

@section('title', 'Pendaftaran Berhasil')

@section('content')
<div class="space-y-6 w-full max-w-xl mx-auto py-4 sm:py-8" x-data="{
    countdown: 5,
    timer: null,
    init() {
        this.timer = setInterval(() => {
            if (this.countdown > 1) {
                this.countdown--;
            } else {
                clearInterval(this.timer);
                document.getElementById('logout-form').submit();
            }
        }, 1000);
    }
}">
    <div class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-10 shadow-lg text-center space-y-6">
        {{-- Success Checkmark Icon --}}
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-emerald-100 text-emerald-600 shadow-inner">
            <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12" />
            </svg>
        </div>

        <div>
            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20 mb-2">
                Pendaftaran Selesai & Terverifikasi
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Pendaftaran Berhasil!</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-2">Data Anda resmi terdaftar pada kegiatan ekstrakurikuler pilihan.</p>
        </div>

        {{-- Ringkasan Pendaftaran --}}
        <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-5 text-left space-y-3 text-xs sm:text-sm">
            <div class="flex justify-between items-center pb-2 border-b border-slate-200/60">
                <span class="text-slate-500">Nama Lengkap</span>
                <span class="font-bold text-slate-900">{{ $user->name }}</span>
            </div>
            <div class="flex justify-between items-center pb-2 border-b border-slate-200/60">
                <span class="text-slate-500">NIS & Kelas</span>
                <span class="font-semibold text-slate-800">{{ $user->student?->nis ?? $user->username }} • {{ $registration?->kelas ?? $user->student?->kelas }}</span>
            </div>
            <div class="flex justify-between items-center pb-2 border-b border-slate-200/60">
                <span class="text-slate-500">Eskul Terpilih</span>
                <span class="font-black text-blue-700 text-sm sm:text-base">{{ $registration?->eskul_utama ?? session('eskul', 'Terdaftar') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-slate-500">Status Akun</span>
                <span class="inline-flex items-center gap-1 font-bold text-emerald-700">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Terkunci (Final)
                </span>
            </div>
        </div>

        {{-- Countdown Auto Logout --}}
        <div class="rounded-2xl border border-blue-200 bg-blue-50/60 p-4 text-center space-y-2">
            <p class="text-xs font-semibold text-blue-900">
                Token login telah dihanguskan demi keamanan. Sistem akan otomatis keluar dalam:
            </p>
            <div class="flex items-center justify-center gap-1.5 font-black text-blue-700 text-2xl font-mono">
                <span x-text="countdown">5</span>
                <span class="text-xs uppercase font-sans font-bold text-blue-600">detik</span>
            </div>
        </div>

        <form id="logout-form" method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full rounded-2xl border border-slate-300 bg-white px-5 py-3 text-xs sm:text-sm font-bold text-slate-700 hover:bg-slate-50 transition shadow-xs">
                Keluar Sekarang (Logout)
            </button>
        </form>
    </div>
</div>
@endsection

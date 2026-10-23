@extends('layouts.siswa')

@section('title', 'Pendaftaran Berhasil — Kartu Peserta')

@section('content')
<style>
    .card-pelajar-container {
        width: 8.5cm;
        height: 5.5cm;
        min-width: 8.5cm;
        min-height: 5.5cm;
    }
    @media print {
        @page {
            size: auto;
            margin: 10mm;
        }
        body {
            background: white !important;
        }
        .no-print, header, nav, footer {
            display: none !important;
        }
        .print-area {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            background: transparent !important;
        }
        .card-pelajar-container {
            width: 8.5cm !important;
            height: 5.5cm !important;
            border: 1.5px solid #1e3a8a !important;
            box-shadow: none !important;
            page-break-inside: avoid !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>

<div class="space-y-6 w-full max-w-xl mx-auto py-4 sm:py-6" x-data="{
    countdown: 30,
    paused: false,
    timer: null,
    init() {
        this.timer = setInterval(() => {
            if (!this.paused) {
                if (this.countdown > 1) {
                    this.countdown--;
                } else {
                    clearInterval(this.timer);
                    document.getElementById('logout-form').submit();
                }
            }
        }, 1000);
    },
    togglePause() {
        this.paused = !this.paused;
    }
}">
    <div class="print-area rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xl text-center space-y-6">
        {{-- Success Banner (Disembunyikan saat cetak) --}}
        <div class="no-print space-y-2">
            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                <x-icon name="check" class="h-3.5 w-3.5" />
                Pendaftaran Berhasil & Terverifikasi
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Kartu Tanda Peserta Ekstrakurikuler</h1>
            <p class="text-xs text-slate-500">Simpan atau cetak kartu ini sebagai bukti resmi keikutsertaan kegiatan eskul.</p>
        </div>

        {{-- KARTU PELAJAR / KARTU PESERTA (UKURAN FISIK PRESISI 8.5 cm x 5.5 cm) --}}
        <div class="flex justify-center overflow-x-auto py-1">
            <div class="card-pelajar-container relative rounded-2xl border-2 border-blue-900/30 bg-white shadow-xl overflow-hidden flex flex-col justify-between text-left select-none">
                {{-- Header Kartu (Biru Dongker / Design System) --}}
                <div class="bg-gradient-to-r from-blue-950 via-blue-900 to-indigo-950 text-white px-3 py-1.5 flex items-center justify-between border-b border-blue-800 shrink-0">
                    <div class="flex items-center gap-2 min-w-0">
                        <x-school-logo :size="22" class="h-5 w-5 shrink-0 rounded-full bg-white p-0.5 shadow-2xs" />
                        <div class="leading-none min-w-0">
                            <h3 class="font-black text-[9px] uppercase tracking-wide truncate text-white">{{ $schoolName }}</h3>
                            <p class="text-[6.5px] font-semibold text-blue-200 uppercase tracking-widest mt-0.5">KARTU PESERTA EKSTRAKURIKULER</p>
                        </div>
                    </div>
                    <span class="rounded bg-blue-800/90 border border-blue-400/30 px-1.5 py-0.5 text-[7px] font-bold text-blue-100 tracking-wider shrink-0">
                        TA {{ $tahunAjaran }}
                    </span>
                </div>

                {{-- Badan Kartu --}}
                <div class="px-3 py-2 flex items-center gap-3 bg-gradient-to-b from-blue-50/30 via-white to-slate-50/50 flex-1 relative min-h-0">
                    {{-- Watermark Logo Sekolah --}}
                    <div class="absolute inset-0 flex items-center justify-center opacity-[0.035] pointer-events-none">
                        <x-school-logo :size="90" />
                    </div>

                    {{-- Foto / Avatar Siswa --}}
                    <div class="flex flex-col items-center justify-center shrink-0 z-10">
                        <div class="h-[2.1cm] w-[1.55cm] rounded-lg border border-blue-300 bg-gradient-to-b from-blue-100 via-blue-50 to-blue-200 flex flex-col items-center justify-center shadow-inner overflow-hidden relative">
                            <span class="text-lg font-black text-blue-800">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            <span class="text-[6.5px] font-bold text-blue-600 uppercase tracking-tighter mt-0.5">FOTO</span>
                            <div class="absolute bottom-0 inset-x-0 bg-blue-900/90 text-[5.5px] text-white text-center py-0.5 font-bold uppercase tracking-wider">
                                SISWA
                            </div>
                        </div>
                    </div>

                    {{-- Data Isian Kartu Pelajar --}}
                    <div class="flex-1 min-w-0 space-y-1 text-[8px] leading-tight z-10">
                        <div class="grid grid-cols-[56px_1fr] items-baseline">
                            <span class="font-semibold text-slate-500 uppercase tracking-wider text-[7px]">Nama</span>
                            <span class="font-black text-slate-900 text-[8.5px] truncate">: {{ $user->name }}</span>
                        </div>
                        <div class="grid grid-cols-[56px_1fr] items-baseline">
                            <span class="font-semibold text-slate-500 uppercase tracking-wider text-[7px]">NIS</span>
                            <span class="font-bold text-slate-800">: {{ $user->student?->nis ?? ($registration?->nis ?? $user->username) }}</span>
                        </div>
                        <div class="grid grid-cols-[56px_1fr] items-baseline">
                            <span class="font-semibold text-slate-500 uppercase tracking-wider text-[7px]">Jenis Eskul</span>
                            <span class="font-black text-blue-700 text-[9px] truncate">: {{ $registration?->eskul_utama ?? session('eskul', 'Terdaftar') }}</span>
                        </div>
                        <div class="grid grid-cols-[56px_1fr] items-baseline">
                            <span class="font-semibold text-slate-500 uppercase tracking-wider text-[7px]">Tahun Ajaran</span>
                            <span class="font-bold text-slate-800">: {{ $tahunAjaran }}</span>
                        </div>
                        <div class="grid grid-cols-[56px_1fr] items-baseline">
                            <span class="font-semibold text-slate-500 uppercase tracking-wider text-[7px]">Berlaku s/d</span>
                            <span class="font-extrabold text-emerald-700">: {{ $berlakuSampai }}</span>
                        </div>
                    </div>
                </div>

                {{-- Footer Kartu --}}
                <div class="bg-slate-50 border-t border-blue-100 px-3 py-1 flex items-center justify-between text-[6.5px] text-slate-500 shrink-0">
                    <span class="inline-flex items-center gap-1 font-bold text-emerald-700">
                        <x-icon name="check" class="h-2.5 w-2.5" />
                        TERDAFTAR RESMI
                    </span>
                    <span class="font-mono text-slate-400">
                        ID: {{ substr($registration?->verify_token ?? md5($user->id . $tahunAjaran), 0, 10) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Countdown Auto Logout (No-Print) --}}
        <div class="no-print rounded-2xl border border-blue-200 bg-blue-50/60 p-4 text-center space-y-2">
            <p class="text-xs font-semibold text-blue-900">
                Token login telah dihanguskan demi keamanan. Sistem akan otomatis keluar dalam:
            </p>
            <div class="flex items-center justify-center gap-1.5 font-black text-blue-700 text-2xl font-mono">
                <span x-text="countdown">30</span>
                <span class="text-xs uppercase font-sans font-bold text-blue-600">detik</span>
                <template x-if="paused">
                    <span class="ms-2 rounded-md bg-amber-200 px-2 py-0.5 text-[11px] font-bold text-amber-800 uppercase font-sans">Dijeda</span>
                </template>
            </div>
        </div>

        {{-- Action Buttons (No-Print) --}}
        <div class="no-print space-y-2 pt-1">
            <div class="grid grid-cols-2 gap-2">
                <button type="button" @click="togglePause()" class="w-full inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-2xs">
                    <template x-if="!paused">
                        <span class="inline-flex items-center gap-1.5">
                            <x-icon name="clock" class="h-3.5 w-3.5 text-slate-500" />
                            Jeda Hitung Mundur
                        </span>
                    </template>
                    <template x-if="paused">
                        <span class="inline-flex items-center gap-1.5 text-blue-700 font-bold">
                            <x-icon name="clock" class="h-3.5 w-3.5 text-blue-600" />
                            Lanjutkan Hitung Mundur
                        </span>
                    </template>
                </button>
                <button type="button" onclick="window.print()" class="w-full inline-flex items-center justify-center gap-1.5 rounded-xl border border-blue-600 bg-blue-600 px-3.5 py-2.5 text-xs font-bold text-white hover:bg-blue-700 transition shadow-xs cursor-pointer">
                    <x-icon name="printer" class="h-3.5 w-3.5 text-white" />
                    Cetak Kartu (8,5 x 5,5 cm)
                </button>
            </div>

            <form id="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full rounded-xl bg-slate-900 px-5 py-2.5 text-xs sm:text-sm font-bold text-white hover:bg-slate-800 transition shadow-xs cursor-pointer">
                    Keluar Sesi Sekarang
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

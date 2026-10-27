@extends('layouts.siswa')

@section('title', 'Pendaftaran Berhasil — Kartu Peserta')

@section('content')
<style>
    /* Standar fisik kartu: 85.6mm x 54mm (ID-1 / CR80) */
    .id-card-wrapper {
        display: flex;
        justify-content: center;
        padding: 4px 0;
    }
    .id-card {
        width: 85.6mm;
        height: 54mm;
        min-width: 85.6mm;
        min-height: 54mm;
        max-width: 85.6mm;
        max-height: 54mm;
        box-sizing: border-box;
        background: #ffffff;
        border: 1.5px solid #1e3a8a;
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 4px 6px -4px rgba(15, 23, 42, 0.08);
        text-align: left;
        user-select: none;
    }
    .id-card-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
        color: #ffffff;
        padding: 5px 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #172554;
        flex-shrink: 0;
    }
    .id-card-header-left {
        display: flex;
        align-items: center;
        gap: 6px;
        min-width: 0;
    }
    .id-card-logo-circle {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #ffffff;
        padding: 1.5px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
    }
    .id-card-header-text {
        min-width: 0;
        line-height: 1.1;
    }
    .id-card-header-title {
        font-size: 8px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.2px;
        color: #ffffff;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 175px;
    }
    .id-card-header-sub {
        font-size: 6px;
        font-weight: 700;
        color: #93c5fd;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 1px;
    }
    .id-card-badge {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 3px;
        padding: 1.5px 5px;
        font-size: 6.5px;
        font-weight: 700;
        color: #f8fafc;
        letter-spacing: 0.3px;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .id-card-body {
        padding: 5px 8px;
        display: flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 60%, #f1f5f9 100%);
        flex: 1;
        position: relative;
        overflow: hidden;
    }
    .id-card-watermark {
        position: absolute;
        right: 4px;
        bottom: 2px;
        width: 60px;
        height: 60px;
        opacity: 0.05;
        pointer-events: none;
        z-index: 1;
        object-fit: contain;
    }
    .id-card-photo {
        width: 44px;
        height: 58px;
        flex-shrink: 0;
        border-radius: 4px;
        border: 1px solid #93c5fd;
        background: linear-gradient(180deg, #dbeafe 0%, #eff6ff 50%, #bfdbfe 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.06);
        z-index: 2;
    }
    .id-card-photo-initial {
        font-size: 18px;
        font-weight: 900;
        color: #1e3a8a;
        line-height: 1;
    }
    .id-card-photo-tag {
        font-size: 5.5px;
        font-weight: 700;
        color: #2563eb;
        letter-spacing: 0.5px;
        margin-top: 1px;
    }
    .id-card-photo-banner {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: #1e3a8a;
        color: #ffffff;
        font-size: 5px;
        font-weight: 800;
        text-align: center;
        padding: 1px 0;
        letter-spacing: 0.5px;
    }
    .id-card-info {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 2px;
        z-index: 2;
    }
    .id-card-row {
        display: flex;
        align-items: baseline;
        font-size: 7px;
        line-height: 1.2;
    }
    .id-card-label {
        width: 52px;
        flex-shrink: 0;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        font-size: 6.2px;
        letter-spacing: 0.2px;
    }
    .id-card-sep {
        width: 6px;
        flex-shrink: 0;
        color: #94a3b8;
        font-weight: 700;
    }
    .id-card-value {
        flex: 1;
        min-width: 0;
        font-weight: 700;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .id-card-value-primary {
        font-weight: 800;
        color: #1d4ed8;
        font-size: 7.5px;
    }
    .id-card-footer {
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        padding: 3px 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 6px;
        flex-shrink: 0;
        z-index: 2;
    }
    .id-card-status {
        color: #047857;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 3px;
        letter-spacing: 0.3px;
    }
    .id-card-token {
        color: #64748b;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 6px;
        font-weight: 600;
    }
    @media print {
        @page {
            size: auto;
            margin: 10mm;
        }
        body {
            background: #ffffff !important;
            color: #000000 !important;
        }
        .no-print, header, nav, footer, .skip-link {
            display: none !important;
        }
        .print-area {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            background: transparent !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
        }
        .id-card-wrapper {
            padding: 0 !important;
            margin: 20mm auto !important;
        }
        .id-card {
            border: 1.5px solid #1e3a8a !important;
            box-shadow: none !important;
            page-break-inside: avoid !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>

<div class="space-y-6 w-full max-w-xl mx-auto py-4 sm:py-6" x-data="{
    countdown: 15,
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
    <div class="print-area rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xl text-center space-y-6">
        {{-- Success Banner (Disembunyikan saat cetak) --}}
        <div class="no-print space-y-2">
            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                <x-icon name="check" class="h-3.5 w-3.5" />
                Pendaftaran Berhasil / Terverifikasi
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Kartu Tanda Peserta Ekstrakurikuler</h1>
            <p class="text-xs text-slate-500">Simpan atau tangkap layar (screenshot) kartu ini dalam 15 detik sebagai bukti resmi keikutsertaan kegiatan eskul.</p>
        </div>

        {{-- KARTU PELAJAR / KARTU PESERTA (UKURAN FISIK PRESISI 8.5 cm x 5.4 cm) --}}
        <div class="id-card-wrapper">
            <div class="id-card">
                {{-- Header Kartu --}}
                <div class="id-card-header">
                    <div class="id-card-header-left">
                        <span class="id-card-logo-circle">
                            <img src="/logo.png" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; display: none;" onload="this.style.display='block'; if(this.nextElementSibling) this.nextElementSibling.style.display='none';" onerror="this.style.display='none';">
                            <svg style="width: 14px; height: 14px; color: #1e3a8a;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"/><path d="M22 10v6"/><path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"/></svg>
                        </span>
                        <div class="id-card-header-text">
                            <h3 class="id-card-header-title">{{ $schoolName }}</h3>
                            <p class="id-card-header-sub">KARTU PESERTA EKSTRAKURIKULER</p>
                        </div>
                    </div>
                    <span class="id-card-badge">
                        TA {{ $tahunAjaran }}
                    </span>
                </div>

                {{-- Badan Kartu --}}
                <div class="id-card-body">
                    {{-- Watermark Logo Sekolah (Transparan, tidak menghalangi teks) --}}
                    <img src="/logo.png" alt="" class="id-card-watermark" onerror="this.style.display='none';">

                    {{-- Foto Siswa / Avatar --}}
                    <div class="id-card-photo">
                        <span class="id-card-photo-initial">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        <span class="id-card-photo-tag">FOTO</span>
                        <div class="id-card-photo-banner">SISWA</div>
                    </div>

                    {{-- Data Isian Kartu --}}
                    <div class="id-card-info">
                        <div class="id-card-row">
                            <span class="id-card-label">Nama</span>
                            <span class="id-card-sep">:</span>
                            <span class="id-card-value" style="font-weight: 800;">{{ $user->name }}</span>
                        </div>
                        <div class="id-card-row">
                            <span class="id-card-label">NIS</span>
                            <span class="id-card-sep">:</span>
                            <span class="id-card-value">{{ $user->student?->nis ?? ($registration?->nis ?? $user->username) }}</span>
                        </div>
                        @if ($registration?->kelas ?? $user->student?->kelas)
                            <div class="id-card-row">
                                <span class="id-card-label">Kelas</span>
                                <span class="id-card-sep">:</span>
                                <span class="id-card-value">{{ $registration?->kelas ?? $user->student?->kelas }}</span>
                            </div>
                        @endif
                        <div class="id-card-row">
                            <span class="id-card-label">Eskul</span>
                            <span class="id-card-sep">:</span>
                            <span class="id-card-value id-card-value-primary">{{ $registration?->eskul_utama ?? session('eskul', 'Terdaftar') }}</span>
                        </div>
                        <div class="id-card-row">
                            <span class="id-card-label">Thn Ajaran</span>
                            <span class="id-card-sep">:</span>
                            <span class="id-card-value">
                                {{ $tahunAjaran }} <span style="font-style: italic; font-weight: normal; color: #64748b; font-size: 6px;">(Kode: {{ $periode?->periode ?? ($registration?->periode ?? $tahunAjaran) }})</span>
                            </span>
                        </div>
                        <div class="id-card-row">
                            <span class="id-card-label">Berlaku s/d</span>
                            <span class="id-card-sep">:</span>
                            <span class="id-card-value" style="color: #047857; font-weight: 800;">{{ $berlakuSampai }}</span>
                        </div>
                    </div>
                </div>

                {{-- Footer Kartu --}}
                <div class="id-card-footer">
                    <span class="id-card-status">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle;"><polyline points="20 6 9 17 4 12"/></svg>
                        TERDAFTAR RESMI
                    </span>
                    <span class="id-card-token">
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
                <span x-text="countdown">15</span>
                <span class="text-xs uppercase font-sans font-bold text-blue-600">detik</span>
            </div>
            <p class="text-[11px] font-semibold text-blue-800">
                Segera simpan atau screenshot kartu bukti di atas sebelum waktu habis.
            </p>
        </div>

        {{-- Action Buttons (No-Print) --}}
        <div class="no-print space-y-2 pt-1">
            <button type="button" onclick="window.print()" class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-blue-600 bg-blue-600 px-4 py-2.5 text-xs sm:text-sm font-bold text-white hover:bg-blue-700 transition shadow-xs cursor-pointer">
                <x-icon name="printer" class="h-4 w-4 text-white" />
                <span>Cetak / Simpan Kartu (8,5 x 5,4 cm)</span>
            </button>

            <form id="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full rounded-xl bg-slate-900 px-5 py-2.5 text-xs sm:text-sm font-bold text-white hover:bg-slate-800 transition shadow-xs cursor-pointer">
                    Keluar Sekarang
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

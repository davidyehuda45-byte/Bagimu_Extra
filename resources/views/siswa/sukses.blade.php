@extends('layouts.siswa')

@section('title', 'Pendaftaran Berhasil — Kartu Peserta')

@section('content')
<style>
    /* Standar fisik kartu internasional ID-1 / CR80 */
    .id-card-wrapper {
        display: flex;
        justify-content: center;
        padding: 4px 0;
    }

    /* KARTU HORIZONTAL (85.6mm x 54mm) */
    .id-card-horizontal {
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

    /* KARTU VERTIKAL / TEGAK (54mm x 85.6mm) */
    .id-card-vertical {
        width: 54mm;
        height: 85.6mm;
        min-width: 54mm;
        min-height: 85.6mm;
        max-width: 54mm;
        max-height: 85.6mm;
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
        padding: 4px 7px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #172554;
        flex-shrink: 0;
    }
    .id-card-header-left {
        display: flex;
        align-items: center;
        gap: 5px;
        min-width: 0;
    }
    .id-card-logo-circle {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #ffffff;
        padding: 1px;
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
        font-size: 7.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.2px;
        color: #ffffff;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .id-card-header-sub {
        font-size: 5.5px;
        font-weight: 700;
        color: #93c5fd;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-top: 1px;
    }
    .id-card-badge {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 3px;
        padding: 1px 4px;
        font-size: 6px;
        font-weight: 700;
        color: #f8fafc;
        letter-spacing: 0.3px;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .id-card-body-horizontal {
        padding: 4px 6px;
        display: flex;
        align-items: center;
        gap: 6px;
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 60%, #f1f5f9 100%);
        flex: 1;
        position: relative;
        overflow: hidden;
    }

    .id-card-body-vertical {
        padding: 5px 6px;
        display: flex;
        flex-direction: column;
        gap: 5px;
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 60%, #f1f5f9 100%);
        flex: 1;
        position: relative;
        overflow: hidden;
    }

    .id-card-watermark {
        position: absolute;
        right: 4px;
        bottom: 2px;
        width: 55px;
        height: 55px;
        opacity: 0.05;
        pointer-events: none;
        z-index: 1;
        object-fit: contain;
    }

    .id-card-photo {
        width: 40px;
        height: 52px;
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
        font-size: 16px;
        font-weight: 900;
        color: #1e3a8a;
        line-height: 1;
    }
    .id-card-photo-tag {
        font-size: 5px;
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
        font-size: 4.8px;
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
        font-size: 6.5px;
        line-height: 1.2;
    }
    .id-card-label {
        width: 46px;
        flex-shrink: 0;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        font-size: 5.8px;
        letter-spacing: 0.2px;
    }
    .id-card-sep {
        width: 5px;
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
        font-size: 7px;
    }

    /* Kotak QR Code di Kartu */
    .id-card-qr-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        padding: 2px;
        flex-shrink: 0;
        z-index: 2;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .id-card-qr-box svg, .id-card-qr-box img {
        width: 44px;
        height: 44px;
        display: block;
    }
    .id-card-qr-tag {
        font-size: 4.5px;
        font-weight: 800;
        color: #1e3a8a;
        letter-spacing: 0.3px;
        margin-top: 1.5px;
        text-transform: uppercase;
    }

    .id-card-footer {
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        padding: 3px 7px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 5.8px;
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
        font-size: 5.8px;
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
            margin: 10mm auto !important;
        }
        .id-card-horizontal, .id-card-vertical {
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
    orientasi: 'horizontal',
    downloadingPng: false,
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
    <div class="print-area rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xl text-center space-y-5">
        {{-- Success Banner (Disembunyikan saat cetak) --}}
        <div class="no-print space-y-2">
            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                <x-icon name="check" class="h-3.5 w-3.5" />
                Pendaftaran Berhasil / Terverifikasi
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Kartu Tanda Peserta Ekstrakurikuler</h1>
            <p class="text-xs text-slate-500">Simpan kartu ini dalam format PNG atau PDF sebagai bukti resmi keikutsertaan kegiatan eskul.</p>
        </div>

        {{-- Switcher Orientasi Kartu (No-Print) --}}
        <div class="no-print flex items-center justify-center gap-1.5 pt-1">
            <div class="inline-flex rounded-xl border border-slate-200 bg-slate-100 p-1 shadow-2xs">
                <button type="button" @click="orientasi = 'horizontal'"
                        :class="orientasi === 'horizontal' ? 'bg-white text-blue-900 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                        class="px-3.5 py-1.5 rounded-lg text-xs transition-all cursor-pointer">
                    Horizontal (8,5 x 5,4 cm)
                </button>
                <button type="button" @click="orientasi = 'vertical'"
                        :class="orientasi === 'vertical' ? 'bg-white text-blue-900 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                        class="px-3.5 py-1.5 rounded-lg text-xs transition-all cursor-pointer">
                    Vertikal / Tegak (5,4 x 8,5 cm)
                </button>
            </div>
        </div>

        @php
            $cardLogo = \App\Models\AppSetting::get('school.logo');
            $cardLogoUrl = $cardLogo ? (str_starts_with($cardLogo, 'http') ? $cardLogo : asset(ltrim($cardLogo, '/'))) : asset('logo.png');
            $nisValue = $user->student?->nis ?? ($registration?->nis ?? $user->username);
            $namaValue = $user->name;
            $kelasValue = $registration?->kelas ?? ($user->student?->kelas ?? '-');
            $eskulValue = $registration?->eskul_utama ?? session('eskul', 'Terdaftar');
            $tokenId = substr($registration?->verify_token ?? md5($user->id . $tahunAjaran), 0, 10);
        @endphp

        {{-- KARTU VERSI HORIZONTAL (85.6mm x 54mm) --}}
        <div class="id-card-wrapper" x-show="orientasi === 'horizontal'" id="card-horizontal-wrapper">
            <div class="id-card-horizontal" id="card-element-horizontal">
                {{-- Header Kartu --}}
                <div class="id-card-header">
                    <div class="id-card-header-left">
                        <span class="id-card-logo-circle">
                            <img src="{{ $cardLogoUrl }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; display: none;" onload="this.style.display='block'; if(this.nextElementSibling) this.nextElementSibling.style.display='none';" onerror="this.style.display='none';">
                            <svg style="width: 13px; height: 13px; color: #1e3a8a;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"/><path d="M22 10v6"/><path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"/></svg>
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

                {{-- Badan Kartu Horizontal --}}
                <div class="id-card-body-horizontal">
                    <img src="{{ $cardLogoUrl }}" alt="" class="id-card-watermark" onerror="this.style.display='none';">

                    {{-- Foto Avatar Siswa --}}
                    <div class="id-card-photo">
                        <span class="id-card-photo-initial">{{ strtoupper(substr($namaValue, 0, 1)) }}</span>
                        <span class="id-card-photo-tag">FOTO</span>
                        <div class="id-card-photo-banner">SISWA</div>
                    </div>

                    {{-- Data Isian Siswa --}}
                    <div class="id-card-info">
                        <div class="id-card-row">
                            <span class="id-card-label">Nama</span>
                            <span class="id-card-sep">:</span>
                            <span class="id-card-value" style="font-weight: 800;">{{ $namaValue }}</span>
                        </div>
                        <div class="id-card-row">
                            <span class="id-card-label">NIS</span>
                            <span class="id-card-sep">:</span>
                            <span class="id-card-value font-mono">{{ $nisValue }}</span>
                        </div>
                        <div class="id-card-row">
                            <span class="id-card-label">Kelas</span>
                            <span class="id-card-sep">:</span>
                            <span class="id-card-value">{{ $kelasValue }}</span>
                        </div>
                        <div class="id-card-row">
                            <span class="id-card-label">Eskul</span>
                            <span class="id-card-sep">:</span>
                            <span class="id-card-value id-card-value-primary">{{ $eskulValue }}</span>
                        </div>
                        <div class="id-card-row">
                            <span class="id-card-label">Thn Ajaran</span>
                            <span class="id-card-sep">:</span>
                            <span class="id-card-value">
                                {{ $tahunAjaran }} <span style="font-style: italic; font-weight: normal; color: #64748b; font-size: 5.5px;">(Kode: {{ $periode?->periode ?? $tahunAjaran }})</span>
                            </span>
                        </div>
                        <div class="id-card-row">
                            <span class="id-card-label">Berlaku s/d</span>
                            <span class="id-card-sep">:</span>
                            <span class="id-card-value" style="color: #047857; font-weight: 800;">{{ $berlakuSampai }}</span>
                        </div>
                    </div>

                    {{-- QR Code Presensi Resmi --}}
                    <div class="id-card-qr-box">
                        @if ($qrSvg)
                            {!! $qrSvg !!}
                        @else
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($qrCodeData ?? $nisValue) }}" alt="QR Presensi" style="width: 44px; height: 44px;">
                        @endif
                        <span class="id-card-qr-tag">SCAN PRESENSI</span>
                    </div>
                </div>

                {{-- Footer Kartu --}}
                <div class="id-card-footer">
                    <span class="id-card-status">
                        <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle;"><polyline points="20 6 9 17 4 12"/></svg>
                        TERDAFTAR RESMI
                    </span>
                    <span class="id-card-token">
                        ID: {{ $tokenId }}
                    </span>
                </div>
            </div>
        </div>

        {{-- KARTU VERSI VERTIKAL / TEGAK (54mm x 85.6mm) --}}
        <div class="id-card-wrapper" x-show="orientasi === 'vertical'" id="card-vertical-wrapper" style="display: none;">
            <div class="id-card-vertical" id="card-element-vertical">
                {{-- Header Kartu Vertikal --}}
                <div class="id-card-header">
                    <div class="id-card-header-left">
                        <span class="id-card-logo-circle">
                            <img src="{{ $cardLogoUrl }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; display: none;" onload="this.style.display='block'; if(this.nextElementSibling) this.nextElementSibling.style.display='none';" onerror="this.style.display='none';">
                            <svg style="width: 13px; height: 13px; color: #1e3a8a;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"/><path d="M22 10v6"/><path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"/></svg>
                        </span>
                        <div class="id-card-header-text">
                            <h3 class="id-card-header-title" style="max-width: 110px;">{{ $schoolName }}</h3>
                            <p class="id-card-header-sub">KARTU PESERTA</p>
                        </div>
                    </div>
                    <span class="id-card-badge">
                        TA {{ $tahunAjaran }}
                    </span>
                </div>

                {{-- Badan Kartu Vertikal --}}
                <div class="id-card-body-vertical">
                    <img src="{{ $cardLogoUrl }}" alt="" class="id-card-watermark" onerror="this.style.display='none';">

                    {{-- Baris Atas: Foto Siswa & Badge Eskul --}}
                    <div class="flex items-center gap-3 z-10">
                        <div class="id-card-photo" style="width: 36px; height: 46px;">
                            <span class="id-card-photo-initial" style="font-size: 14px;">{{ strtoupper(substr($namaValue, 0, 1)) }}</span>
                            <span class="id-card-photo-tag" style="font-size: 4.5px;">FOTO</span>
                            <div class="id-card-photo-banner" style="font-size: 4px;">SISWA</div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-[5.5px] font-bold text-slate-400 uppercase tracking-wider block">Ekstrakurikuler</span>
                            <span class="text-[9px] font-black text-blue-700 truncate block">{{ $eskulValue }}</span>
                            <span class="text-[6px] font-semibold text-slate-500 block mt-0.5">T.A. {{ $tahunAjaran }}</span>
                        </div>
                    </div>

                    {{-- Data Isian Siswa --}}
                    <div class="id-card-info" style="gap: 1.5px;">
                        <div class="id-card-row">
                            <span class="id-card-label" style="width: 34px;">Nama</span>
                            <span class="id-card-sep">:</span>
                            <span class="id-card-value" style="font-weight: 800;">{{ $namaValue }}</span>
                        </div>
                        <div class="id-card-row">
                            <span class="id-card-label" style="width: 34px;">NIS</span>
                            <span class="id-card-sep">:</span>
                            <span class="id-card-value font-mono">{{ $nisValue }}</span>
                        </div>
                        <div class="id-card-row">
                            <span class="id-card-label" style="width: 34px;">Kelas</span>
                            <span class="id-card-sep">:</span>
                            <span class="id-card-value">{{ $kelasValue }}</span>
                        </div>
                        <div class="id-card-row">
                            <span class="id-card-label" style="width: 34px;">Berlaku</span>
                            <span class="id-card-sep">:</span>
                            <span class="id-card-value" style="color: #047857; font-weight: 800;">{{ $berlakuSampai }}</span>
                        </div>
                    </div>

                    {{-- QR Code Presensi Resmi di Tengah/Bawah --}}
                    <div class="flex flex-col items-center justify-center pt-1 z-10">
                        <div class="id-card-qr-box" style="padding: 2.5px;">
                            @if ($qrSvg)
                                {!! $qrSvg !!}
                            @else
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($qrCodeData ?? $nisValue) }}" alt="QR Presensi" style="width: 44px; height: 44px;">
                            @endif
                            <span class="id-card-qr-tag">SCAN PRESENSI</span>
                        </div>
                    </div>
                </div>

                {{-- Footer Kartu Vertikal --}}
                <div class="id-card-footer">
                    <span class="id-card-status">
                        <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle;"><polyline points="20 6 9 17 4 12"/></svg>
                        TERDAFTAR RESMI
                    </span>
                    <span class="id-card-token">
                        ID: {{ $tokenId }}
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
                Segera unduh kartu (PNG) atau simpan (PDF) sebelum waktu habis.
            </p>
        </div>

        {{-- Action Buttons (No-Print) --}}
        <div class="no-print space-y-2 pt-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                {{-- Tombol Unduh Gambar PNG --}}
                <button type="button" onclick="unduhKartuPng()"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-600 bg-emerald-600 px-4 py-2.5 text-xs sm:text-sm font-bold text-white hover:bg-emerald-700 transition shadow-xs cursor-pointer">
                    <x-icon name="download" class="h-4 w-4 text-white" />
                    <span>Unduh Gambar (PNG)</span>
                </button>

                {{-- Tombol Cetak / Simpan PDF --}}
                <button type="button" onclick="window.print()"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-blue-600 bg-blue-600 px-4 py-2.5 text-xs sm:text-sm font-bold text-white hover:bg-blue-700 transition shadow-xs cursor-pointer">
                    <x-icon name="printer" class="h-4 w-4 text-white" />
                    <span>Cetak / Simpan PDF</span>
                </button>
            </div>

            <form id="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full rounded-xl bg-slate-900 px-5 py-2.5 text-xs sm:text-sm font-bold text-white hover:bg-slate-800 transition shadow-xs cursor-pointer">
                    Keluar Sekarang
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function unduhKartuPng() {
    const isVertical = document.querySelector('[x-data]').__x.$data.orientasi === 'vertical';
    const targetElement = isVertical ? document.getElementById('card-element-vertical') : document.getElementById('card-element-horizontal');
    const nis = '{{ $nisValue }}';

    // Buat Canvas resolusi tinggi (300 DPI)
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');

    const width = isVertical ? 638 : 1011;
    const height = isVertical ? 1011 : 638;

    canvas.width = width;
    canvas.height = height;

    // Background kartu putih dengan border navy
    ctx.fillStyle = '#ffffff';
    roundRect(ctx, 0, 0, width, height, 20);
    ctx.fill();

    // Header gradient navy
    const headerHeight = isVertical ? 110 : 85;
    const grad = ctx.createLinearGradient(0, 0, width, 0);
    grad.addColorStop(0, '#0f172a');
    grad.addColorStop(1, '#1e3a8a');
    ctx.fillStyle = grad;
    roundRectTop(ctx, 0, 0, width, headerHeight, 20);
    ctx.fill();

    // Teks Header
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 24px sans-serif';
    ctx.fillText('{{ $schoolName }}', 40, isVertical ? 50 : 45);

    ctx.fillStyle = '#93c5fd';
    ctx.font = 'bold 15px sans-serif';
    ctx.fillText(isVertical ? 'KARTU PESERTA' : 'KARTU PESERTA EKSTRAKURIKULER', 40, isVertical ? 80 : 70);

    // Badge TA Header
    ctx.fillStyle = 'rgba(255,255,255,0.2)';
    roundRect(ctx, width - 180, 25, 150, 40, 8);
    ctx.fill();
    ctx.strokeStyle = 'rgba(255,255,255,0.4)';
    ctx.stroke();
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 16px sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText('TA {{ $tahunAjaran }}', width - 105, 51);
    ctx.textAlign = 'left';

    // Badan Kartu
    if (!isVertical) {
        // HORIZONTAL
        // Avatar Siswa
        ctx.fillStyle = '#dbeafe';
        roundRect(ctx, 40, 120, 150, 200, 12);
        ctx.fill();
        ctx.strokeStyle = '#93c5fd';
        ctx.lineWidth = 3;
        ctx.stroke();

        ctx.fillStyle = '#1e3a8a';
        ctx.font = '900 60px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('{{ strtoupper(substr($namaValue, 0, 1)) }}', 115, 230);

        ctx.fillStyle = '#1e3a8a';
        ctx.fillRect(40, 285, 150, 35);
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 16px sans-serif';
        ctx.fillText('SISWA', 115, 308);
        ctx.textAlign = 'left';

        // Teks Informasi
        const startX = 220;
        let startY = 145;
        const lineSpacing = 32;

        const info = [
            ['NAMA', '{{ $namaValue }}', true],
            ['NIS', '{{ $nisValue }}', false],
            ['KELAS', '{{ $kelasValue }}', false],
            ['ESKUL', '{{ $eskulValue }}', true, '#1d4ed8'],
            ['THN AJARAN', '{{ $tahunAjaran }} (Kode: {{ $periode?->periode ?? $tahunAjaran }})', false],
            ['BERLAKU S/D', '{{ $berlakuSampai }}', true, '#047857']
        ];

        info.forEach(([label, val, bold, color]) => {
            ctx.fillStyle = '#64748b';
            ctx.font = 'bold 17px sans-serif';
            ctx.fillText(label, startX, startY);

            ctx.fillStyle = '#94a3b8';
            ctx.fillText(':', startX + 150, startY);

            ctx.fillStyle = color || (bold ? '#0f172a' : '#334155');
            ctx.font = bold ? 'bold 19px sans-serif' : '18px sans-serif';
            ctx.fillText(val, startX + 175, startY);

            startY += lineSpacing;
        });

        // QR Code Box (Kanan)
        drawQrOnCanvas(ctx, width - 210, 135, 170, 170, isVertical, nis);
    } else {
        // VERTIKAL
        // Avatar + Info Eskul
        ctx.fillStyle = '#dbeafe';
        roundRect(ctx, 40, 135, 120, 160, 12);
        ctx.fill();
        ctx.strokeStyle = '#93c5fd';
        ctx.lineWidth = 3;
        ctx.stroke();

        ctx.fillStyle = '#1e3a8a';
        ctx.font = '900 50px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('{{ strtoupper(substr($namaValue, 0, 1)) }}', 100, 225);

        ctx.fillStyle = '#1e3a8a';
        ctx.fillRect(40, 260, 120, 35);
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 14px sans-serif';
        ctx.fillText('SISWA', 100, 283);
        ctx.textAlign = 'left';

        // Eskul Info Box
        ctx.fillStyle = '#64748b';
        ctx.font = 'bold 15px sans-serif';
        ctx.fillText('EKSTRAKURIKULER', 180, 165);

        ctx.fillStyle = '#1d4ed8';
        ctx.font = 'bold 26px sans-serif';
        ctx.fillText('{{ $eskulValue }}', 180, 205);

        ctx.fillStyle = '#64748b';
        ctx.font = '16px sans-serif';
        ctx.fillText('T.A. {{ $tahunAjaran }}', 180, 240);

        // Informasi Teks Baris
        const startX = 40;
        let startY = 345;
        const lineSpacing = 36;

        const info = [
            ['NAMA', '{{ $namaValue }}', true],
            ['NIS', '{{ $nisValue }}', false],
            ['KELAS', '{{ $kelasValue }}', false],
            ['BERLAKU', '{{ $berlakuSampai }}', true, '#047857']
        ];

        info.forEach(([label, val, bold, color]) => {
            ctx.fillStyle = '#64748b';
            ctx.font = 'bold 18px sans-serif';
            ctx.fillText(label, startX, startY);

            ctx.fillStyle = '#94a3b8';
            ctx.fillText(':', startX + 110, startY);

            ctx.fillStyle = color || (bold ? '#0f172a' : '#334155');
            ctx.font = bold ? 'bold 20px sans-serif' : '18px sans-serif';
            ctx.fillText(val, startX + 135, startY);

            startY += lineSpacing;
        });

        // QR Code Box (Tengah Bawah)
        drawQrOnCanvas(ctx, (width - 250) / 2, 510, 250, 250, isVertical, nis);
    }

    // Footer
    const footerY = height - 60;
    ctx.fillStyle = '#f8fafc';
    ctx.fillRect(0, footerY, width, 60);
    ctx.fillStyle = '#e2e8f0';
    ctx.fillRect(0, footerY, width, 2);

    ctx.fillStyle = '#047857';
    ctx.font = 'bold 18px sans-serif';
    ctx.fillText('✓ TERDAFTAR RESMI', 40, footerY + 38);

    ctx.fillStyle = '#64748b';
    ctx.font = 'bold 18px monospace';
    ctx.textAlign = 'right';
    ctx.fillText('ID: {{ $tokenId }}', width - 40, footerY + 38);
    ctx.textAlign = 'left';

    // Border Luar
    ctx.strokeStyle = '#1e3a8a';
    ctx.lineWidth = 6;
    roundRect(ctx, 0, 0, width, height, 20);
    ctx.stroke();

    // Trigger download
    setTimeout(() => {
        const link = document.createElement('a');
        link.download = `Kartu_Peserta_${nis}_${isVertical ? 'Vertikal' : 'Horizontal'}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
    }, 200);
}

function drawQrOnCanvas(ctx, x, y, w, h, isVertical, nis) {
    // Background QR Box
    ctx.fillStyle = '#ffffff';
    roundRect(ctx, x, y, w, h, 10);
    ctx.fill();
    ctx.strokeStyle = '#cbd5e1';
    ctx.lineWidth = 2;
    ctx.stroke();

    // Render QR Code Image
    const qrImg = new Image();
    qrImg.crossOrigin = 'anonymous';
    qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent('{{ $qrCodeData ?? $nisValue }}')}`;
    qrImg.onload = function() {
        ctx.drawImage(qrImg, x + 10, y + 10, w - 20, h - 35);

        ctx.fillStyle = '#1e3a8a';
        ctx.font = 'bold 13px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('SCAN PRESENSI', x + (w / 2), y + h - 10);
        ctx.textAlign = 'left';
    };
    qrImg.onerror = function() {
        ctx.fillStyle = '#1e3a8a';
        ctx.font = 'bold 14px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('QR: ' + nis, x + (w / 2), y + (h / 2));
        ctx.textAlign = 'left';
    };
}

function roundRect(ctx, x, y, width, height, radius) {
    ctx.beginPath();
    ctx.moveTo(x + radius, y);
    ctx.lineTo(x + width - radius, y);
    ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
    ctx.lineTo(x + width, y + height - radius);
    ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
    ctx.lineTo(x + radius, y + height);
    ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
    ctx.lineTo(x, y + radius);
    ctx.quadraticCurveTo(x, y, x + radius, y);
    ctx.closePath();
}

function roundRectTop(ctx, x, y, width, height, radius) {
    ctx.beginPath();
    ctx.moveTo(x + radius, y);
    ctx.lineTo(x + width - radius, y);
    ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
    ctx.lineTo(x + width, y + height);
    ctx.lineTo(x, y + height);
    ctx.lineTo(x, y + radius);
    ctx.quadraticCurveTo(x, y, x + radius, y);
    ctx.closePath();
}
</script>
@endsection

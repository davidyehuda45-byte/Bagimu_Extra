<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kartu Peserta Eskul — {{ $student->nama }} ({{ $student->nis }})</title>
    <x-favicon />
    <meta name="theme-color" content="#1e3a8a">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Standar fisik kartu: 85.6mm x 54mm (ID-1 / CR80) */
        .id-card-wrapper {
            display: flex;
            justify-content: center;
            padding: 8px 0;
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
            margin: 0;
        }
        .id-card-body {
            padding: 5px 8px;
            display: flex;
            gap: 8px;
            flex-grow: 1;
            align-items: center;
            position: relative;
            background: #ffffff;
        }
        .id-card-avatar {
            width: 22mm;
            height: 28mm;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
            color: #64748b;
        }
        .id-card-details {
            flex-grow: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .id-card-name {
            font-size: 9.5px;
            font-weight: 800;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.15;
        }
        .id-card-row {
            display: flex;
            font-size: 6.5px;
            line-height: 1.25;
            color: #334155;
        }
        .id-card-label {
            width: 52px;
            font-weight: 600;
            color: #64748b;
            flex-shrink: 0;
        }
        .id-card-val {
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .id-card-eskul-box {
            margin-top: 3px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 4px;
            padding: 2.5px 5px;
        }
        .id-card-eskul-label {
            font-size: 5.5px;
            font-weight: 700;
            text-transform: uppercase;
            color: #1e40af;
            letter-spacing: 0.3px;
            line-height: 1;
        }
        .id-card-eskul-name {
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
            .no-print {
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
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white min-h-screen py-8 px-4">

<div class="max-w-xl mx-auto space-y-6">
    {{-- Bar Tombol Navigasi --}}
    <div class="no-print flex items-center justify-between gap-3">
        <a href="{{ route('siswa.login') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 transition shadow-2xs">
            <x-icon name="arrow-left" class="h-4 w-4" />
            <span>Kembali ke Halaman Login</span>
        </a>

        <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-700 px-4 py-2 text-xs font-bold text-white hover:bg-blue-800 transition shadow-xs cursor-pointer">
            <x-icon name="printer" class="h-4 w-4" />
            <span>Cetak / Simpan PDF</span>
        </button>
    </div>

    {{-- Kartu Peserta ID-1 Standar Fisik --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-md text-center space-y-6 print-area">
        <div class="no-print space-y-1">
            <h1 class="text-xl font-black text-slate-900">Kartu Resmi Peserta Ekstrakurikuler</h1>
            <p class="text-xs text-slate-500">Bukti sah keikutsertaan kegiatan ekstrakurikuler sekolah. Simpan atau cetak kartu ini.</p>
        </div>

        <div class="id-card-wrapper">
            <div class="id-card">
                {{-- Header Kartu --}}
                <div class="id-card-header">
                    <div class="id-card-header-left">
                        <div class="id-card-logo-circle">
                            <x-school-logo :size="18" />
                        </div>
                        <div class="id-card-header-text">
                            <h2 class="id-card-header-title">{{ $schoolName }}</h2>
                            <p class="id-card-header-sub">KARTU PESERTA EKSTRAKURIKULER</p>
                        </div>
                    </div>
                    <div style="text-align: right; line-height: 1;">
                        <span style="font-size: 5px; opacity: 0.8; display: block; font-weight: 600;">TAHUN AJARAN</span>
                        <span style="font-size: 7px; font-weight: 800; letter-spacing: 0.2px;">{{ $tahunAjaran }}</span>
                    </div>
                </div>

                {{-- Badan Kartu --}}
                <div class="id-card-body">
                    <div class="id-card-avatar">
                        <x-icon name="user" class="h-8 w-8 text-slate-400" />
                        <span style="font-size: 5px; font-weight: 700; margin-top: 2px; text-transform: uppercase;">SISWA</span>
                    </div>

                    <div class="id-card-details">
                        <div class="id-card-name">{{ $student->nama }}</div>
                        <div class="id-card-row">
                            <span class="id-card-label">NIS / NISN</span>
                            <span class="id-card-val">: {{ $student->nis }} {{ $student->nisn ? '/ ' . $student->nisn : '' }}</span>
                        </div>
                        <div class="id-card-row">
                            <span class="id-card-label">Kelas / Jurusan</span>
                            <span class="id-card-val">: {{ $registration->kelas ?: ($student->kelas ?? '-') }}</span>
                        </div>
                        <div class="id-card-row">
                            <span class="id-card-label">Masa Berlaku</span>
                            <span class="id-card-val">: s/d {{ $berlakuSampai }}</span>
                        </div>

                        <div class="id-card-eskul-box">
                            <div class="id-card-eskul-label">Ekstrakurikuler Terdaftar</div>
                            <div class="id-card-eskul-name">{{ $registration->eskul_utama }}</div>
                        </div>
                    </div>
                </div>

                {{-- Footer Kartu --}}
                <div class="id-card-footer">
                    <div class="id-card-status">
                        <x-icon name="check-circle" class="h-3 w-3 text-emerald-600" />
                        <span>STATUS: TERDAFTAR RESMI</span>
                    </div>
                    <div class="id-card-token">
                        REF: {{ $registration->verify_token ?: strtoupper(substr(md5($student->nis . $registration->created_at), 0, 8)) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Rincian Ekstrakurikuler & Kontak Pembina --}}
        @php
            $eskulModel = \App\Models\Eskul::with(['jadwals', 'pembina'])->where('name', $registration->eskul_utama)->first();
        @endphp
        <div class="no-print rounded-2xl border border-slate-100 bg-slate-50/80 p-4 text-xs text-left space-y-2">
            <h3 class="font-bold text-slate-900 text-sm">Informasi Kegiatan Ekstrakurikuler</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-slate-600">
                <p>Guru Pembina: <strong>{{ $eskulModel?->pembina?->name ?? 'Diumumkan Kesiswaan' }}</strong></p>
                <p>Nomor Kontak: <strong>{{ $eskulModel?->pembina?->whatsapp ?: '-' }}</strong></p>
                <p class="sm:col-span-2">Jadwal Latihan: <strong>{{ $eskulModel?->jadwals?->map(fn($j) => $j->hari . ' (' . substr($j->jam_mulai, 0, 5) . ' - ' . substr($j->jam_selesai, 0, 5) . ')')->implode(', ') ?: 'Sesuai arahan pembina' }}</strong></p>
            </div>
        </div>
    </div>
</div>

</body>
</html>

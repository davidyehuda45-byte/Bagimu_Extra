@php use SimpleSoftwareIO\QrCode\Facades\QrCode as SimpleQr; @endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sertifikat {{ $certificate->nomor }} — {{ $registration->user?->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .cert-container {
                box-shadow: none !important;
                border-width: 6px !important;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 antialiased font-sans">
<div class="max-w-4xl mx-auto p-4 sm:p-6">
    <div class="no-print mb-4 flex items-center justify-between">
        <a href="{{ route('admin.certificates.index') }}" class="px-4 py-2 rounded-lg border bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm">&larr; Kembali</a>
        <button onclick="window.print()" class="px-5 py-2 rounded-lg bg-blue-700 hover:bg-blue-800 text-white text-sm font-bold transition shadow-sm inline-flex items-center gap-1.5">
            <x-icon name="printer" class="h-4 w-4" /> Cetak / Unduh PDF
        </button>
    </div>
    <div class="cert-container bg-white border-8 border-double border-slate-800 rounded-xl p-8 sm:p-12 text-center shadow-lg relative" style="min-height: 520px;">
        <div class="text-xs font-bold tracking-widest uppercase text-slate-500 mb-1">
            {{ \App\Models\AppSetting::get('school.name', 'SMK BAGIMU NEGERIKU SEMARANG') }}
        </div>
        <div class="text-sm tracking-widest uppercase font-semibold text-blue-900 mb-4">
            SERTIFIKAT PENGHARGAAN EKSTRAKURIKULER
        </div>
        <div class="text-xs text-slate-500">Diberikan kepada:</div>
        <h1 class="text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">{{ $registration->user?->name ?? '-' }}</h1>
        <p class="text-sm text-slate-600 mt-1 font-medium">NIS: {{ $registration->nis ?? '-' }} &bull; Kelas: {{ $registration->kelas }}</p>
        <p class="mt-6 text-sm text-slate-700">Telah aktif dan berhasil menyelesaikan kegiatan ekstrakurikuler</p>
        <p class="text-2xl font-bold text-slate-900 mt-1">{{ $registration->eskul_utama }}</p>
        <p class="text-xs text-slate-500 mt-1">Tahun Ajaran {{ $registration->periode }}</p>
        @if ($registration->predikat)
            <div class="mt-2 inline-block px-3 py-1 rounded-full bg-slate-100 text-xs font-bold text-slate-800 border">
                Nilai: {{ $registration->nilai ?? '-' }} (Predikat {{ $registration->predikat }})
            </div>
        @endif
        <div class="mt-5 text-xs text-slate-500">Nomor Registrasi: <strong class="text-slate-800">{{ $certificate->nomor }}</strong></div>
        <div class="mt-6 flex flex-col items-center justify-center">
            <div class="p-2 bg-white rounded-lg border border-slate-200">
                {!! SimpleQr::size(120)->generate(route('verify.form', ['kode' => $certificate->nomor])) !!}
            </div>
            <div class="mt-2 text-[10px] text-slate-400 font-mono break-all max-w-sm">{{ route('verify.form', ['kode' => $certificate->nomor]) }}</div>
            <div class="mt-1 text-[10px] text-slate-500">Nomor: <strong class="font-mono">{{ $certificate->nomor }}</strong> — QR ini stabil walau E-Card dicetak ulang.</div>
        </div>
        <div class="mt-6 text-[11px] text-slate-400 italic">Pindai kode QR untuk memverifikasi keaslian dokumen sertifikat ini secara resmi.</div>
    </div>
</div>
</body>
</html>

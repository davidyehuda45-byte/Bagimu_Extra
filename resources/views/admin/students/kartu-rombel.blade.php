<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Kartu Login — Kelas {{ $kelas }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm;
        }
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-card {
                box-shadow: none !important;
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen py-6 px-4 font-sans">

    {{-- Top Action Bar (No-Print) --}}
    <div class="no-print max-w-5xl mx-auto mb-6 flex flex-col sm:flex-row items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-base font-bold text-slate-900">Lembar Cetak Kartu Login Siswa</h1>
            <p class="text-xs text-slate-500">Kelas: <strong class="text-slate-800">{{ $kelas }}</strong> • Total: <strong class="text-blue-700">{{ count($cardsData) }} kartu</strong> (Format A4 Siap Gunting)</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.close()" type="button" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                Tutup
            </button>
            <button onclick="window.print()" type="button" class="rounded-xl bg-blue-700 px-5 py-2 text-xs font-bold text-white hover:bg-blue-800 shadow-sm cursor-pointer flex items-center gap-1.5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    {{-- Grid Kartu Lembar A4 (2 Kolom x 4 Baris) --}}
    <div class="max-w-5xl mx-auto grid grid-cols-2 gap-3.5">
        @foreach ($cardsData as $item)
            @php
                $s = $item['student'];
                $token = $item['token'];
            @endphp
            <div class="print-card rounded-2xl bg-white border-2 border-dashed border-slate-300 overflow-hidden flex flex-col justify-between p-3.5 shadow-2xs text-left">
                {{-- Header Kartu --}}
                <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-2">
                    <div class="flex items-center gap-2">
                        <x-school-logo :size="28" />
                        <div class="leading-tight">
                            <span class="block text-[10px] font-extrabold uppercase text-blue-900 tracking-wider">
                                {{ config('app.name', 'Bagimu Extra') }}
                            </span>
                            <span class="block text-[9px] text-slate-500 font-medium">
                                {{ \App\Models\AppSetting::get('school.name', 'SMK Bagimu Negeriku') }}
                            </span>
                        </div>
                    </div>
                    <span class="rounded bg-blue-100 text-blue-900 font-mono font-bold text-[10px] px-2 py-0.5">
                        {{ $s->kelas }}
                    </span>
                </div>

                {{-- Konten Utama --}}
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <h2 class="text-xs font-black text-slate-900 truncate leading-tight">{{ $s->nama }}</h2>
                        <p class="text-[10px] font-mono text-slate-500 mt-0.5">NIS: {{ $s->nis }}</p>

                        {{-- Token Box --}}
                        <div class="mt-2 rounded-lg bg-blue-50 border border-blue-200/80 px-2.5 py-1 inline-block">
                            <span class="block text-[8px] font-bold uppercase text-slate-400 leading-none">Token Login:</span>
                            <span class="font-mono text-sm font-black tracking-widest text-blue-950 select-all leading-normal">
                                {{ $token }}
                            </span>
                        </div>
                    </div>

                    {{-- QR Code Login Siswa --}}
                    <div class="shrink-0 flex flex-col items-center">
                        <div class="p-1 bg-white border border-slate-200 rounded-lg">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(58)->margin(0)->generate(route('siswa.login')) !!}
                        </div>
                        <span class="text-[7px] text-slate-400 mt-0.5 uppercase tracking-wider font-semibold">Scan Login</span>
                    </div>
                </div>

                {{-- Footer Kartu --}}
                <div class="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-between text-[8px] text-slate-400">
                    <span>Gunakan NIS & Token untuk login</span>
                    <span class="font-mono">{{ route('siswa.login') }}</span>
                </div>
            </div>
        @endforeach
    </div>

</body>
</html>

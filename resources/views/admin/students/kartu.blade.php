<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kartu Login Siswa — {{ $student->nama }} ({{ $student->nis }})</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { background: white !important; margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .print-card { box-shadow: none !important; border: 1.5px solid #0f172a !important; page-break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen py-8 px-4 flex flex-col items-center justify-center font-sans">

    {{-- Kartu Kredensial Siswa --}}
    <div class="print-card w-full max-w-sm rounded-3xl bg-white shadow-xl border border-slate-200/80 overflow-hidden text-center transition-all">
        {{-- Card Header --}}
        <div class="bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 px-6 py-5 text-white">
            <div class="flex items-center justify-center gap-3">
                <x-school-logo :size="38" />
                <div class="text-left leading-tight">
                    <p class="text-xs font-bold uppercase tracking-wider text-blue-300">{{ config('app.name', 'Bagimu Extra') }}</p>
                    <p class="text-[11px] text-slate-300 font-medium">{{ \App\Models\AppSetting::get('school.name', 'SMK Bagimu Negeriku') }}</p>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-white/10">
                <span class="inline-block rounded-full bg-white/10 px-3 py-0.5 text-[10px] font-bold uppercase tracking-widest text-blue-200">
                    Kartu Login Siswa
                </span>
            </div>
        </div>

        {{-- Card Body --}}
        <div class="p-6 space-y-4">
            <div>
                <h2 class="text-lg font-black text-slate-900 leading-tight">{{ $student->nama }}</h2>
                <div class="mt-2 flex items-center justify-center gap-2 flex-wrap text-xs">
                    <span class="rounded-lg bg-slate-100 px-2.5 py-1 font-semibold text-slate-700">NIS: {{ $student->nis }}</span>
                    <span class="rounded-lg bg-blue-50 px-2.5 py-1 font-semibold text-blue-700">{{ $student->kelas }}</span>
                </div>
            </div>

            {{-- Token Credential Box --}}
            <div class="rounded-2xl border border-blue-100 bg-gradient-to-b from-blue-50/70 to-slate-50 p-4 shadow-inner">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Token Akses Login</p>
                @if ($plainToken)
                    <p class="my-1 font-mono text-3xl font-extrabold tracking-[0.25em] text-blue-950 select-all">
                        {{ $plainToken }}
                    </p>
                    <p class="text-[10px] text-slate-500 leading-relaxed">
                        Ketik NIS & token ini pada halaman login siswa.
                    </p>
                @else
                    <p class="my-1 font-mono text-xl font-bold tracking-widest text-slate-300">—</p>
                    <p class="text-[10px] text-slate-400 leading-relaxed">
                        Belum ada token aktif. Terbitkan dari Direktori Siswa (tombol Token).
                    </p>
                @endif
            </div>

            {{-- QR Code Login Portal --}}
            <div class="flex flex-col items-center pt-1">
                <div class="rounded-2xl border border-slate-200 bg-white p-2.5 shadow-sm">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(125)->margin(1)->generate(route('siswa.login')) !!}
                </div>
                <p class="mt-2 text-[10px] text-slate-400 font-medium">Pindai QR untuk membuka portal login siswa</p>
            </div>
        </div>

        {{-- Card Footer --}}
        <div class="bg-slate-50 border-t border-slate-100 px-6 py-3 text-[10px] text-slate-400 flex items-center justify-between">
            <span>Simpan & jaga kerahasiaan token</span>
            <span>Tahun Ajaran Aktif</span>
        </div>
    </div>

    {{-- Action Buttons (Screen Only) --}}
    <div class="no-print mt-6 flex gap-3">
        <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-blue-700 px-5 py-2.5 text-xs font-bold text-white shadow hover:bg-blue-800 transition-colors">
            <x-icon name="printer" class="h-4 w-4" />
            Cetak Kartu
        </button>
        <a href="{{ route('admin.students.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
            Kembali ke Siswa
        </a>
    </div>

</body>
</html>

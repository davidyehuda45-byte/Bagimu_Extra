@extends('layouts.staff')

@section('title', 'Kartu Login Siswa')

@section('content')
<div class="no-print mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Kartu Login Siswa ({{ $kartu->total() }})</h1>
        <p class="text-xs text-slate-500 mt-0.5">Cetak kartu fisik untuk dibagikan kepada siswa sebelum periode pendaftaran dimulai.</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <form method="GET" action="{{ route('admin.kartu.index') }}" class="flex gap-2">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama atau NIS..." class="border-slate-300 rounded-xl text-sm focus:border-blue-500 focus:ring-blue-500">
            <button class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold bg-white hover:bg-slate-50 text-slate-700">Cari</button>
        </form>
        <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-blue-700 px-4 py-2.5 text-xs font-bold text-white hover:bg-blue-800 shadow transition-colors">
            <x-icon name="printer" class="h-4 w-4" />
            <span>Cetak Halaman Ini ({{ $kartu->count() }} dari {{ $kartu->total() }})</span>
        </button>
    </div>
</div>

<style>
@media print {
    .no-print, header, aside { display: none !important; }
    main { padding: 0 !important; max-width: 100% !important; margin: 0 !important; }
    .print-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 12px !important;
    }
    .print-card-box {
        border: 1.5px solid #0f172a !important;
        box-shadow: none !important;
        page-break-inside: avoid !important;
    }
}
</style>

<div class="print-grid grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($kartu as $s)
        <div class="print-card-box rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden text-center flex flex-col justify-between">
            {{-- Header kartu --}}
            <div class="bg-gradient-to-r from-slate-900 to-blue-950 px-4 py-3 text-white">
                <div class="flex items-center justify-center gap-2.5">
                    <x-school-logo :size="28" />
                    <div class="text-left leading-tight">
                        <p class="text-[11px] font-black uppercase tracking-wider text-blue-300">{{ config('app.name', 'Bagimu Extra') }}</p>
                        <p class="text-[9px] text-slate-300">{{ \App\Models\AppSetting::get('school.name', 'SMK Bagimu Negeriku') }}</p>
                    </div>
                </div>
            </div>

            {{-- Body kartu --}}
            <div class="p-4 space-y-3 flex-1 flex flex-col justify-center">
                <div>
                    <h3 class="font-black text-sm text-slate-900 truncate">{{ $s->nama }}</h3>
                    <div class="mt-1 flex items-center justify-center gap-1.5 text-[11px]">
                        <span class="rounded-md bg-slate-100 px-2 py-0.5 font-semibold text-slate-700">NIS: {{ $s->nis }}</span>
                        <span class="rounded-md bg-blue-50 px-2 py-0.5 font-semibold text-blue-700">{{ $s->kelas }}</span>
                    </div>
                </div>

                {{-- Token --}}
                <div class="rounded-xl border border-blue-100 bg-blue-50/50 p-2.5">
                    <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Token Login</span>
                    @if ($s->user?->login_token && !$s->user?->login_token_revoked_at)
                        <p class="font-mono text-2xl font-extrabold tracking-[0.25em] text-blue-950 my-0.5">
                            {{ $s->user->login_token }}
                        </p>
                        <p class="text-[9px] text-slate-500">Ketik NIS & token ini di login siswa</p>
                    @else
                        <p class="font-mono text-xl font-bold tracking-widest text-slate-300 my-0.5">—</p>
                        <p class="text-[9px] text-slate-400">Belum ada token aktif</p>
                    @endif
                </div>

                {{-- QR Code mini: hanya untuk buka portal login, bukan verifikasi --}}
                <div class="flex flex-col items-center pt-1">
                    <div class="rounded-xl border border-slate-200 bg-white p-1.5">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->margin(1)->generate(route('siswa.login')) !!}
                    </div>
                    <p class="mt-1 text-[8px] text-slate-400">QR Login — pindai untuk membuka portal login siswa</p>
                </div>
            </div>

            {{-- Footer kartu --}}
            <div class="bg-slate-50 border-t border-slate-100 px-3 py-1.5 text-[9px] text-slate-400">
                <span>Rahasia • Jangan berikan token kepada orang lain</span>
            </div>
        </div>
    @empty
        <div class="col-span-full py-12 text-center text-slate-400">
            <p class="text-sm font-semibold text-slate-600">Belum ada siswa berakun.</p>
            <p class="text-xs text-slate-400 mt-1">Terbitkan token siswa terlebih dahulu di Direktori Siswa.</p>
        </div>
    @endforelse
</div>

<div class="no-print mt-5 border-t border-slate-100 pt-3">
    {{ $kartu->links() }}
</div>
@endsection

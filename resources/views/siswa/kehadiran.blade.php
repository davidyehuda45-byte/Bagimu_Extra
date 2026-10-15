@extends('layouts.siswa')

@section('title', 'Presensi & Izin')

@section('content')
<div class="space-y-4">
    <div>
        <h1 class="text-lg font-bold text-slate-900">Presensi & Izin</h1>
        @if ($pendaftaran)
            <p class="text-sm text-slate-500">{{ $pendaftaran->eskul_utama }} • Periode {{ $pendaftaran->periode }}</p>
        @endif
    </div>

    {{-- Filter eskul --}}
    @if ($eskulDiikuti->isNotEmpty())
        <form method="GET" action="{{ route('siswa.kehadiran') }}" class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
            <label for="filter-eskul" class="block text-xs font-semibold text-slate-600">Filter eskul</label>
            <div class="mt-1 flex gap-2">
                <select id="filter-eskul" name="eskul" class="min-w-0 flex-1 rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua eskul yang diikuti</option>
                    @foreach ($eskulDiikuti as $nama)
                        <option value="{{ $nama }}" @selected($filter === $nama)>{{ $nama }}</option>
                    @endforeach
                </select>
                <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Tampilkan</button>
            </div>
        </form>
    @endif

    {{-- KPI --}}
    <div class="grid grid-cols-4 gap-2 text-center">
        <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
            <p class="text-xl font-extrabold text-emerald-600">{{ $statistik['hadir'] }}</p>
            <p class="text-[11px] text-slate-500">Hadir</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
            <p class="text-xl font-extrabold text-amber-600">{{ $statistik['izin'] }}</p>
            <p class="text-[11px] text-slate-500">Izin</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
            <p class="text-xl font-extrabold text-amber-600">{{ $statistik['sakit'] }}</p>
            <p class="text-[11px] text-slate-500">Sakit</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
            <p class="text-xl font-extrabold text-rose-600">{{ $statistik['alpa'] }}</p>
            <p class="text-[11px] text-slate-500">Alpa</p>
        </div>
    </div>

    {{-- Bar persen --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <x-progress :value="$persen" aria-label="Persentase kehadiran {{ $persen }} persen" />
        <div class="mt-2 flex items-center justify-between">
            <p class="text-xs text-slate-500">
                @if ($statistik['total'] === 0)
                    Belum ada data presensi.
                @elseif ($persen >= 80)
                    Kehadiran sangat baik, pertahankan!
                @else
                    Kehadiran di bawah 80%, tingkatkan kehadiranmu.
                @endif
            </p>
            <span class="text-sm font-bold tabular-nums text-slate-800">{{ $persen }}%</span>
        </div>
    </div>

    {{-- Ajukan izin --}}
    <a href="{{ route('siswa.izin.create') }}" class="block rounded-xl bg-blue-700 px-5 py-3 text-center text-sm font-bold text-white hover:bg-blue-800">Ajukan Izin / Sakit</a>

    {{-- Riwayat pengajuan --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <h2 class="font-bold text-slate-900">Riwayat Pengajuan Izin</h2>
        @if ($pengajuan->isEmpty())
            <p class="mt-1 text-sm text-slate-500">Belum ada pengajuan.</p>
        @else
            <ul class="mt-2 divide-y divide-slate-100">
                @foreach ($pengajuan as $p)
                    <li class="py-2">
                        <div class="flex items-center gap-2 text-sm">
                            <span class="font-semibold text-slate-900">{{ $p->tanggal->format('d M Y') }}</span>
                            <span class="text-slate-500">{{ $p->jenis->label() }}</span>
                            <span class="ms-auto inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $p->status->value === 'APPROVED' ? 'bg-emerald-100 text-emerald-800' : ($p->status->value === 'PENDING' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">{{ $p->status->label() }}</span>
                        </div>
                        <p class="mt-0.5 text-xs text-slate-600">{{ $p->alasan }}</p>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Riwayat pertemuan --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <h2 class="font-bold text-slate-900">Riwayat Pertemuan</h2>
        @if ($kehadiran->isEmpty())
            <p class="mt-1 text-sm text-slate-500">Belum ada data presensi.</p>
        @else
            <ul class="mt-2 divide-y divide-slate-100">
                @foreach ($kehadiran as $h)
                    <li class="flex items-center gap-2 py-2 text-sm">
                        <span class="font-semibold text-slate-900">{{ $h->tanggal->format('d M Y') }}</span>
                        <span class="text-slate-500">{{ $h->eskul?->name }}</span>
                        <span class="ms-auto inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $h->status->value === 'HADIR' ? 'bg-emerald-100 text-emerald-800' : ($h->status->value === 'ALPA' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">{{ $h->status->label() }}</span>
                    </li>
                    @if ($h->keterangan)
                        <p class="-mt-1 mb-1 text-xs text-slate-400">{{ $h->keterangan }}</p>
                    @endif
                @endforeach
            </ul>
            @if ($kehadiran->hasPages())
                <div class="mt-2">{{ $kehadiran->links() }}</div>
            @endif
        @endif
        <p class="mt-2 text-[11px] text-slate-400">Sakit/dispensasi diverifikasi Pembina sebelum disahkan rekap resmi.</p>
    </div>
</div>
@endsection

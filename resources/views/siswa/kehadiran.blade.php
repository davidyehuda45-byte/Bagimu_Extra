@extends('layouts.siswa')

@section('title', 'Presensi & Izin')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div>
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Presensi & Pengajuan Izin</h1>
            @if ($pendaftaran)
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ $pendaftaran->eskul_utama }} • Tahun Ajaran {{ $pendaftaran->periode }}</p>
            @endif
        </div>
        <a href="{{ route('siswa.izin.create') }}" class="inline-flex items-center gap-1.5 self-start sm:self-center rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700 transition shadow-xs shadow-blue-600/20">
            <x-icon name="plus" class="h-3.5 w-3.5" />
            <span>Ajukan Izin / Sakit</span>
        </a>
    </div>

    {{-- Filter Eskul --}}
    @if ($eskulDiikuti->isNotEmpty())
        <form method="GET" action="{{ route('siswa.kehadiran') }}" class="rounded-2xl sm:rounded-3xl border border-slate-200/90 bg-white p-4 shadow-xs">
            <label for="filter-eskul" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Filter Berdasarkan Ekstrakurikuler</label>
            <div class="flex gap-2">
                <select id="filter-eskul" name="eskul" class="min-w-0 flex-1 rounded-xl border-slate-300 text-xs sm:text-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-500/10">
                    <option value="">Semua eskul yang diikuti</option>
                    @foreach ($eskulDiikuti as $nama)
                        <option value="{{ $nama }}" @selected($filter === $nama)>{{ $nama }}</option>
                    @endforeach
                </select>
                <button class="rounded-xl bg-slate-900 px-4 py-2 text-xs sm:text-sm font-semibold text-white hover:bg-slate-800 transition cursor-pointer">Tampilkan</button>
            </div>
        </form>
    @endif

    {{-- KPI Cards - Bersih & Cerah --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
        <div class="rounded-2xl sm:rounded-3xl border border-slate-200/80 bg-white p-4 shadow-xs">
            <p class="text-2xl sm:text-3xl font-black text-slate-900 tabular-nums">{{ $statistik['hadir'] }}</p>
            <p class="text-xs font-semibold text-emerald-600 mt-0.5">Hadir</p>
        </div>
        <div class="rounded-2xl sm:rounded-3xl border border-slate-200/80 bg-white p-4 shadow-xs">
            <p class="text-2xl sm:text-3xl font-black text-slate-900 tabular-nums">{{ $statistik['izin'] }}</p>
            <p class="text-xs font-semibold text-amber-600 mt-0.5">Izin</p>
        </div>
        <div class="rounded-2xl sm:rounded-3xl border border-slate-200/80 bg-white p-4 shadow-xs">
            <p class="text-2xl sm:text-3xl font-black text-slate-900 tabular-nums">{{ $statistik['sakit'] }}</p>
            <p class="text-xs font-semibold text-blue-600 mt-0.5">Sakit</p>
        </div>
        <div class="rounded-2xl sm:rounded-3xl border border-slate-200/80 bg-white p-4 shadow-xs">
            <p class="text-2xl sm:text-3xl font-black text-slate-900 tabular-nums">{{ $statistik['alpa'] }}</p>
            <p class="text-xs font-semibold text-rose-600 mt-0.5">Alpa</p>
        </div>
    </div>

    {{-- Bar Persen Kehadiran --}}
    <x-card>
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Persentase Kehadiran Latihan</span>
            <span class="text-base font-black tabular-nums text-slate-900">{{ $persen }}%</span>
        </div>
        <x-progress :value="$persen" aria-label="Persentase kehadiran {{ $persen }} persen" />
        <p class="mt-2 text-xs text-slate-500 leading-relaxed">
            @if ($statistik['total'] === 0)
                Belum ada data presensi pertemuan untuk eskul ini.
            @elseif ($persen >= 80)
                Kehadiran sangat baik, pertahankan keaktifanmu hingga akhir semester!
            @else
                Tingkat kehadiran di bawah 80%, tingkatkan keaktifan latihanmu untuk kelulusan eskul.
            @endif
        </p>
    </x-card>

    {{-- Riwayat Pengajuan Izin --}}
    <x-card>
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                    <x-icon name="inbox" class="h-4 w-4" />
                </span>
                <h2 class="font-bold text-slate-900 text-sm sm:text-base">Riwayat Surat Izin & Sakit</h2>
            </div>
            <a href="{{ route('siswa.izin.create') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">+ Buat Pengajuan</a>
        </div>

        @if ($pengajuan->isEmpty())
            <x-empty-state icon="inbox" title="Belum ada pengajuan izin" message="Surat izin atau sakit yang pernah diajukan akan tercatat di sini." />
        @else
            <ul class="mt-3 divide-y divide-slate-100">
                @foreach ($pengajuan as $p)
                    <li class="py-3 first:pt-1 last:pb-0">
                        <div class="flex flex-wrap items-center justify-between gap-2 text-xs sm:text-sm">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900">{{ $p->tanggal->format('d M Y') }}</span>
                                <span class="text-slate-400">•</span>
                                <span class="font-medium text-slate-600">{{ $p->jenis->label() }}</span>
                            </div>
                            <x-badge tone="{{ $p->status->value === 'APPROVED' ? 'emerald' : ($p->status->value === 'PENDING' ? 'amber' : 'rose') }}" dot="true">
                                {{ $p->status->label() }}
                            </x-badge>
                        </div>
                        <p class="mt-1 text-xs text-slate-600 leading-relaxed">{{ $p->alasan }}</p>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-card>

    {{-- Riwayat Pertemuan --}}
    <x-card>
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                    <x-icon name="calendar-check" class="h-4 w-4" />
                </span>
                <h2 class="font-bold text-slate-900 text-sm sm:text-base">Riwayat Presensi Latihan</h2>
            </div>
        </div>

        @if ($kehadiran->isEmpty())
            <x-empty-state icon="calendar" title="Belum ada data presensi" message="Data absensi latihan rutin akan otomatis muncul setelah sesi selesai dicatat oleh pembina." />
        @else
            <ul class="mt-3 divide-y divide-slate-100">
                @foreach ($kehadiran as $h)
                    <li class="py-3 first:pt-1 last:pb-0">
                        <div class="flex items-center justify-between gap-2 text-xs sm:text-sm">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900">{{ $h->tanggal->format('d M Y') }}</span>
                                <span class="text-slate-400">•</span>
                                <span class="text-slate-600 font-medium">{{ $h->eskul?->name }}</span>
                            </div>
                            <x-badge tone="{{ $h->status->value === 'HADIR' ? 'emerald' : ($h->status->value === 'ALPA' ? 'rose' : 'amber') }}" dot="true">
                                {{ $h->status->label() }}
                            </x-badge>
                        </div>
                        @if ($h->keterangan)
                            <p class="mt-1 text-xs text-slate-500">{{ $h->keterangan }}</p>
                        @endif
                    </li>
                @endforeach
            </ul>
            @if ($kehadiran->hasPages())
                <div class="mt-4 pt-3 border-t border-slate-100">{{ $kehadiran->links() }}</div>
            @endif
        @endif
        <p class="mt-3 text-[11px] text-slate-400">Catatan: Presensi sakit/izin memerlukan verifikasi dari Guru Pembina eskul sebelum disahkan dalam rekapitulasi nilai.</p>
    </x-card>
</div>
@endsection

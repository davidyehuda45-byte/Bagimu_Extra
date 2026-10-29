@extends('layouts.staff')

@section('title', 'Periode & Jendela Pendaftaran')

@section('content')
<div class="mb-5">
    <h1 class="text-xl font-bold text-slate-900">Periode & Jendela Pendaftaran</h1>
    <p class="text-sm text-slate-500">Kelola status jendela buka/tutup pendaftaran siswa serta riwayat arsip periode.</p>
</div>

@if ($activePeriode)
    {{-- Kartu Status Jendela Periode Aktif --}}
    @php($buka = $window && $window->exists && $window->isOpen())
    <div class="rounded-2xl border p-5 mb-4 shadow-sm {{ $buka ? 'border-emerald-200 bg-emerald-50' : 'border-rose-200 bg-rose-50' }}">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-2.5 w-2.5 rounded-full {{ $buka ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                    <p class="font-extrabold {{ $buka ? 'text-emerald-800' : 'text-rose-700' }}">
                        JENDELA {{ $buka ? 'DIBUKA — Siswa bisa mendaftar' : 'DITUTUP — Formulir terkunci' }}
                    </p>
                </div>
                <p class="text-sm mt-0.5 {{ $buka ? 'text-emerald-700' : 'text-rose-600' }}">
                    Periode Aktif: <strong>{{ $activePeriode->periode }}</strong> ({{ $activePeriode->nama ?? 'Tanpa label' }}) • {{ $window && $window->exists ? $window->label() : 'Belum diatur (tertutup)' }}
                </p>
            </div>
            <form method="POST" action="{{ route('admin.windows.update', $activePeriode->periode) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="buka" value="{{ $buka ? '0' : '1' }}">
                @if ($window && $window->exists)
                    <input type="hidden" name="tgl_buka" value="{{ $window->tgl_buka?->format('Y-m-d\TH:i') }}">
                    <input type="hidden" name="tgl_tutup" value="{{ $window->tgl_tutup?->format('Y-m-d\TH:i') }}">
                @endif
                <button class="rounded-xl px-5 py-2.5 text-sm font-bold text-white shadow-sm transition {{ $buka ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                    {{ $buka ? 'Tutup Pendaftaran Sekarang' : 'Buka Pendaftaran Sekarang' }}
                </button>
            </form>
        </div>
        <p class="mt-2 text-[11px] {{ $buka ? 'text-emerald-600' : 'text-rose-500' }}">Berlaku seketika, jadwal otomatis dipertahankan.</p>
    </div>

    {{-- Jadwal Otomatis --}}
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-5 shadow-sm mb-6">
        <h2 class="font-bold text-slate-900 text-sm mb-1">Jadwal Otomatis (Opsional)</h2>
        <p class="text-xs text-slate-500 mb-3">Atur rentang waktu pembukaan jendela untuk Periode {{ $activePeriode->periode }}.</p>
        <form method="POST" action="{{ route('admin.windows.update', $activePeriode->periode) }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-sm">
            @csrf
            @method('PUT')
            <input type="hidden" name="buka" value="1">
            <div class="sm:col-span-2">
                <label class="block font-medium text-slate-700 mb-1">Buka mulai</label>
                <input type="datetime-local" name="tgl_buka" value="{{ old('tgl_buka', $window?->tgl_buka?->format('Y-m-d\TH:i')) }}" class="w-full border-slate-300 rounded-xl text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block font-medium text-slate-700 mb-1">Tutup pada</label>
                <input type="datetime-local" name="tgl_tutup" value="{{ old('tgl_tutup', $window?->tgl_tutup?->format('Y-m-d\TH:i')) }}" class="w-full border-slate-300 rounded-xl text-sm">
            </div>
            <div class="flex items-center gap-3 sm:col-span-4 mt-1">
                <button class="px-4 py-2 rounded-xl bg-blue-700 text-white font-bold hover:bg-blue-800 text-sm shadow-sm transition">Simpan Jadwal</button>
                @if ($window && $window->exists && ($window->tgl_buka || $window->tgl_tutup))
                    <button type="submit" form="hapus-jadwal-form" class="text-xs font-medium text-rose-600 hover:underline">Hapus Jadwal</button>
                @endif
            </div>
        </form>
        @if ($window && $window->exists && ($window->tgl_buka || $window->tgl_tutup))
            <form id="hapus-jadwal-form" method="POST" action="{{ route('admin.windows.update', $activePeriode->periode) }}" class="hidden">
                @csrf
                @method('PUT')
                <input type="hidden" name="buka" value="{{ $window->buka ? '1' : '0' }}">
            </form>
        @endif
    </div>
@endif

<div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-bold text-slate-900">Daftar Periode & Arsip</h2>
    <a href="{{ route('admin.periodes.create') }}" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 shadow-sm transition">Tambah Periode</a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b text-left text-gray-500">
                    <th class="py-2 px-3">Periode</th>
                    <th class="py-2 px-3">Nama</th>
                    <th class="py-2 px-3">Status</th>
                    <th class="py-2 px-3">Jendela</th>
                    <th class="py-2 px-3">Pendaftar</th>
                    <th class="py-2 px-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($periodes as $p)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-3 font-medium">{{ $p->periode }}</td>
                        <td class="py-2 px-3">{{ $p->nama ?? '-' }}</td>
                        <td class="py-2 px-3">
                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $p->status->value === 'DIBUKA' ? 'bg-emerald-100 text-emerald-800' : ($p->status->value === 'DIARSIPKAN' ? 'bg-slate-200 text-slate-600' : 'bg-amber-100 text-amber-800') }}">{{ $p->status->value === 'DIBUKA' ? 'Aktif' : $p->status->label() }}</span>
                        </td>
                        <td class="py-2 px-3">
                            @php($w = $p->window)
                            @if ($w && $w->isOpen())
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold bg-emerald-100 text-emerald-800">Buka</span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold bg-slate-200 text-slate-600">Tutup</span>
                            @endif
                        </td>
                        <td class="py-2 px-3">{{ $p->registrations_count }}</td>
                        <td class="py-2 px-3">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('admin.rekap.index', ['periode' => $p->periode]) }}" title="Lihat Rekap & Riwayat Periode Ini" class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-emerald-600 hover:bg-emerald-50 hover:border-emerald-200 transition shadow-2xs">
                                    <x-icon name="trophy" class="h-4 w-4" />
                                </a>
                                <a href="{{ route('admin.periodes.show', $p->periode) }}" title="Lihat Detail" class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 transition shadow-2xs">
                                    <x-icon name="eye" class="h-4 w-4" />
                                </a>
                                <a href="{{ route('admin.periodes.edit', $p->periode) }}" title="Ubah Periode" class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-amber-600 hover:bg-amber-50 hover:border-amber-200 transition shadow-2xs">
                                    <x-icon name="pencil" class="h-4 w-4" />
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-4 px-3 text-center text-gray-500">Belum ada periode.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $periodes->links() }}</div>
</div>
@endsection

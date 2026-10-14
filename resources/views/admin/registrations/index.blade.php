@extends('layouts.staff')

@section('title', 'Data Pendaftar')

@section('content')
<h1 class="text-xl font-bold text-slate-900 mb-4">Data Pendaftar ({{ $total }})</h1>

<form method="GET" action="{{ route('admin.registrations.index') }}" class="rounded-2xl border border-slate-200 bg-white p-4 mb-4 grid grid-cols-1 sm:grid-cols-5 gap-3 text-sm shadow-sm">
    <div>
        <label class="block font-medium mb-1 text-slate-700">Periode</label>
        <select name="periode" class="w-full border-slate-300 rounded-xl text-sm">
            <option value="">Semua</option>
            @foreach ($periodes as $p)
                <option value="{{ $p->periode }}" @selected($periodeFilter === $p->periode)>{{ $p->periode }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block font-medium mb-1 text-slate-700">Eskul Utama</label>
        <select name="eskul" class="w-full border-slate-300 rounded-xl text-sm">
            <option value="">Semua</option>
            @foreach ($eskuls as $e)
                <option value="{{ $e->name }}" @selected(request('eskul') === $e->name)>{{ $e->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block font-medium mb-1 text-slate-700">Jurusan</label>
        <select name="jurusan" class="w-full border-slate-300 rounded-xl text-sm">
            <option value="">Semua</option>
            @foreach ($jurusanList as $j)
                <option value="{{ $j->value }}" @selected(request('jurusan') === $j->value)>{{ $j->short() }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block font-medium mb-1 text-slate-700">Cari (nama / email / NIS / kelas)</label>
        <input type="text" name="q" value="{{ request('q') }}" class="w-full border-slate-300 rounded-xl text-sm" placeholder="Nama, email, NIS, atau kelas">
    </div>
    <div class="flex items-end gap-2 flex-wrap">
        <button class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-700 text-white font-semibold hover:bg-blue-800 transition-colors">
            <x-icon name="search" class="h-4 w-4" />
            <span>Filter</span>
        </button>
        <a href="{{ route('admin.registrations.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 transition-colors">Reset</a>
        <a href="{{ route('admin.registrations.export', array_merge(request()->query(), ['format' => 'excel'])) }}"
           title="Unduh seluruh data pendaftar dalam format berkas Excel (.xls)"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-emerald-600 bg-emerald-50 text-emerald-800 text-xs font-bold hover:bg-emerald-100 transition-colors">
            <x-icon name="download" class="h-3.5 w-3.5 text-emerald-700" />
            <span>Ekspor ke Excel</span>
        </a>
        <a href="{{ route('admin.registrations.export', array_merge(request()->query(), ['format' => 'csv'])) }}"
           title="Unduh data pendaftar dalam format berkas teks CSV (.csv)"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-teal-600 bg-teal-50 text-teal-800 text-xs font-bold hover:bg-teal-100 transition-colors">
            <x-icon name="download" class="h-3.5 w-3.5 text-teal-700" />
            <span>Ekspor ke CSV</span>
        </a>
    </div>
</form>

<div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <p class="mb-3 text-xs text-slate-400">Menampilkan {{ $registrations->firstItem() ?? 0 }}–{{ $registrations->lastItem() ?? 0 }} dari {{ $registrations->total() }}</p>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b text-left text-slate-500">
                    <th class="py-2 px-3">No</th>
                    <th class="py-2 px-3">Nama</th>
                    <th class="py-2 px-3 hidden xl:table-cell">Email</th>
                    <th class="py-2 px-3">Kelas</th>
                    <th class="py-2 px-3">Jurusan</th>
                    <th class="py-2 px-3">Gender</th>
                    <th class="py-2 px-3">Usia</th>
                    <th class="py-2 px-3">No. WA</th>
                    <th class="py-2 px-3">Eskul</th>
                    <th class="py-2 px-3">Periode</th>
                    <th class="py-2 px-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($registrations as $i => $r)
                    <tr class="border-b hover:bg-slate-50">
                        <td class="py-2 px-3">{{ ($registrations->firstItem() ?? 0) + $i }}</td>
                        <td class="py-2 px-3">
                            <p class="font-semibold text-slate-900">{{ $r->user?->name ?? '-' }}</p>
                            <p class="text-[11px] text-slate-400">{{ $r->user?->email }} • {{ $r->created_at?->format('d/m/Y') }}</p>
                        </td>
                        <td class="py-2 px-3 hidden xl:table-cell text-slate-600">{{ $r->user?->email ?? '-' }}</td>
                        <td class="py-2 px-3">{{ $r->kelas }}</td>
                        <td class="py-2 px-3">{{ $r->jurusan?->short() ?? '-' }}</td>
                        <td class="py-2 px-3">{{ $r->gender?->label() ?? '-' }}</td>
                        <td class="py-2 px-3">{{ $r->usia }}</td>
                        <td class="py-2 px-3">
                            @if ($r->whatsapp && \App\Support\Whatsapp::link($r->whatsapp))
                                <a href="{{ \App\Support\Whatsapp::link($r->whatsapp) }}" target="_blank" rel="noopener" class="font-medium text-emerald-600 hover:text-emerald-700">{{ $r->whatsapp }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="py-2 px-3">{{ $r->eskul_utama }}@if ($r->eskul_cadangan)<span class="block text-[11px] text-slate-400">Cad: {{ $r->eskul_cadangan }}</span>@endif</td>
                        <td class="py-2 px-3">{{ $r->periode }}</td>
                        <td class="py-2 px-3">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('admin.registrations.show', $r) }}" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs font-semibold hover:bg-slate-50">Detail</a>
                                <form method="POST" action="{{ route('admin.registrations.destroy', $r) }}" onsubmit="return confirm('Hapus permanen data pendaftaran ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg px-2.5 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="11" class="py-6 px-3 text-center text-slate-500">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $registrations->links() }}</div>
</div>
@endsection

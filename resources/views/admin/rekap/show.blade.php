@extends('layouts.staff')

@section('title', 'Siswa Eskul')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900">{{ $model->name }}</h1>
        <p class="text-sm text-slate-500">{{ $siswa->total() }} siswa eskul utama{{ $periode ? " — periode $periode" : '' }}{{ $model->kuota_aktif ? ", kuota {$model->kuota}" : '' }}</p>
        @if ($model->description)
            <p class="text-sm text-slate-600 mt-1">{{ $model->description }}</p>
        @endif
    </div>
    <a href="{{ route('admin.rekap.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-sm bg-white shrink-0">Kembali ke Rekap</a>
</div>

<div class="flex flex-wrap gap-1.5 mb-4 text-[11px] font-semibold">
    <span class="rounded-full bg-blue-100 px-2.5 py-1 text-blue-800">{{ $siswa->total() }} terdaftar</span>
</div>

<div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b text-left text-slate-500">
                    <th class="py-2 px-3">No</th>
                    <th class="py-2 px-3">Nama</th>
                    <th class="py-2 px-3">Kelas</th>
                    <th class="py-2 px-3 hidden sm:table-cell">Jurusan</th>
                    <th class="py-2 px-3 hidden md:table-cell">Tgl Daftar</th>
                    <th class="py-2 px-3">Kontak</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($siswa as $i => $r)
                    <tr class="border-b hover:bg-slate-50">
                        <td class="py-2 px-3">{{ ($siswa->firstItem() ?? 0) + $i }}</td>
                        <td class="py-2 px-3">
                            <p class="font-semibold text-slate-900">{{ $r->user?->name ?? '—' }}</p>
                            <p class="text-[11px] text-slate-400 sm:hidden">{{ $r->user?->email }}</p>
                        </td>
                        <td class="py-2 px-3">{{ $r->kelas }}</td>
                        <td class="py-2 px-3 hidden sm:table-cell">{{ $r->jurusan?->short() ?? '-' }}</td>
                        <td class="py-2 px-3 hidden md:table-cell">{{ $r->created_at?->format('d/m/Y') }}</td>
                        <td class="py-2 px-3">
                            @if ($r->whatsapp && \App\Support\Whatsapp::link($r->whatsapp))
                                <a href="{{ \App\Support\Whatsapp::link($r->whatsapp) }}" target="_blank" rel="noopener" class="font-medium text-emerald-600 hover:text-emerald-700">WA</a>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-6 px-3 text-center text-slate-500">Belum ada siswa.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $siswa->links() }}</div>
</div>
@endsection

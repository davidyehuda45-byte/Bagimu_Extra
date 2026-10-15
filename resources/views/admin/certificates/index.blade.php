@extends('layouts.staff')

@section('title', 'Sertifikat')

@section('content')
<div class="mb-4">
    <h1 class="text-xl font-bold">Sertifikat</h1>
    <p class="text-sm text-gray-600">Terbitkan sertifikat untuk anggota yang disetujui. QR sertifikat memakai nomor SRT yang stabil.</p>
</div>

<form method="GET" action="{{ route('admin.certificates.index') }}" class="mb-4 flex flex-wrap gap-2 bg-white border rounded-lg p-3">
    <select name="periode" class="border rounded px-3 py-2 text-sm">
        <option value="">Semua periode</option>
        @foreach ($periodes as $p)
            <option value="{{ $p }}" @selected($filterPeriode === $p)>{{ $p }}</option>
        @endforeach
    </select>
    <input type="text" name="q" value="{{ $filterQ }}" placeholder="Cari nama / NIS / eskul / nomor SRT…" class="min-w-0 flex-1 border rounded px-3 py-2 text-sm">
    <button class="px-4 py-2 rounded bg-gray-900 text-white text-sm">Filter</button>
</form>

<div class="bg-white border rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium">Nama</th>
                    <th class="px-4 py-2 text-left font-medium">NIS / Eskul / Periode</th>
                    <th class="px-4 py-2 text-left font-medium">Nomor Sertifikat</th>
                    <th class="px-4 py-2 text-left font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($pendaftar as $daftar)
                    <tr>
                        <td class="px-4 py-2">{{ $daftar->user?->name ?? '-' }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $daftar->nis ?? '-' }} / {{ $daftar->eskul_utama }} / {{ $daftar->periode }}</td>
                        <td class="px-4 py-2">{{ $daftar->certificate?->nomor ?? '—' }}</td>
                        <td class="px-4 py-2">
                            <div class="flex gap-2">
                                @if ($daftar->certificate)
                                    <a href="{{ route('admin.certificates.cetak', $daftar->certificate) }}" class="px-3 py-1 rounded border text-xs hover:bg-gray-50">Cetak</a>
                                @else
                                    <form method="POST" action="{{ route('admin.certificates.store') }}">
                                        @csrf
                                        <input type="hidden" name="registration_id" value="{{ $daftar->id }}">
                                        <button class="px-3 py-1 rounded bg-gray-900 text-white text-xs">Terbitkan</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center">
                            <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                <x-icon name="award" class="h-5 w-5" />
                            </div>
                            <p class="text-sm font-semibold text-slate-700">Tidak ada pendaftar yang disetujui</p>
                            <p class="text-xs text-slate-400 mt-0.5">Sertifikat hanya dapat diterbitkan untuk siswa yang pendaftarannya sudah disetujui.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $pendaftar->links() }}</div>
</div>
@endsection

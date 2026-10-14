@extends('layouts.staff')

@section('title', 'Detail Barang')

@section('content')
<div class="mb-4 flex flex-wrap items-center gap-3">
    <div>
        <h1 class="text-xl font-bold">{{ $item->nama }}</h1>
        <p class="text-sm text-gray-600">{{ $item->eskul?->name ?? 'Umum' }} · Jumlah {{ $item->jumlah }} · {{ $item->kondisi?->label() ?? $item->kondisi }}</p>
    </div>
    <a href="{{ route('admin.inventory.index') }}" class="ms-auto px-4 py-2 rounded border text-sm">Kembali</a>
</div>

@if ($item->keterangan)
    <div class="mb-4 bg-white border rounded-lg p-4 text-sm">{{ $item->keterangan }}</div>
@endif

<div class="grid lg:grid-cols-2 gap-4">
    <div class="bg-white border rounded-lg overflow-hidden">
        <div class="px-4 py-3 border-b font-semibold text-sm">Daftar Peminjaman</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium">Peminjam</th>
                        <th class="px-4 py-2 text-left font-medium">Jumlah / Tanggal</th>
                        <th class="px-4 py-2 text-left font-medium">Status</th>
                        <th class="px-4 py-2 text-left font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($loans as $loan)
                        <tr>
                            <td class="px-4 py-2">{{ $loan->peminjam }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $loan->jumlah }} · {{ $loan->tgl_pinjam?->format('Y-m-d') }} s.d. {{ $loan->tgl_kembali?->format('Y-m-d') ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $loan->status }}</td>
                            <td class="px-4 py-2">
                                @if ($loan->status === 'DIPINJAM')
                                    <form method="POST" action="{{ route('admin.loans.kembali', $loan) }}">
                                        @csrf
                                        @method('PUT')
                                        <button class="px-3 py-1 rounded bg-green-700 text-white text-xs">Tandai Kembali</button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-500">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center">
                                <div class="mx-auto mb-1 flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                    <x-icon name="clipboard" class="h-4 w-4" />
                                </div>
                                <p class="text-xs font-semibold text-slate-700">Belum ada riwayat peminjaman</p>
                                <p class="text-[11px] text-slate-400">Barang ini belum pernah dipinjam.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white border rounded-lg p-4">
        <div class="font-semibold text-sm mb-3">Formulir Peminjaman</div>
        <form method="POST" action="{{ route('admin.loans.store', $item) }}" class="space-y-3">
            @csrf
            <div>
                <label for="peminjam" class="block text-sm font-medium">Nama Peminjam</label>
                <input type="text" name="peminjam" id="peminjam" required maxlength="100" class="mt-1 w-full border rounded px-3 py-2 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="jumlah" class="block text-sm font-medium">Jumlah</label>
                    <input type="number" name="jumlah" id="jumlah" required min="1" value="1" class="mt-1 w-full border rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label for="tgl_pinjam" class="block text-sm font-medium">Tanggal Pinjam</label>
                    <input type="date" name="tgl_pinjam" id="tgl_pinjam" required value="{{ date('Y-m-d') }}" class="mt-1 w-full border rounded px-3 py-2 text-sm">
                </div>
            </div>
            <button class="px-4 py-2 rounded bg-gray-900 text-white text-sm">Catat Peminjaman</button>
        </form>
    </div>
</div>
@endsection

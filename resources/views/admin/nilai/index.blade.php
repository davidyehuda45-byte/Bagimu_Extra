@extends('layouts.staff')

@section('title', 'Nilai')

@section('content')
<div class="mb-4">
    <h1 class="text-xl font-bold">Nilai</h1>
    <p class="text-sm text-gray-600">Kelola nilai akhir anggota yang disetujui.</p>
</div>

<form method="GET" action="{{ route('admin.nilai.index') }}" class="no-print mb-4 flex flex-wrap gap-3 items-end bg-white border rounded-lg p-4">
    <div>
        <label for="eskul_id" class="block text-xs font-medium text-gray-700">Ekstrakurikuler</label>
        <select name="eskul_id" id="eskul_id" class="mt-1 border rounded px-3 py-2 text-sm">
            @foreach ($eskuls as $e)
                <option value="{{ $e->id }}" @selected($eskul && $eskul->id === $e->id)>{{ $e->name }}</option>
            @endforeach
        </select>
    </div>
    <button class="px-4 py-2 rounded bg-gray-900 text-white text-sm">Tampilkan</button>
    <span class="text-xs text-gray-500">Periode aktif: {{ $periodeAktif?->periode ?? '-' }}</span>
    @if ($eskul)
        <span class="ms-auto flex flex-wrap gap-2">
            <a href="{{ route('admin.nilai.export', ['eskul_id' => $eskul->id]) }}" class="inline-flex items-center gap-1.5 rounded border px-3 py-2 text-xs font-semibold hover:bg-gray-50"><x-icon name="download" class="h-3.5 w-3.5" />CSV</a>
            <a href="{{ route('admin.nilai.export', ['eskul_id' => $eskul->id, 'format' => 'excel']) }}" class="inline-flex items-center gap-1.5 rounded border px-3 py-2 text-xs font-semibold hover:bg-gray-50"><x-icon name="download" class="h-3.5 w-3.5" />Excel</a>
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 rounded border px-3 py-2 text-xs font-semibold hover:bg-gray-50"><x-icon name="printer" class="h-3.5 w-3.5" />Cetak</button>
        </span>
    @endif
</form>

@if (! $eskul)
    <div class="bg-white border rounded-lg p-6 text-sm text-gray-600">Belum ada data ekstrakurikuler.</div>
@else
    <form method="POST" action="{{ route('admin.nilai.store') }}" class="bg-white border rounded-lg overflow-hidden">
        @csrf
        <input type="hidden" name="eskul_id" value="{{ $eskul->id }}">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium">Nama</th>
                        <th class="px-4 py-2 text-left font-medium">NIS / Kelas</th>
                        <th class="px-4 py-2 text-left font-medium">Nilai (0–100)</th>
                        <th class="px-4 py-2 text-left font-medium">Predikat</th>
                        <th class="px-4 py-2 text-left font-medium">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($pendaftar as $daftar)
                        <tr>
                            <td class="px-4 py-2">{{ $daftar->user?->name ?? '-' }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $daftar->nis ?? '-' }} / {{ $daftar->kelas }}</td>
                            <td class="px-4 py-2">
                                <input type="number" name="nilai[{{ $daftar->id }}]" value="{{ old('nilai.'.$daftar->id, $daftar->nilai) }}" min="0" max="100" class="border rounded px-2 py-1 text-sm w-24" @disabled(auth()->user()->hasRole(\App\Enums\Role::KEPALA_SEKOLAH))>
                            </td>
                            <td class="px-4 py-2">
                                <input type="text" name="predikat[{{ $daftar->id }}]" value="{{ old('predikat.'.$daftar->id, $daftar->predikat) }}" maxlength="2" placeholder="Otomatis" class="border rounded px-2 py-1 text-sm w-20" @disabled(auth()->user()->hasRole(\App\Enums\Role::KEPALA_SEKOLAH))>
                            </td>
                            <td class="px-4 py-2">
                                <input type="text" name="catatan[{{ $daftar->id }}]" value="{{ old('catatan.'.$daftar->id, $daftar->catatan_nilai) }}" maxlength="1000" placeholder="Opsional" class="border rounded px-2 py-1 text-sm w-56">
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">Tidak ada anggota yang disetujui untuk eskul ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($pendaftar->isNotEmpty() && !auth()->user()->hasRole(\App\Enums\Role::KEPALA_SEKOLAH))
            <div class="p-4 border-t flex items-center gap-3 no-print">
                <button class="px-4 py-2 rounded bg-gray-900 text-white text-sm">Simpan Nilai</button>
                <span class="text-xs text-gray-500">Predikat otomatis bila kosong: ≥85 A, ≥70 B, ≥55 C, lainnya D.</span>
            </div>
        @endif
    </form>
@endif
@endsection

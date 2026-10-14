@extends('layouts.staff')

@section('title', 'Kehadiran')

@section('content')
<div class="mb-4">
    <h1 class="text-xl font-bold">Kehadiran</h1>
    <p class="text-sm text-gray-600">Kelola presensi anggota ekstrakurikuler per tanggal.</p>
</div>

<form method="GET" action="{{ route('admin.kehadiran.index') }}" class="mb-4 flex flex-wrap gap-3 items-end bg-white border rounded-lg p-4">
    <div>
        <label for="eskul_id" class="block text-xs font-medium text-gray-700">Ekstrakurikuler</label>
        <select name="eskul_id" id="eskul_id" class="mt-1 border rounded px-3 py-2 text-sm">
            @foreach ($eskuls as $e)
                <option value="{{ $e->id }}" @selected($eskul && $eskul->id === $e->id)>{{ $e->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="tanggal" class="block text-xs font-medium text-gray-700">Tanggal</label>
        <input type="date" name="tanggal" id="tanggal" value="{{ $tanggal }}" class="mt-1 border rounded px-3 py-2 text-sm">
    </div>
    <button class="px-4 py-2 rounded bg-gray-900 text-white text-sm">Tampilkan</button>
    @if ($eskul)
        <span class="text-xs text-gray-500">Periode aktif: {{ $periodeAktif?->periode ?? '-' }}</span>
    @endif
</form>

@if (! $eskul)
    <div class="bg-white border rounded-lg p-6 text-sm text-gray-600">Belum ada data ekstrakurikuler.</div>
@else
    @if ($locked)
        <div class="mb-4 rounded-lg border border-yellow-300 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
            Presensi tanggal {{ $tanggal }} sudah <strong>dikunci</strong> dan tidak dapat diubah.
        </div>
    @endif

    <div class="mb-4 flex flex-wrap gap-2">
        @if (auth()->user()->hasRole(\App\Enums\Role::ADMIN, \App\Enums\Role::OPERATOR, \App\Enums\Role::PEMBINA))
        <form method="POST" action="{{ route('admin.kehadiran.lock') }}">
            @csrf
            <input type="hidden" name="eskul_id" value="{{ $eskul->id }}">
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
            <button class="px-4 py-2 rounded border text-sm hover:bg-gray-50">Kunci Presensi</button>
        </form>
        @endif
        @if (auth()->user()->hasRole(\App\Enums\Role::ADMIN, \App\Enums\Role::OPERATOR))
        <form method="POST" action="{{ route('admin.kehadiran.unlock') }}">
            @csrf
            <input type="hidden" name="eskul_id" value="{{ $eskul->id }}">
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
            <button class="px-4 py-2 rounded border text-sm hover:bg-gray-50">Buka Kunci</button>
        </form>
        @endif
    </div>

    @if (!auth()->user()->hasRole(\App\Enums\Role::KEPALA_SEKOLAH))
    <form method="POST" action="{{ route('admin.kehadiran.store') }}" class="bg-white border rounded-lg overflow-hidden">
        @csrf
        <input type="hidden" name="eskul_id" value="{{ $eskul->id }}">
        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium">Nama</th>
                        <th class="px-4 py-2 text-left font-medium">NIS / Kelas</th>
                        <th class="px-4 py-2 text-left font-medium">Status</th>
                        <th class="px-4 py-2 text-left font-medium">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($pendaftar as $daftar)
                        @php $ada = $peta[$daftar->id] ?? null; @endphp
                        <tr>
                            <td class="px-4 py-2">{{ $daftar->user?->name ?? '-' }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $daftar->nis ?? '-' }} / {{ $daftar->kelas }}</td>
                            <td class="px-4 py-2">
                                <select name="status[{{ $daftar->id }}]" class="border rounded px-2 py-1 text-sm" @disabled($ada && $ada->isLocked())>
                                    @foreach ($statusList as $st)
                                        <option value="{{ $st->value }}" @selected(($ada?->status?->value ?? 'HADIR') === $st->value)>{{ $st->label() }}</option>
                                    @endforeach
                                </select>
                                @if ($ada && $ada->isLocked())
                                    <span class="ml-2 text-xs text-yellow-700">Terkunci</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                <input type="text" name="keterangan[{{ $daftar->id }}]" value="{{ $ada->keterangan ?? '' }}" maxlength="200" placeholder="Opsional" class="border rounded px-2 py-1 text-sm w-48" @disabled($ada && $ada->isLocked())>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Tidak ada anggota yang disetujui untuk eskul ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($pendaftar->isNotEmpty())
            <div class="p-4 border-t">
                <button class="px-4 py-2 rounded bg-gray-900 text-white text-sm">Simpan Kehadiran</button>
            </div>
        @endif
    </form>
    @endif
@endif
@endsection

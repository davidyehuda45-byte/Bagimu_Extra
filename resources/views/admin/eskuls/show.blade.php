@extends('layouts.staff')

@section('title', 'Detail Eskul')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-bold">{{ $eskul->name }}</h1>
    <div class="flex gap-2">
        <a href="{{ route('admin.eskuls.edit', $eskul) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold transition shadow-xs"><x-icon name="pencil" class="h-4 w-4" /> Ubah</a>
        <a href="{{ route('admin.eskuls.index') }}" class="px-4 py-2 rounded border text-sm">Kembali</a>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-5 mb-4 text-sm space-y-1">
    <p><span class="text-gray-500">Deskripsi:</span> {{ $eskul->description ?? '-' }}</p>
    <p><span class="text-gray-500">Pembina:</span> {{ $eskul->pembina?->name ?? '-' }}</p>
    @if ($eskul->temp_pembina_id)
        <p><span class="text-gray-500">Pembina sementara:</span> {{ $eskul->tempPembina?->name ?? '-' }} s/d {{ $eskul->temp_pembina_sampai?->format('d M Y') ?? '-' }}</p>
    @endif
    <p><span class="text-gray-500">Kuota:</span> {{ $eskul->kuota_aktif ? $eskul->kuota : 'Tanpa batas' }}</p>
    <p><span class="text-gray-500">Status:</span> {{ $eskul->aktif ? 'Aktif' : 'Nonaktif' }}</p>
    <div class="pt-2">
        <span class="font-medium">Jumlah pendaftar per status:</span>
        <span class="text-gray-600">Menunggu: {{ $pendaftar['PENDING'] ?? $pendaftar[\App\Enums\Status::PENDING->value] ?? 0 }}</span> ·
        <span class="text-gray-600">Disetujui: {{ $pendaftar['APPROVED'] ?? $pendaftar[\App\Enums\Status::APPROVED->value] ?? 0 }}</span> ·
        <span class="text-gray-600">Ditolak: {{ $pendaftar['REJECTED'] ?? $pendaftar[\App\Enums\Status::REJECTED->value] ?? 0 }}</span>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-5 mb-4">
    <h2 class="font-semibold mb-3 text-sm">Delegasi Pembina Sementara</h2>
    <form method="POST" action="{{ route('admin.eskuls.delegasi', $eskul) }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm items-end">
        @csrf
        <div>
            <label class="block font-medium mb-1">Pembina sementara</label>
            <select name="temp_pembina_id" class="w-full border rounded px-3 py-2">
                <option value="">— Lepas delegasi —</option>
                @foreach ($pembinas ?? [] as $p)
                    <option value="{{ $p->id }}" @selected(old('temp_pembina_id', $eskul->temp_pembina_id) == $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block font-medium mb-1">Berlaku sampai</label>
            <input type="date" name="temp_pembina_sampai" value="{{ old('temp_pembina_sampai', $eskul->temp_pembina_sampai?->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <button class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Simpan Delegasi</button>
        </div>
    </form>
</div>

<div id="jadwal" class="bg-white rounded-2xl border border-slate-200 p-5 mb-4 shadow-sm">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
            <x-icon name="clock" class="h-5 w-5 text-blue-700" />
            Daftar Jadwal Latihan
        </h2>
        <span class="text-xs text-slate-500 font-medium">{{ $eskul->jadwals->count() }} Jadwal Terdaftar</span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wider text-slate-400">
                    <th class="py-2.5 px-3">Hari</th>
                    <th class="py-2.5 px-3">Jam Latihan</th>
                    <th class="py-2.5 px-3">Tempat / Ruang</th>
                    <th class="py-2.5 px-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($eskul->jadwals as $j)
                    <tr class="hover:bg-slate-50/80 transition-colors" x-data="{ editing: false }">
                        <template x-if="!editing">
                            <td class="py-2.5 px-3 font-semibold text-slate-800">{{ $j->hari }}</td>
                        </template>
                        <template x-if="!editing">
                            <td class="py-2.5 px-3 font-mono text-xs text-slate-600">{{ $j->jam_mulai }} – {{ $j->jam_selesai }}</td>
                        </template>
                        <template x-if="!editing">
                            <td class="py-2.5 px-3 text-slate-600">{{ $j->tempat ?? '—' }}</td>
                        </template>
                        <template x-if="!editing">
                            <td class="py-2.5 px-3 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" @click="editing = true" class="inline-flex items-center justify-center h-7 w-7 rounded-lg border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100 transition shadow-2xs" title="Ubah jadwal ini">
                                        <x-icon name="pencil" class="h-3.5 w-3.5" />
                                    </button>
                                    <form method="POST" action="{{ route('admin.jadwals.destroy', $j) }}" onsubmit="return confirm('Hapus jadwal hari {{ $j->hari }} jam {{ $j->jam_mulai }}–{{ $j->jam_selesai }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center h-7 w-7 rounded-lg border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 transition shadow-2xs cursor-pointer" title="Hapus jadwal">
                                            <x-icon name="trash" class="h-3.5 w-3.5" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </template>

                        {{-- Mode Edit Form Inline --}}
                        <template x-if="editing">
                            <td colspan="4" class="p-3 bg-amber-50/50 rounded-xl">
                                <form method="POST" action="{{ route('admin.jadwals.update', $j) }}" class="space-y-3">
                                    @csrf
                                    @method('PUT')
                                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">Hari</label>
                                            <select name="hari" required class="w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs bg-white">
                                                @foreach ($hariList as $h)
                                                    <option value="{{ $h }}" @selected($j->hari === $h)>{{ $h }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">Jam Mulai</label>
                                            <input type="time" name="jam_mulai" value="{{ $j->jam_mulai }}" required class="w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs bg-white font-mono">
                                        </div>
                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">Jam Selesai</label>
                                            <input type="time" name="jam_selesai" value="{{ $j->jam_selesai }}" required class="w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs bg-white font-mono">
                                        </div>
                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">Tempat</label>
                                            <input type="text" name="tempat" value="{{ $j->tempat }}" placeholder="Aula / Lapangan" class="w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs bg-white">
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-end gap-2 pt-1">
                                        <button type="button" @click="editing = false" class="rounded-xl border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100">
                                            Batal
                                        </button>
                                        <button type="submit" class="rounded-xl bg-blue-700 px-4 py-1.5 text-xs font-bold text-white hover:bg-blue-800 shadow-sm">
                                            Simpan Perubahan Jadwal
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </template>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-slate-400">
                            <x-icon name="clock" class="h-6 w-6 mx-auto mb-1 text-slate-300" />
                            <p class="text-sm font-medium">Belum ada jadwal latihan</p>
                            <p class="text-xs text-slate-400">Tambahkan jadwal pada form di bawah.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
    <h2 class="text-base font-bold text-slate-900 mb-3 flex items-center gap-2">
        <x-icon name="plus" class="h-4 w-4 text-emerald-600" />
        Tambah Jadwal Latihan Baru
    </h2>
    <form method="POST" action="{{ route('admin.jadwals.store', $eskul) }}" class="grid grid-cols-1 sm:grid-cols-5 gap-3 text-sm">
        @csrf
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Hari</label>
            <select name="hari" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                @foreach ($hariList as $h)
                    <option value="{{ $h }}">{{ $h }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Mulai</label>
            <input type="time" name="jam_mulai" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-mono focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Selesai</label>
            <input type="time" name="jam_selesai" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-mono focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Tempat</label>
            <input type="text" name="tempat" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Aula / Lapangan">
        </div>
        <div class="flex items-end">
            <button class="w-full rounded-xl bg-blue-700 py-2.5 px-4 font-bold text-white text-sm hover:bg-blue-800 shadow-sm transition">
                + Tambah Jadwal
            </button>
        </div>
    </form>
</div>
@endsection

@extends('layouts.staff')

@section('title', 'Kenaikan Kelas')

@section('content')
<h1 class="text-xl font-bold text-slate-900 mb-1">Kenaikan Kelas</h1>
<p class="text-sm text-slate-500 mb-4">
    Kelas 10 naik ke 11 (tetap aktif) • Kelas 11 naik ke 12 (<b>otomatis nonaktif</b> — kelas 12 tidak ikut eskul & tidak bisa login) •
    Kelas 12 <b>lulus</b> (otomatis nonaktif). Kasus lulus duluan/individual atur manual di Direktori Siswa.
</p>

<div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b text-left text-slate-500">
                    <th class="py-2 px-3">NIS</th>
                    <th class="py-2 px-3">Nama</th>
                    <th class="py-2 px-3">Kelas Sekarang</th>
                    <th class="py-2 px-3">Hasil</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($peta as $row)
                    <tr class="border-b hover:bg-slate-50">
                        <td class="py-2 px-3 font-medium">{{ $row['student']->nis }}</td>
                        <td class="py-2 px-3">{{ $row['student']->nama }}</td>
                        <td class="py-2 px-3">{{ $row['student']->kelas }} ({{ $row['student']->status }})</td>
                        <td class="py-2 px-3 text-slate-600">{{ $row['aksi'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-6 text-center text-slate-500">Belum ada data siswa.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($peta->isNotEmpty())
        <form method="POST" action="{{ route('admin.kenaikan.store') }}" class="mt-4 flex flex-wrap items-center gap-3" onsubmit="return confirm('Jalankan kenaikan kelas untuk SEMUA siswa sesuai pratinjau di atas?')">
            @csrf
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="konfirmasi" value="1" required class="rounded border-slate-300 text-blue-700 focus:ring-blue-500">
                <span class="text-slate-700">Saya sudah memeriksa pratinjau di atas</span>
            </label>
            <button class="rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-blue-800">Jalankan Kenaikan Kelas</button>
        </form>
    @endif
</div>
@endsection

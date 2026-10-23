@extends('layouts.staff')

@section('title', 'Eskul Saya')

@section('content')
<div class="space-y-4">
    <div>
        <h1 class="text-xl font-bold">Eskul Saya</h1>
        <p class="text-sm text-gray-600">Daftar ekstrakurikuler yang Anda ampu (tetap maupun sementara).</p>
    </div>

    <div class="rounded-lg border bg-white p-4 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50 text-left">
                        <th class="px-3 py-2 font-medium">Nama</th>
                        <th class="px-3 py-2 font-medium">Deskripsi</th>
                        <th class="px-3 py-2 font-medium">Kuota</th>
                        <th class="px-3 py-2 font-medium">Jadwal</th>
                        <th class="px-3 py-2 font-medium">Status</th>
                        <th class="px-3 py-2 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($eskuls as $eskul)
                        <tr class="border-b last:border-0">
                            <td class="px-3 py-2 font-medium">{{ $eskul->name }}</td>
                            <td class="px-3 py-2 text-gray-600">{{ \Illuminate\Support\Str::limit($eskul->description, 60) }}</td>
                            <td class="px-3 py-2">{{ $eskul->kuota }}</td>
                            <td class="px-3 py-2">{{ $eskul->jadwals_count }} jadwal</td>
                            <td class="px-3 py-2">
                                @if ($eskul->aktif)
                                    <span class="inline-block rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">Aktif</span>
                                @else
                                    <span class="inline-block rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <a href="{{ route('pembina.eskul.show', $eskul) }}" class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 transition shadow-2xs" title="Lihat Detail">
                                    <x-icon name="eye" class="h-4 w-4" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-4 text-center text-gray-500">Belum ada eskul yang Anda ampu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

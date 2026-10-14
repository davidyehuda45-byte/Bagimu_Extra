<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jadwal Latihan — {{ config('app.name', 'Bagimu Extra') }}</title>
    <meta name="theme-color" content="#1e3a8a">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="w-full space-y-4 px-4 sm:px-8 py-10">
    <a href="{{ route('home') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">← Beranda</a>
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900">Jadwal Latihan</h1>
        <p class="text-sm text-slate-500">Waktu dalam WIB. Perubahan jadwal diumumkan pembina.</p>
    </div>

    @if ($jadwals->isEmpty())
        <p class="text-sm text-slate-500">Belum ada jadwal.</p>
    @else
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-slate-500">Hari</th>
                            <th class="px-4 py-2 text-left font-medium text-slate-500">Jam</th>
                            <th class="px-4 py-2 text-left font-medium text-slate-500">Eskul</th>
                            <th class="px-4 py-2 text-left font-medium text-slate-500">Tempat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($jadwals as $j)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2 font-semibold">{{ ucfirst(strtolower($j->hari)) }}</td>
                                <td class="px-4 py-2">{{ $j->jam_mulai }}–{{ $j->jam_selesai }}</td>
                                <td class="px-4 py-2">{{ $j->eskul?->name }}</td>
                                <td class="px-4 py-2 text-slate-500">@ {{ $j->tempat }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
</body>
</html>

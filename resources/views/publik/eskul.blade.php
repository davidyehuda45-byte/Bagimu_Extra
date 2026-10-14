<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Katalog Eskul — {{ config('app.name', 'Bagimu Extra') }}</title>
    <meta name="theme-color" content="#1e3a8a">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="w-full space-y-4 px-4 sm:px-8 py-10">
    <a href="{{ route('home') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">← Beranda</a>
    <h1 class="text-2xl font-extrabold text-slate-900">Katalog Eskul</h1>

    @if ($eskuls->isEmpty())
        <p class="text-sm text-slate-500">Belum ada eskul aktif.</p>
    @else
        <div class="grid gap-3 sm:grid-cols-2">
            @foreach ($eskuls as $e)
                <div class="rounded-2xl border border-slate-200 bg-white text-slate-900 shadow-sm">
                    <div class="p-4">
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-bold">{{ $e->name }}</p>
                            @if ($e->kuota_aktif)
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold whitespace-nowrap bg-amber-100 text-amber-800 ring-1 ring-inset ring-amber-600/20">Kuota {{ $e->kuota }}</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold whitespace-nowrap bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-600/20">Bebas</span>
                            @endif
                        </div>
                        @if ($e->description)
                            <p class="mt-1 text-sm text-slate-500">{{ $e->description }}</p>
                        @endif
                        <p class="mt-1 text-xs text-slate-500">Pembina: {{ $e->pembina?->name ?? '—' }}</p>
                        @if ($e->jadwals->isNotEmpty())
                            <ul class="mt-2 space-y-0.5 text-xs text-slate-500">
                                @foreach ($e->jadwals as $j)
                                    <li>{{ ucfirst(strtolower($j->hari)) }} {{ $j->jam_mulai }}–{{ $j->jam_selesai }} @ {{ $j->tempat }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mt-2 text-xs text-slate-400">Jadwal menyusul.</p>
                        @endif
                        <a href="{{ route('siswa.login') }}" class="mt-3 inline-block rounded-xl bg-blue-700 px-4 py-2 text-xs font-bold text-white hover:bg-blue-800">Masuk untuk mendaftar</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
</body>
</html>

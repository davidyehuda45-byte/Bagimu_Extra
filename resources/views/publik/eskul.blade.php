<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Katalog Eskul — {{ config('app.name', 'Bagimu Extra') }}</title>
    <meta name="theme-color" content="#1e3a8a">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" media="print" onload="this.media='all'" />
    <noscript><link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" /></noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="w-full space-y-4 px-4 sm:px-8 py-10">
    <a href="{{ route('home') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">← Beranda</a>
    <h1 class="text-2xl font-extrabold text-slate-900">Katalog Eskul</h1>

    @if ($eskuls->isEmpty())
        <x-card><x-empty-state icon="trophy" title="Belum ada eskul aktif" message="Katalog akan muncul setelah TU mengaktifkan eskul." /></x-card>
    @else
        <div class="grid gap-3 sm:grid-cols-2">
            @foreach ($eskuls as $e)
                <x-eskul-card :eskul="$e" :show-pembina="true" :action-url="route('siswa.daftar', ['eskul' => $e->name])" action-label="Pilih eskul ini" />
            @endforeach
        </div>
    @endif

    <footer class="rounded-2xl bg-slate-100 p-5 text-sm text-slate-600">
        <p class="font-bold text-slate-800">{{ \App\Models\AppSetting::get('school.name', config('app.name', 'Bagimu Extra')) }}</p>
        <p>{{ \App\Models\AppSetting::get('school.address', '') }}{{ \App\Models\AppSetting::get('school.contact') ? ' • '.\App\Models\AppSetting::get('school.contact') : '' }}</p>
        <div class="mt-3 flex flex-wrap gap-4 border-t border-slate-200 pt-3 text-xs">
            <a href="{{ route('siswa.daftar') }}" class="font-medium text-slate-600 hover:text-blue-700 hover:underline">Portal Siswa</a>
            <a href="{{ route('verify.form') }}" class="font-medium text-slate-600 hover:text-blue-700 hover:underline">Verifikasi E-Card / Sertifikat</a>
        </div>
    </footer>
</div>
</body>
</html>

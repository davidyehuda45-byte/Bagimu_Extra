<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Sertifikat — {{ config('app.name', 'Bagimu Extra') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="mx-auto max-w-xl space-y-4 px-4 py-10">
    <div class="text-center">
        <h1 class="text-xl font-extrabold text-slate-900">Verifikasi Sertifikat Resmi</h1>
        <p class="text-sm text-slate-500">{{ config('app.name', 'Bagimu Extra') }}</p>
    </div>

    <form method="GET" action="{{ route('verify.form') }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <label for="kode" class="block text-sm font-medium text-slate-700">Nomor sertifikat (SRT-…) atau kode pendaftaran</label>
        <div class="mt-2 flex gap-2">
            <input id="kode" name="kode" type="text" value="{{ $kode }}" placeholder="cth: SRT-2026-ABCDEF atau ESK-000000003F"
                   class="min-w-0 flex-1 rounded-xl border-slate-300 font-mono text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <button class="rounded-xl bg-blue-700 px-4 py-2 text-sm font-bold text-white hover:bg-blue-800">Periksa</button>
        </div>
    </form>

    @if ($error)
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5 text-center">
            <p class="font-bold text-rose-700">Kode atau Nomor Tidak Valid</p>
            <p class="mt-1 text-sm text-rose-600">{{ $error }}</p>
        </div>
    @endif

    @if ($registration)
        @if ($registration->card_revoked_at)
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5 text-center">
                <p class="text-lg font-extrabold text-rose-700">Data tidak berlaku</p>
                <p class="mt-1 text-sm text-rose-600">Catatan pendaftaran ini sudah dinonaktifkan atau digantikan.</p>
            </div>
        @else
            @php($st = $registration->status->value)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                @if ($st === 'APPROVED')
                    <p class="text-center text-lg font-extrabold text-emerald-600">TERDAFTAR</p>
                @elseif ($st === 'PENDING')
                    <p class="text-center text-lg font-extrabold text-amber-600">MENUNGGU VERIFIKASI</p>
                @else
                    <p class="text-center text-lg font-extrabold text-rose-600">{{ $registration->status->label() }}</p>
                @endif

                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Nama</dt><dd class="font-semibold text-slate-900 text-right">{{ $registration->user?->name ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Kode</dt><dd class="font-mono font-semibold text-slate-900">{{ $registration->kode() }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Kelas • Jurusan</dt><dd class="font-semibold text-slate-900 text-right">{{ $registration->kelas }} • {{ $registration->jurusan?->short() ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Eskul</dt><dd class="font-semibold text-slate-900 text-right">{{ $registration->eskul_utama }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Periode</dt><dd class="font-semibold text-slate-900">{{ $registration->periode }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Tanggal Daftar</dt><dd class="font-semibold text-slate-900">{{ $registration->created_at->format('d M Y') }}</dd></div>
                    @if ($registration->certificate)
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Sertifikat</dt><dd class="font-mono font-semibold text-slate-900">{{ $registration->certificate->nomor }}</dd></div>
                    @endif
                </dl>

                @if ($jadwals->isNotEmpty())
                    <div class="mt-4 border-t border-slate-100 pt-3">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Jadwal Latihan</p>
                        <ul class="mt-1 space-y-0.5 text-sm text-slate-700">
                            @foreach ($jadwals as $j)
                                <li>{{ ucfirst(strtolower($j->hari)) }} • {{ $j->jam_mulai }}–{{ $j->jam_selesai }} • {{ $j->tempat }}</li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <p class="mt-4 border-t border-slate-100 pt-3 text-xs text-slate-400">Jadwal latihan belum tersedia atau eskul sudah tidak aktif/berganti nama.</p>
                @endif
            </div>
        @endif
    @endif

    <p class="text-center text-[11px] text-slate-400">Diverifikasi otomatis oleh {{ config('app.name', 'Bagimu Extra') }} — {{ \App\Models\AppSetting::get('school.name', '') }}</p>
    <div class="flex flex-wrap items-center justify-center gap-4 text-xs">
        <a href="{{ route('home') }}" class="font-medium text-slate-500 hover:text-blue-700 hover:underline">Beranda</a>
        <a href="{{ route('publik.eskul') }}" class="font-medium text-slate-500 hover:text-blue-700 hover:underline">Katalog Eskul</a>
        <a href="{{ route('siswa.login') }}" class="font-medium text-slate-500 hover:text-blue-700 hover:underline">Portal Siswa</a>
    </div>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi E-Card — {{ config('app.name', 'Bagimu Extra') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="mx-auto max-w-xl space-y-4 px-4 py-10">
    <div class="text-center">
        <h1 class="text-xl font-extrabold text-slate-900">Verifikasi E-Card</h1>
        <p class="text-sm text-slate-500">{{ config('app.name', 'Bagimu Extra') }}</p>
    </div>

    <form method="GET" action="{{ route('verify.form') }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <label for="kode" class="block text-sm font-medium text-slate-700">Kode E-Card (ESK-… atau token)</label>
        <div class="mt-2 flex gap-2">
            <input id="kode" name="kode" type="text" value="{{ $kode }}" placeholder="cth: ESK-000000003F"
                   class="min-w-0 flex-1 rounded-xl border-slate-300 font-mono text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <button class="rounded-xl bg-blue-700 px-4 py-2 text-sm font-bold text-white hover:bg-blue-800">Periksa</button>
        </div>
    </form>

    @if ($error)
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5 text-center">
            <p class="font-bold text-rose-700">QR tidak valid</p>
            <p class="mt-1 text-sm text-rose-600">{{ $error }}</p>
        </div>
    @endif

    @if ($registration)
        @if ($registration->card_revoked_at)
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5 text-center">
                <p class="text-lg font-extrabold text-rose-700">Kartu tidak berlaku</p>
                <p class="mt-1 text-sm text-rose-600">Kartu ini sudah dicabut (cetak ulang/hilang). Minta kartu terbaru kepada siswa.</p>
            </div>
        @else
            @php($st = $registration->status->value)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-center text-lg font-extrabold text-emerald-600">TERDAFTAR</p>

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
                @endif
            </div>
        @endif
    @endif

    <p class="text-center text-[11px] text-slate-400">Diverifikasi otomatis oleh {{ config('app.name', 'Bagimu Extra') }} — {{ \App\Models\AppSetting::get('school.name', '') }}</p>
</div>
</body>
</html>

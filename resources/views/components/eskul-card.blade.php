@props(['eskul', 'showPembina' => false, 'actionUrl' => null, 'actionLabel' => 'Masuk untuk mendaftar'])

<x-card>
    <div class="flex items-center justify-between gap-2">
        <p class="font-bold text-slate-900">{{ $eskul->name }}</p>
        @if ($eskul->kuota_aktif)
            <x-badge tone="amber">Kuota {{ $eskul->kuota }}</x-badge>
        @else
            <x-badge tone="emerald">Bebas</x-badge>
        @endif
    </div>

    @if ($eskul->description)
        <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ $eskul->description }}</p>
    @endif

    @if ($showPembina)
        <p class="mt-1 text-xs text-slate-500">Pembina: {{ $eskul->pembina?->name ?? '—' }}</p>
    @endif

    @if ($eskul->jadwals->isNotEmpty())
        <ul class="mt-2 space-y-0.5 text-xs text-slate-500">
            @foreach ($eskul->jadwals->take($showPembina ? 10 : 2) as $j)
                <li>{{ ucfirst(strtolower($j->hari)) }} {{ $j->jam_mulai }}–{{ $j->jam_selesai }} @ {{ $j->tempat }}</li>
            @endforeach
            @if (! $showPembina && $eskul->jadwals->count() > 2)
                <li class="text-slate-400">+{{ $eskul->jadwals->count() - 2 }} sesi lainnya</li>
            @endif
        </ul>
    @else
        <p class="mt-2 text-xs text-slate-400">Jadwal menyusul.</p>
    @endif

    @if ($actionUrl)
        <a href="{{ $actionUrl }}" class="mt-3 inline-block rounded-xl bg-blue-700 px-4 py-2 text-xs font-bold text-white hover:bg-blue-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">{{ $actionLabel }}</a>
    @endif

    {{ $slot }}
</x-card>

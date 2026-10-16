@props(['eskul', 'showPembina' => false, 'actionUrl' => null, 'actionLabel' => 'Masuk untuk mendaftar'])

<div class="group flex flex-col justify-between rounded-2xl sm:rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs hover:border-blue-300 hover:shadow-sm hover-lift transition">
    <div>
        <div class="flex items-start justify-between gap-2">
            <h3 class="font-bold text-base text-slate-900 group-hover:text-blue-600 transition-colors">{{ $eskul->name }}</h3>
            @if ($eskul->kuota_aktif)
                <x-badge tone="amber" dot="true">Kuota {{ $eskul->kuota }}</x-badge>
            @else
                <x-badge tone="emerald" dot="true">Kuota Terbuka</x-badge>
            @endif
        </div>

        @if ($eskul->description)
            <p class="mt-2 line-clamp-2 text-xs sm:text-sm text-slate-600 leading-relaxed">{{ $eskul->description }}</p>
        @endif

        @if ($showPembina)
            <div class="mt-2.5 flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                <x-icon name="user" class="h-3.5 w-3.5 text-slate-400" />
                <span>Pembina: <strong class="text-slate-800 font-semibold">{{ $eskul->pembina?->name ?? 'Belum ditentukan' }}</strong></span>
            </div>
        @endif

        @if ($eskul->jadwals->isNotEmpty())
            <div class="mt-3.5 pt-3 border-t border-slate-100">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Jadwal Rutin</p>
                <ul class="space-y-1 text-xs text-slate-600">
                    @foreach ($eskul->jadwals->take($showPembina ? 10 : 2) as $j)
                        <li class="flex items-center gap-1.5">
                            <span class="inline-flex items-center rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold text-slate-700">
                                {{ ucfirst(strtolower($j->hari)) }}
                            </span>
                            <span>{{ $j->jam_mulai }}–{{ $j->jam_selesai }}</span>
                            <span class="text-slate-400">@</span>
                            <span class="font-medium text-slate-700 truncate">{{ $j->tempat }}</span>
                        </li>
                    @endforeach
                    @if (! $showPembina && $eskul->jadwals->count() > 2)
                        <li class="text-[11px] font-medium text-blue-600 pt-0.5">+{{ $eskul->jadwals->count() - 2 }} sesi latihan lainnya</li>
                    @endif
                </ul>
            </div>
        @else
            <div class="mt-3.5 pt-3 border-t border-slate-100">
                <p class="text-xs text-slate-400 italic">Jadwal latihan akan segera diumumkan.</p>
            </div>
        @endif
    </div>

    @if ($actionUrl)
        <div class="mt-4 pt-2">
            <a href="{{ $actionUrl }}" class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700 transition shadow-xs shadow-blue-600/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                <span>{{ $actionLabel }}</span>
                <x-icon name="arrow-right" class="h-3.5 w-3.5" />
            </a>
        </div>
    @endif

    {{ $slot }}
</div>

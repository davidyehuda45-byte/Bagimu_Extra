@props(['items' => [], 'max' => null])

@php
$vals = collect($items)->pluck('value')->map(fn ($v) => (int) $v);
$peak = $max ?? max(1, $vals->max() ?? 1);
@endphp

<ul {{ $attributes->merge(['class' => 'space-y-2.5']) }}>
    @forelse ($items as $row)
        @php($pct = $peak > 0 ? min(100, round(($row['value'] / $peak) * 100)) : 0)
        <li>
            <div class="flex items-center justify-between gap-2 text-sm">
                <span class="truncate font-medium text-slate-800">{{ $row['label'] }}
                    @if (! empty($row['suffix']))
                        <span class="ml-1 font-semibold text-slate-500">{{ $row['suffix'] }}</span>
                    @endif
                </span>
                <span class="shrink-0 text-xs tabular-nums text-slate-500">{{ $row['display'] ?? $row['value'] }}</span>
            </div>
            <x-progress :value="$pct" tone="{{ $row['tone'] ?? 'blue' }}" class="mt-1 !h-2" aria-label="{{ $row['label'] }} {{ $row['display'] ?? $row['value'] }}" />
        </li>
    @empty
        <li><x-empty-state title="Belum ada data" message="Data distribusi akan muncul di sini." /></li>
    @endforelse
</ul>

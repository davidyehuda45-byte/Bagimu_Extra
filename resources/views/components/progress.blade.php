@props(['value' => 0, 'tone' => null])

@php
$value = max(0, min(100, (int) $value));
$auto = $value >= 85 ? 'bg-emerald-500' : ($value >= 70 ? 'bg-amber-400' : 'bg-rose-500');
$bar = $tone ? match ($tone) {
    'blue' => 'bg-blue-600',
    'emerald' => 'bg-emerald-500',
    'amber' => 'bg-amber-400',
    'rose' => 'bg-rose-500',
    default => 'bg-blue-600',
} : $auto;
@endphp

<div {{ $attributes->merge(['class' => 'h-2.5 overflow-hidden rounded-full bg-slate-100']) }} role="progressbar" aria-valuenow="{{ $value }}" aria-valuemin="0" aria-valuemax="100" aria-label="{{ $attributes->get('aria-label', 'Progress') }}">
    <div class="{{ $bar }} h-full rounded-full bar-animate" style="width: {{ $value }}%"></div>
</div>

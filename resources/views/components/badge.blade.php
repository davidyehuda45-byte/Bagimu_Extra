@props(['tone' => 'slate', 'dot' => false])

@php
$tones = [
    'blue' => 'bg-blue-50 text-blue-700 ring-blue-600/15 border border-blue-200/60',
    'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/15 border border-emerald-200/60',
    'amber' => 'bg-amber-50 text-amber-800 ring-amber-600/15 border border-amber-200/60',
    'rose' => 'bg-rose-50 text-rose-700 ring-rose-600/15 border border-rose-200/60',
    'slate' => 'bg-slate-100 text-slate-700 ring-slate-400/15 border border-slate-200/60',
];
$dotColors = [
    'blue' => 'bg-blue-600',
    'emerald' => 'bg-emerald-600',
    'amber' => 'bg-amber-600',
    'rose' => 'bg-rose-600',
    'slate' => 'bg-slate-400',
];
$cls = $tones[$tone] ?? $tones['slate'];
$dotCls = $dotColors[$tone] ?? $dotColors['slate'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-bold whitespace-nowrap tracking-wide $cls"]) }}>
    @if($dot)
        <span class="h-1.5 w-1.5 rounded-full {{ $dotCls }}"></span>
    @endif
    {{ $slot }}
</span>

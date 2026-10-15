@props(['tone' => 'slate'])

@php
$tones = [
    'blue' => 'bg-blue-100 text-blue-800 ring-blue-600/20',
    'emerald' => 'bg-emerald-100 text-emerald-800 ring-emerald-600/20',
    'amber' => 'bg-amber-100 text-amber-800 ring-amber-600/20',
    'rose' => 'bg-rose-100 text-rose-800 ring-rose-600/20',
    'slate' => 'bg-slate-100 text-slate-600 ring-slate-500/20',
];
$cls = $tones[$tone] ?? $tones['slate'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold whitespace-nowrap ring-1 ring-inset $cls"]) }}>
    {{ $slot }}
</span>

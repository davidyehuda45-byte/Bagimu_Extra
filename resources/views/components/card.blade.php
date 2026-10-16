@props(['pad' => 'p-5 sm:p-6'])

<div {{ $attributes->merge(['class' => "rounded-2xl sm:rounded-3xl border border-slate-200/90 bg-white shadow-[0_1px_3px_0_rgba(0,0,0,0.03),0_1px_2px_-1px_rgba(0,0,0,0.03)] transition-all duration-200 $pad"]) }}>
    {{ $slot }}
</div>

@props(['pad' => 'p-4'])

<div {{ $attributes->merge(['class' => "rounded-2xl border border-slate-200 bg-white shadow-sm $pad"]) }}>
    {{ $slot }}
</div>

@props(['icon' => 'inbox', 'title' => '', 'message' => ''])

<div {{ $attributes->merge(['class' => 'py-6 text-center']) }}>
    <p class="mx-auto flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
        <x-icon name="{{ $icon }}" class="h-5 w-5" />
    </p>
    @if ($title)
        <p class="mt-2 text-sm font-bold text-slate-800">{{ $title }}</p>
    @endif
    @if ($message)
        <p class="mt-1 text-sm text-slate-500">{{ $message }}</p>
    @endif
    {{ $slot }}
</div>

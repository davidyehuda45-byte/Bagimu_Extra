@props(['label' => 'Menu', 'panelClass' => 'w-80'])

<div x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
    <div @click="open = ! open" :aria-expanded="open.toString()">
        {{ $trigger }}
    </div>

    <div x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        x-cloak
        class="absolute right-0 z-50 mt-2 {{ $panelClass }} rounded-2xl border border-slate-200 bg-white p-3 shadow-xl ring-1 ring-black/5"
        role="menu"
        :aria-hidden="(! open).toString()"
        @click="if ($event.target.closest('a')) open = false">
        {{ $slot }}
    </div>
</div>

@if (session('status'))
    <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-4 flex items-start justify-between gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900 shadow-sm">
        <div class="flex items-start gap-3">
            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-200 text-emerald-800">
                <x-icon name="check" class="h-3.5 w-3.5" />
            </span>
            <div class="leading-relaxed font-medium">{{ session('status') }}</div>
        </div>
        <button type="button" @click="show = false" class="text-emerald-600 hover:text-emerald-900 leading-none" aria-label="Tutup">
            <x-icon name="x" class="h-4 w-4" />
        </button>
    </div>
@endif

@if (session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-4 flex items-start justify-between gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900 shadow-sm">
        <div class="flex items-start gap-3">
            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-rose-200 text-rose-800">
                <x-icon name="x" class="h-3.5 w-3.5" />
            </span>
            <div class="leading-relaxed font-medium">{{ session('error') }}</div>
        </div>
        <button type="button" @click="show = false" class="text-rose-600 hover:text-rose-900 leading-none" aria-label="Tutup">
            <x-icon name="x" class="h-4 w-4" />
        </button>
    </div>
@endif

@if (session('warning'))
    <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-4 flex items-start justify-between gap-3 rounded-2xl border border-blue-200 bg-blue-50/80 p-4 text-sm text-slate-800 shadow-sm">
        <div class="flex items-start gap-3">
            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-200 text-blue-800">
                <x-icon name="info" class="h-3.5 w-3.5" />
            </span>
            <div class="leading-relaxed font-medium">{{ session('warning') }}</div>
        </div>
        <button type="button" @click="show = false" class="text-blue-600 hover:text-blue-900 leading-none" aria-label="Tutup">
            <x-icon name="x" class="h-4 w-4" />
        </button>
    </div>
@endif

@if ($errors->any())
    <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-4 flex items-start justify-between gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900 shadow-sm">
        <div class="flex items-start gap-3">
            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-rose-200 text-rose-800">
                <x-icon name="x" class="h-3.5 w-3.5" />
            </span>
            <div>
                <p class="font-bold mb-1">Terjadi kesalahan pada input:</p>
                <ul class="list-disc ps-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" @click="show = false" class="text-rose-600 hover:text-rose-900 leading-none" aria-label="Tutup">
            <x-icon name="x" class="h-4 w-4" />
        </button>
    </div>
@endif


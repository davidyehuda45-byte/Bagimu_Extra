@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-300 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/10 rounded-xl shadow-2xs text-sm transition-all duration-150 disabled:bg-slate-50 disabled:text-slate-400']) }}>

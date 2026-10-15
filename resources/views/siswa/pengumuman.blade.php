@extends('layouts.siswa')

@section('title', 'Pengumuman')

@section('content')
<div class="space-y-4">
    <div>
        <h1 class="text-lg font-extrabold text-slate-900">Pengumuman</h1>
        <p class="text-sm text-slate-500">Info & agenda terbaru dari sekolah.</p>
    </div>

    <x-card pad="!p-0 overflow-hidden">
        @if ($daftar->isEmpty())
            <x-empty-state icon="megaphone" title="Belum ada pengumuman" message="Pengumuman terbaru akan muncul di sini." />
        @else
            <ul class="divide-y divide-slate-100">
                @foreach ($daftar as $n)
                    <li class="p-4 {{ $n->dibaca_at ? '' : 'bg-blue-50/60' }}">
                        <div class="flex items-center gap-1.5">
                            @if (! $n->dibaca_at)
                                <span class="h-2 w-2 shrink-0 rounded-full bg-blue-600"></span>
                            @endif
                            <p class="text-sm font-bold text-slate-900">{{ $n->judul }}</p>
                        </div>
                        <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $n->isi }}</p>
                        <p class="mt-1 text-[11px] text-slate-400">{{ $n->created_at->format('d M Y H:i') }}</p>
                    </li>
                @endforeach
            </ul>
            <div class="border-t border-slate-100 p-4">
                {{ $daftar->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection

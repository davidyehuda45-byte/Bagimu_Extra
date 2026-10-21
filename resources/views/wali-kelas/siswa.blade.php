@extends('layouts.staff')

@section('title', 'Siswa Rombel — Wali Kelas')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Direktori Siswa Rombel {{ $kelas?->nama }}</h1>
            <p class="text-xs text-slate-500">Daftar siswa dalam rombel Anda dan status ekstrakurikuler yang diikuti.</p>
        </div>
        <form method="GET" action="{{ route('wali-kelas.siswa') }}" class="flex items-center gap-2">
            @if (auth()->user()->isAdmin() && $allClasses->count() > 1)
                <select name="kelas_id" class="border-slate-300 rounded-xl px-3 py-1.5 text-xs font-semibold focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                    @foreach ($allClasses as $c)
                        <option value="{{ $c->id }}" @selected($kelas && $kelas->id === $c->id)>{{ $c->nama }}</option>
                    @endforeach
                </select>
            @endif
            <select name="periode" class="border-slate-300 rounded-xl px-3 py-1.5 text-xs font-semibold focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                @foreach ($periodes as $p)
                    <option value="{{ $p->periode }}" @selected($periode && $periode->periode === $p->periode)>{{ $p->periode }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if (! $kelas)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-xs font-semibold text-amber-800 flex items-center gap-3">
            <x-icon name="alert-circle" class="h-5 w-5 text-amber-600 shrink-0" />
            <div>
                <p class="font-bold text-sm text-amber-900">Akun Anda Belum Ditugaskan ke Rombel Kelas</p>
                <p class="mt-0.5 text-amber-700">Hubungi Administrator sekolah untuk menautkan akun Anda ke rombel binaan di menu <b>Kelola Wali Kelas</b>.</p>
            </div>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
        <div class="overflow-x-auto rounded-xl border border-slate-100">
            <table class="min-w-full text-xs">
                <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider font-bold text-[11px]">
                    <tr>
                        <th class="py-2.5 px-3 text-center">No</th>
                        <th class="py-2.5 px-4 text-left">NIS</th>
                        <th class="py-2.5 px-4 text-left">Nama Siswa</th>
                        <th class="py-2.5 px-4 text-center">L/P</th>
                        <th class="py-2.5 px-4 text-left">Ekstrakurikuler Diikuti</th>
                        <th class="py-2.5 px-4 text-left">No. WhatsApp</th>
                        <th class="py-2.5 px-4 text-center">Status Siswa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($students as $index => $s)
                        @php $reg = $regPeta->get($s->nis); @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-2.5 px-3 text-center font-bold text-slate-400">{{ $s->no_absen ?? ($index + 1) }}</td>
                            <td class="py-2.5 px-4 font-mono font-bold text-slate-800">{{ $s->nis }}</td>
                            <td class="py-2.5 px-4 font-semibold text-slate-900">{{ $s->nama }}</td>
                            <td class="py-2.5 px-4 text-center font-bold text-slate-600">{{ $s->gender?->short() ?? '-' }}</td>
                            <td class="py-2.5 px-4">
                                @if ($reg)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-bold bg-blue-100 text-blue-800 text-[11px]">
                                        <x-icon name="check" class="h-3 w-3" />
                                        {{ $reg->eskul_utama }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md font-semibold bg-slate-100 text-slate-500 text-[10px]">
                                        Belum Mendaftar
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-slate-600 font-mono">
                                @if ($s->whatsapp)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $s->whatsapp) }}" target="_blank" class="text-emerald-700 font-semibold hover:underline">
                                        {{ $s->whatsapp }}
                                    </a>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $s->status === 'AKTIF' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $s->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                Tidak ada data siswa di rombel ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


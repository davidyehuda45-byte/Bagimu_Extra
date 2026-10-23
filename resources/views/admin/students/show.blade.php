@extends('layouts.staff')

@section('title', 'Detail Siswa')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-bold text-slate-900">Siswa — {{ $student->nama }}</h1>
    <div class="flex gap-2">
        <a href="{{ route('admin.students.edit', $student) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold transition shadow-xs"><x-icon name="pencil" class="h-4 w-4" /> Ubah</a>
        <a href="{{ route('admin.students.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-sm bg-white">Kembali</a>
    </div>
</div>

<div class="rounded-2xl border border-slate-200 bg-white p-5 mb-4 text-sm shadow-sm">
    <div class="flex items-center gap-2 mb-3">
        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $student->status === 'AKTIF' ? 'bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-600/20' : 'bg-amber-100 text-amber-800 ring-1 ring-inset ring-amber-600/20' }}">{{ $student->status }}</span>
        <span class="font-mono text-xs text-slate-500">NIS {{ $student->nis }}</span>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-1">
        <p><span class="text-slate-500">NIS:</span> {{ $student->nis }}</p>
        <p><span class="text-slate-500">NISN:</span> {{ $student->nisn ?? '—' }}</p>
        <p><span class="text-slate-500">Nama:</span> {{ $student->nama }}</p>
        <p><span class="text-slate-500">Kelas:</span> {{ $student->kelas }}</p>
        <p><span class="text-slate-500">Jurusan:</span> {{ $student->jurusan?->label() ?? '-' }}</p>
        <p><span class="text-slate-500">Jenis Kelamin:</span> {{ $student->gender?->label() ?? '-' }}</p>
        <p><span class="text-slate-500">No. Absen:</span> {{ $student->no_absen ?? '-' }}</p>
        <p><span class="text-slate-500">Akun:</span> {{ $student->user ? '@'.$student->user->username : 'Belum ada' }}</p>
        <p><span class="text-slate-500">Wali:</span> {{ $student->wali_nama ?? '—' }}{{ $student->wali_kontak ? ' • '.$student->wali_kontak : '' }}</p>
        <p><span class="inline-flex items-center gap-1 text-slate-500"><x-icon name="phone" class="h-3.5 w-3.5" />HP / WA Siswa:</span> {{ $student->whatsapp ?? '—' }}</p>
    </div>
</div>

<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <h2 class="font-bold text-sm text-slate-900 mb-3">Riwayat Eskul Lintas Periode</h2>
    @if ($riwayat->isEmpty())
        <p class="text-sm text-slate-500">Belum pernah mendaftar.</p>
    @else
        <ul class="space-y-2 text-sm">
            @foreach ($riwayat as $r)
                <li class="flex flex-wrap items-center gap-2 border-b border-slate-100 pb-2 last:border-0">
                    <span class="font-semibold text-slate-900">{{ $r->periode }}</span>
                    <span class="text-slate-600">{{ $r->kelas }} • {{ $r->eskul_utama }}</span>
                    <span class="ms-auto inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $r->status->value === 'APPROVED' ? 'bg-emerald-100 text-emerald-800' : ($r->status->value === 'PENDING' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">{{ $r->status->label() }}</span>
                    <span class="font-mono text-[11px] text-slate-400">NIS {{ $r->nis }}</span>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection

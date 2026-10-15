@extends('layouts.staff')

@section('title', 'Undang Pembina')

@section('content')
<h1 class="text-xl font-bold text-slate-900 mb-1">Undang Pembina Baru</h1>
<p class="text-sm text-slate-500 mb-4">Buat akun untuk pembina ekstrakurikuler atau pengelola sistem. Kredensial login (username & password) akan langsung ditampilkan dan dikirimkan ke email yang dituju.</p>

<form method="POST" action="{{ route('admin.invitations.store') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm w-full space-y-4" x-data="{ peran: '{{ old('role', 'PEMBINA') }}' }">
    @csrf
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap & Gelar</label>
        <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso, S.Pd." required maxlength="100" class="w-full border-slate-300 rounded-xl text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Email Resmi (untuk menerima undangan)</label>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@sekolah.sch.id" required class="w-full border-slate-300 rounded-xl text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Peran / Penugasan</label>
        <select name="role" x-model="peran" class="w-full border-slate-300 rounded-xl text-sm">
            <option value="PEMBINA">Pembina Ekstrakurikuler</option>
            <option value="OPERATOR">Operator Sistem</option>
            <option value="ADMIN">Administrator Penuh</option>
            <option value="KEPALA_SEKOLAH">Kepala Sekolah (Read-only)</option>
        </select>
    </div>
    <div x-show="peran === 'PEMBINA'">
        <span class="block text-sm font-medium text-slate-700 mb-1">Eskul yang Diampu <span class="font-normal text-slate-400">(langsung tugaskan ke pembina ini)</span></span>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 rounded-xl border border-slate-200 bg-slate-50 p-3">
            @forelse ($eskuls as $e)
                <label class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm text-slate-700 hover:bg-white">
                    <input type="checkbox" name="eskul_ids[]" value="{{ $e->id }}" @checked(in_array($e->id, old('eskul_ids', []))) class="rounded border-slate-300 text-blue-700 focus:ring-blue-500">
                    <span>{{ $e->name }}</span>
                    @if ($e->pembina_id)
                        <span class="text-[10px] text-amber-600">(ada pembina — akan diganti)</span>
                    @endif
                </label>
            @empty
                <p class="text-xs text-slate-400">Belum ada eskul. Buat dulu di Master Eskul.</p>
            @endforelse
        </div>
    </div>
    <button class="rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-blue-800 transition shadow-sm">Buat & Undang Pembina</button>
</form>
@endsection

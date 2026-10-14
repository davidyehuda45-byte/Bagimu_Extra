@extends('layouts.staff')

@section('title', 'Undang Pembina')

@section('content')
<h1 class="text-xl font-bold text-slate-900 mb-1">Undang Pembina Baru</h1>
<p class="text-sm text-slate-500 mb-4">Buat akun untuk pembina ekstrakurikuler atau pengelola sistem. Kredensial login (username & password) akan langsung ditampilkan dan dikirimkan ke email yang dituju.</p>

<form method="POST" action="{{ route('admin.invitations.store') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm w-full space-y-4">
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
        <select name="role" class="w-full border-slate-300 rounded-xl text-sm">
            <option value="PEMBINA">Pembina Ekstrakurikuler</option>
            <option value="OPERATOR">Operator Sistem</option>
            <option value="ADMIN">Administrator Penuh</option>
            <option value="KEPALA_SEKOLAH">Kepala Sekolah (Read-only)</option>
        </select>
    </div>
    <button class="rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-blue-800 transition shadow-sm">Buat & Undang Pembina</button>
</form>
@endsection

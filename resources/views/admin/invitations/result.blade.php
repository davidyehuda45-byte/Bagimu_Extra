@extends('layouts.staff')

@section('title', 'Undangan Terkirim')

@section('content')
<h1 class="text-xl font-bold text-slate-900 mb-4">Undangan Dibuat</h1>

<div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm w-full">
    <p class="text-sm text-emerald-800">Status email: <b>{{ $mailStatus }}</b></p>
    <p class="mt-1 text-xs text-emerald-700">Catat/salin kredensial di bawah SEKARANG — password tidak ditampilkan lagi.</p>
</div>

<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm w-full mt-4 text-sm">
    <dl class="space-y-2">
        <div class="flex justify-between gap-3"><dt class="text-slate-500">Nama</dt><dd class="font-semibold">{{ $user->name }}</dd></div>
        <div class="flex justify-between gap-3"><dt class="text-slate-500">Email</dt><dd class="font-semibold">{{ $user->email }}</dd></div>
        <div class="flex justify-between gap-3"><dt class="text-slate-500">Peran</dt><dd class="font-semibold">{{ $user->role->label() }}</dd></div>
        <div class="flex justify-between gap-3"><dt class="text-slate-500">Username</dt><dd class="font-mono font-bold" id="cred-user">{{ $username }}</dd></div>
        <div class="flex justify-between gap-3"><dt class="text-slate-500">Password</dt><dd class="font-mono font-bold tracking-widest" id="cred-pass">{{ $password }}</dd></div>
        <div class="flex justify-between gap-3"><dt class="text-slate-500">Link login</dt><dd><a href="{{ $loginUrl }}" class="font-medium text-blue-600 hover:underline break-all">{{ $loginUrl }}</a></dd></div>
    </dl>
    <div class="mt-4 flex flex-wrap gap-2">
        <button type="button" onclick="navigator.clipboard.writeText('Username: {{ $username }}\nPassword: {{ $password }}\nLogin: {{ $loginUrl }}'); this.textContent = 'Tersalin!';" class="rounded-xl bg-blue-700 px-4 py-2 text-sm font-bold text-white hover:bg-blue-800">Salin Kredensial</button>
        <a href="{{ $loginUrl }}" target="_blank" rel="noopener" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold hover:bg-slate-50">Tes Login →</a>
        <a href="{{ route('admin.invitations.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold hover:bg-slate-50">Undang Lagi</a>
    </div>
</div>
@endsection

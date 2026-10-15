@extends('layouts.staff')

@section('title', 'Kelola Pembina')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Kelola Pembina</h1>
        <p class="text-sm text-slate-500">Kelola Akun Staf Pembina ekstrakurikuler dan penugasan administrator sistem.</p>
    </div>
    <div x-data="{ openForm: false }">
        <button type="button" @click="openForm = !openForm" class="rounded-xl bg-blue-700 px-4 py-2 text-sm font-bold text-white hover:bg-blue-800 transition shadow-sm inline-flex items-center gap-1.5">
            <span x-text="openForm ? '✕ Tutup Form' : '+ Tambah Pembina Baru'"></span>
        </button>

        {{-- Form Tambah Pembina Modal / Drawer --}}
        <div x-show="openForm" x-transition class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-md">
            <h2 class="text-base font-bold text-slate-900 mb-3">Tambah Data Pembina Baru</h2>
            <form method="POST" action="{{ route('admin.users.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm" x-data="{ peran: 'PEMBINA' }">
                @csrf
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Nama Lengkap & Gelar</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso, S.Pd." required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Email Resmi</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@sekolah.sch.id" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Username (Opsional)</label>
                    <input type="text" name="username" value="{{ old('username') }}" placeholder="username_pembina" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Peran / Role</label>
                    <select name="role" x-model="peran" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:outline-none">
                        <option value="PEMBINA">Pembina Ekstrakurikuler</option>
                        <option value="OPERATOR">Operator Sistem</option>
                        <option value="KEPALA_SEKOLAH">Kepala Sekolah (Read-only)</option>
                        <option value="ADMIN">Administrator Penuh</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block font-medium text-slate-700 mb-1">Password Awal (Min. 8 karakter)</label>
                    <input type="password" name="password" required minlength="8" placeholder="••••••••" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:outline-none">
                </div>
                <div class="sm:col-span-2" x-show="peran === 'PEMBINA'">
                    <span class="block font-medium text-slate-700 mb-1">Eskul yang Diampu</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 rounded-xl border border-slate-200 bg-slate-50 p-3">
                        @forelse ($eskuls as $e)
                            <label class="flex items-center gap-2 rounded-lg px-2 py-1 text-sm text-slate-700 hover:bg-white">
                                <input type="checkbox" name="eskul_ids[]" value="{{ $e->id }}" class="rounded border-slate-300 text-blue-700 focus:ring-blue-500">
                                <span>{{ $e->name }}</span>
                                @if ($e->pembina_id)
                                    <span class="text-[10px] text-amber-600">(ada pembina — akan diganti)</span>
                                @endif
                            </label>
                        @empty
                            <p class="text-xs text-slate-400">Belum ada eskul.</p>
                        @endforelse
                    </div>
                </div>
                <div class="sm:col-span-2 flex justify-end gap-2">
                    <button type="button" @click="openForm = false" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-blue-700 px-5 py-2 text-xs font-bold text-white hover:bg-blue-800 shadow-sm">Simpan Data Pembina</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Tabel Daftar Pembina --}}
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Nama & Email</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Username</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Peran Aktif</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Eskul Diampu</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Ubah Peran</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($users as $u)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-4 py-3">
                            <p class="font-bold text-slate-900">{{ $u->name }}</p>
                            <p class="text-xs text-slate-500">{{ $u->email }}</p>
                        </td>
                        <td class="px-4 py-3 text-slate-600 font-mono text-xs">
                            {{ $u->username ?? '-' }}
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $roleBadge = match($u->role->value ?? (string)$u->role) {
                                    'ADMIN' => 'bg-rose-100 text-rose-800 border-rose-200',
                                    'OPERATOR' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    'PEMBINA' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'KEPALA_SEKOLAH' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    default => 'bg-slate-100 text-slate-800 border-slate-200',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $roleBadge }}">
                                {{ $u->role->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if (($u->role->value ?? (string) $u->role) === 'PEMBINA')
                                @if ($u->eskulDiampu->isNotEmpty())
                                    <ul class="space-y-0.5 text-xs text-slate-700">
                                        @foreach ($u->eskulDiampu as $e)
                                            <li class="flex items-center gap-1"><x-icon name="trophy" class="h-3 w-3 text-amber-600" />{{ $e->name }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-xs text-amber-600">Belum ditugaskan</span>
                                @endif
                            @else
                                <span class="text-xs text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($u->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.role', $u) }}" class="flex items-center gap-1.5">
                                    @csrf
                                    @method('PUT')
                                    <select name="role" class="rounded-lg border border-slate-300 py-1 px-2 text-xs text-slate-700 focus:outline-none focus:border-blue-600">
                                        <option value="PEMBINA" @selected($u->role->value === 'PEMBINA')>Pembina</option>
                                        <option value="OPERATOR" @selected($u->role->value === 'OPERATOR')>Operator</option>
                                        <option value="KEPALA_SEKOLAH" @selected($u->role->value === 'KEPALA_SEKOLAH')>Kepsek</option>
                                        <option value="ADMIN" @selected($u->role->value === 'ADMIN')>Admin</option>
                                    </select>
                                    <button type="submit" class="rounded-lg border border-slate-300 bg-white px-2 py-1 text-xs font-medium text-slate-700 hover:bg-slate-100">Ubah</button>
                                </form>
                            @else
                                <span class="text-xs text-slate-400 italic">Akun Anda</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2" x-data="{ showReset: false }">
                                {{-- Trigger reset password --}}
                                <button type="button" @click="showReset = !showReset" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100 shadow-sm" title="Reset Kata Sandi">
                                    <x-icon name="key" class="h-3.5 w-3.5" /> Reset Sandi
                                </button>

                                {{-- Form Hapus --}}
                                @if ($u->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Hapus akun {{ $u->name }} ({{ $u->email }})?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg bg-rose-50 border border-rose-200 px-2.5 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-100 shadow-sm">
                                            Hapus
                                        </button>
                                    </form>
                                @endif

                                {{-- Inline Reset Password Modal/Drawer --}}
                                <div x-show="showReset" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                                    <div @click.away="showReset = false" class="w-full max-w-sm rounded-2xl border border-slate-200 bg-white p-5 shadow-2xl">
                                        <h3 class="text-base font-bold text-slate-900 mb-1">Reset Password Pembina</h3>
                                        <p class="text-xs text-slate-500 mb-3">{{ $u->name }} ({{ $u->email }})</p>
                                        <form method="POST" action="{{ route('admin.users.password', $u) }}">
                                            @csrf
                                            @method('PUT')
                                            <label class="block text-xs font-medium text-slate-700 mb-1">Password Baru (min. 8)</label>
                                            <input type="text" name="password" required minlength="8" placeholder="PasswordBaru123" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm mb-3">
                                            <div class="flex justify-end gap-2">
                                                <button type="button" @click="showReset = false" class="rounded-xl border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-600">Batal</button>
                                                <button type="submit" class="rounded-xl bg-blue-700 px-4 py-1.5 text-xs font-bold text-white hover:bg-blue-800">Simpan Password</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center">
                            <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                <x-icon name="users" class="h-5 w-5" />
                            </div>
                            <p class="text-sm font-semibold text-slate-700">Belum ada data pembina</p>
                            <p class="text-xs text-slate-400 mt-0.5">Tambahkan data pembina ekstrakurikuler untuk ditugaskan pada Master Eskul.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-100">{{ $users->links() }}</div>
</div>
@endsection

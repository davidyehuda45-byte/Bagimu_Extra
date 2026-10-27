@extends('layouts.staff')

@section('title', 'Kelola Pembina Ekstrakurikuler')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Kelola Pembina Ekstrakurikuler</h1>
        <p class="text-sm text-slate-500">Kelola akun staf pembina ekstrakurikuler dan penugasan eskul binaan (1 pembina = 1 eskul).</p>
    </div>
    <div x-data="{ openForm: false }">
        <button type="button" @click="openForm = !openForm" class="rounded-xl bg-blue-700 px-4 py-2 text-sm font-bold text-white hover:bg-blue-800 transition shadow-sm inline-flex items-center gap-1.5 cursor-pointer">
            <span x-text="openForm ? 'Tutup Form' : '+ Tambah Pembina Baru'"></span>
        </button>

        {{-- Form Tambah Pembina --}}
        <div x-show="openForm" x-transition class="mt-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-md" style="display: none;">
            <h2 class="text-base font-bold text-slate-900 mb-3">Tambah Data Pembina Baru</h2>
            <form method="POST" action="{{ route('admin.users.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                @csrf
                <input type="hidden" name="role" value="PEMBINA">

                <div>
                    <label class="block font-semibold text-slate-700 text-xs mb-1">Nama Lengkap & Gelar</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso, S.Pd." required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 text-xs mb-1">Email Resmi</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="pembina@sekolah.sch.id" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 text-xs mb-1">Username Login</label>
                    <input type="text" name="username" value="{{ old('username') }}" placeholder="pembina_futsal" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 text-xs mb-1">Eskul yang Dibina (1 Pembina = 1 Eskul)</label>
                    <select name="eskul_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:outline-none">
                        <option value="">-- Belum Ditugaskan / Pilih Nanti --</option>
                        @foreach ($eskuls as $e)
                            <option value="{{ $e->id }}">
                                {{ $e->name }} {{ $e->pembina_id ? '(Sudah ada pembina)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block font-semibold text-slate-700 text-xs mb-1">Password Awal (Min. 8 karakter)</label>
                    <input type="password" name="password" required minlength="8" placeholder="••••••••" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:outline-none">
                </div>

                <div class="sm:col-span-2 flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="openForm = false" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-blue-700 px-5 py-2 text-xs font-bold text-white hover:bg-blue-800 shadow-sm">Simpan Akun Pembina</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Tabel Daftar Pembina --}}
<div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3 text-left font-semibold text-slate-700 text-xs">Nama & Email Pembina</th>
                    <th class="px-5 py-3 text-left font-semibold text-slate-700 text-xs">Username</th>
                    <th class="px-5 py-3 text-left font-semibold text-slate-700 text-xs">Eskul yang Dibina</th>
                    <th class="px-5 py-3 text-left font-semibold text-slate-700 text-xs">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($users as $u)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-5 py-3.5">
                            <p class="font-bold text-slate-900 text-sm">{{ $u->name }}</p>
                            <p class="text-xs text-slate-500 font-mono">{{ $u->email }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-slate-600 font-mono text-xs">
                            {{ $u->username ?? '-' }}
                        </td>
                        <td class="px-5 py-3.5">
                            @if ($u->eskulDiampu->isNotEmpty())
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                        <x-icon name="trophy" class="h-3.5 w-3.5 text-blue-600" />
                                        {{ $u->eskulDiampu->first()->name }}
                                    </span>
                                </div>
                            @else
                                <span class="inline-flex items-center rounded-lg bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                                    Belum ditugaskan
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-1.5" x-data="{ showReset: false }">
                                <button type="button" @click="showReset = !showReset" class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 transition shadow-2xs" title="Reset Sandi">
                                    <x-icon name="key" class="h-4 w-4" />
                                </button>

                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}" data-confirm="Hapus akun pembina {{ $u->name }}? Tindakan ini tidak dapat dibatalkan." data-confirm-title="Hapus Akun Pembina" data-confirm-btn="Hapus Akun">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-100 hover:border-rose-300 transition shadow-2xs cursor-pointer" title="Hapus Akun">
                                        <x-icon name="trash" class="h-4 w-4" />
                                    </button>
                                </form>

                                {{-- Modal / Dialog Reset Password Inline --}}
                                <div x-show="showReset" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs" style="display: none;" @keydown.escape.window="showReset = false">
                                    <div class="w-full max-w-sm rounded-3xl bg-white p-6 shadow-2xl border border-slate-200" @click.outside="showReset = false">
                                        <h3 class="font-bold text-slate-900 text-sm mb-2">Reset Kata Sandi</h3>
                                        <p class="text-xs text-slate-500 mb-4">Ubah password akun {{ $u->name }} ({{ $u->email }}):</p>
                                        <form method="POST" action="{{ route('admin.users.password', $u) }}" class="space-y-3">
                                            @csrf
                                            @method('PUT')
                                            <div>
                                                <input type="password" name="password" required minlength="8" placeholder="Password baru (min. 8)" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:outline-none">
                                            </div>
                                            <div class="flex justify-end gap-2 pt-2">
                                                <button type="button" @click="showReset = false" class="rounded-xl border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-600">Batal</button>
                                                <button type="submit" class="rounded-xl bg-blue-700 px-4 py-1.5 text-xs font-bold text-white hover:bg-blue-800">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-xs text-slate-400">
                            Belum ada akun pembina terdaftar. Klik "+ Tambah Pembina Baru" untuk menambahkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-100">{{ $users->links() }}</div>
</div>
@endsection

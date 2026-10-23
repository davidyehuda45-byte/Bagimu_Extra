@extends('layouts.staff')

@section('title', 'Kelola Wali Kelas')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" x-data="{ openForm: false }">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Kelola Wali Kelas</h1>
        <p class="text-sm text-slate-500">Kelola akun guru wali kelas dan penugasan rombel binaan agar dapat memantau eskul & rapor siswa kelasnya.</p>
    </div>
    <div>
        <button type="button" @click="openForm = !openForm" class="rounded-xl bg-blue-700 px-4 py-2.5 text-xs sm:text-sm font-bold text-white hover:bg-blue-800 transition shadow-xs inline-flex items-center gap-2 cursor-pointer">
            <x-icon name="user-plus" class="h-4 w-4" />
            <span x-text="openForm ? '✕ Tutup Formulir' : '+ Tambah Wali Kelas Baru'"></span>
        </button>

        {{-- Form Tambah Wali Kelas Baru --}}
        <div x-show="openForm" x-transition class="mt-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-md text-left" style="display: none;">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Tambah Akun Wali Kelas Baru</h2>
                    <p class="text-xs text-slate-500">Buat akun login guru dan langsung tautkan ke rombel kelas yang diampu.</p>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.wali-kelas.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Lengkap &amp; Gelar <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Hartono, S.Pd." required
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-semibold focus:border-blue-600 focus:outline-hidden focus:ring-2 focus:ring-blue-600/20">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Resmi Sekolah <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="budi@sekolah.sch.id" required
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-medium focus:border-blue-600 focus:outline-hidden focus:ring-2 focus:ring-blue-600/20">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Username Login (Opsional)</label>
                    <input type="text" name="username" value="{{ old('username') }}" placeholder="cth: walibudi"
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-medium focus:border-blue-600 focus:outline-hidden focus:ring-2 focus:ring-blue-600/20">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Rombel Kelas Binaan <span class="text-rose-500">*</span></label>
                    <select name="school_class_id" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-semibold focus:border-blue-600 focus:outline-hidden focus:ring-2 focus:ring-blue-600/20">
                        <option value="">-- Pilih Rombel Kelas Binaan --</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c->id }}" {{ old('school_class_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->nama }} (Tingkat {{ $c->tingkat ?? '-' }}) {{ $c->waliKelas ? '— [Sudah diampu: ' . $c->waliKelas->name . ']' : '— [Tersedia]' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Kata Sandi Awal (Min. 8 karakter) <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required minlength="8" placeholder="••••••••"
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-medium focus:border-blue-600 focus:outline-hidden focus:ring-2 focus:ring-blue-600/20">
                </div>

                <div class="sm:col-span-2 flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="openForm = false" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="rounded-xl bg-blue-700 px-5 py-2 text-xs font-bold text-white hover:bg-blue-800 transition shadow-xs">
                        Simpan &amp; Tautkan Wali Kelas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Flash Feedback --}}
@if (session('status'))
    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-semibold text-emerald-800 flex items-center gap-2">
        <x-icon name="check-circle" class="h-4 w-4 text-emerald-600 shrink-0" />
        <span>{{ session('status') }}</span>
    </div>
@endif

@if ($errors->any())
    <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs font-semibold text-rose-800 space-y-1">
        @foreach ($errors->all() as $err)
            <div class="flex items-center gap-2">
                <x-icon name="alert-triangle" class="h-4 w-4 text-rose-600 shrink-0" />
                <span>{{ $err }}</span>
            </div>
        @endforeach
    </div>
@endif

{{-- Tabel Daftar Wali Kelas --}}
<div class="rounded-3xl border border-slate-200 bg-white shadow-xs overflow-hidden">
    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900">Daftar Wali Kelas Terdaftar</h2>
            <p class="text-xs text-slate-500">Total {{ $waliKelases->total() }} akun wali kelas di dalam sistem.</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-600">
            <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3.5">Nama &amp; Kontak</th>
                    <th class="px-5 py-3.5">Username Login</th>
                    <th class="px-5 py-3.5">Rombel Kelas Binaan</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($waliKelases as $wk)
                    <tr class="hover:bg-slate-50/70 transition-colors" x-data="{ showEdit: false, showReset: false }">
                        <td class="px-5 py-3.5">
                            <div class="font-bold text-slate-900 text-sm">{{ $wk->name }}</div>
                            <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                <x-icon name="mail" class="h-3 w-3 text-slate-400" />
                                <span>{{ $wk->email }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 font-mono font-medium text-slate-700">
                            {{ $wk->username ?? '—' }}
                        </td>
                        <td class="px-5 py-3.5">
                            @if ($wk->managedClass)
                                <div class="inline-flex items-center gap-1.5 rounded-xl bg-blue-50 border border-blue-200 px-3 py-1.5 text-xs font-bold text-blue-700">
                                    <x-icon name="grad" class="h-3.5 w-3.5 text-blue-600" />
                                    <span>{{ $wk->managedClass->nama }}</span>
                                    <span class="text-slate-400 font-normal">({{ $wk->managedClass->students->count() }} siswa)</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center rounded-lg bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700 border border-amber-200/80">
                                        Belum ada kelas
                                    </span>
                                    <button type="button" @click="showEdit = true" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                        Tautkan Kelas &rarr;
                                    </button>
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="inline-flex items-center gap-1.5 justify-end">
                                {{-- Tombol Edit --}}
                                <button type="button" @click="showEdit = true" title="Edit Data Wali Kelas"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-amber-600 hover:bg-amber-50 hover:border-amber-200 transition shadow-2xs">
                                    <x-icon name="pencil" class="h-4 w-4" />
                                </button>

                                {{-- Tombol Reset Password --}}
                                <button type="button" @click="showReset = true" title="Reset Kata Sandi"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 transition shadow-2xs">
                                    <x-icon name="key" class="h-4 w-4" />
                                </button>

                                {{-- Tombol Hapus --}}
                                <form method="POST" action="{{ route('admin.wali-kelas.destroy', $wk) }}"
                                      data-confirm="Hapus akun wali kelas {{ $wk->name }}? Rombel kelas binaan akan dilepas."
                                      data-confirm-title="Hapus Akun Wali Kelas"
                                      data-confirm-btn="Ya, Hapus Akun">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus Akun" class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-100 hover:border-rose-300 transition shadow-2xs cursor-pointer">
                                        <x-icon name="trash" class="h-4 w-4" />
                                    </button>
                                </form>
                            </div>

                            {{-- Modal Edit Wali Kelas --}}
                            <div x-show="showEdit" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs text-left" style="display: none;" @keydown.escape.window="showEdit = false">
                                <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl border border-slate-200" @click.outside="showEdit = false">
                                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                                        <h3 class="font-bold text-slate-900 text-sm">Edit Data Wali Kelas</h3>
                                        <button type="button" @click="showEdit = false" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.wali-kelas.update', $wk) }}" class="space-y-3.5 text-xs">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="block font-bold text-slate-700 mb-1">Nama Lengkap &amp; Gelar</label>
                                            <input type="text" name="name" value="{{ $wk->name }}" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs font-semibold focus:border-blue-600 focus:outline-hidden">
                                        </div>
                                        <div>
                                            <label class="block font-bold text-slate-700 mb-1">Email Resmi</label>
                                            <input type="email" name="email" value="{{ $wk->email }}" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs font-medium focus:border-blue-600 focus:outline-hidden">
                                        </div>
                                        <div>
                                            <label class="block font-bold text-slate-700 mb-1">Username Login</label>
                                            <input type="text" name="username" value="{{ $wk->username }}" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs font-medium focus:border-blue-600 focus:outline-hidden">
                                        </div>
                                        <div>
                                            <label class="block font-bold text-slate-700 mb-1">Rombel Kelas Binaan <span class="text-rose-500">*</span></label>
                                            <select name="school_class_id" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs font-semibold focus:border-blue-600 focus:outline-hidden">
                                                <option value="">-- Pilih Rombel Kelas Binaan --</option>
                                                @foreach ($classes as $c)
                                                    <option value="{{ $c->id }}" {{ $wk->managedClass?->id === $c->id ? 'selected' : '' }}>
                                                        {{ $c->nama }} (Tingkat {{ $c->tingkat ?? '-' }}) {{ $c->waliKelas && $c->waliKelas->id !== $wk->id ? '— [Sudah diampu: ' . $c->waliKelas->name . ']' : ($wk->managedClass?->id === $c->id ? '— [Kelas Saat Ini]' : '— [Tersedia]') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                            <button type="button" @click="showEdit = false" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                                            <button type="submit" class="rounded-xl bg-blue-700 px-5 py-2 text-xs font-bold text-white hover:bg-blue-800">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- Modal Reset Sandi Inline --}}
                            <div x-show="showReset" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs text-left" style="display: none;" @keydown.escape.window="showReset = false">
                                <div class="w-full max-w-sm rounded-3xl bg-white p-6 shadow-2xl border border-slate-200" @click.outside="showReset = false">
                                    <h3 class="font-bold text-slate-900 text-sm mb-1">Reset Kata Sandi</h3>
                                    <p class="text-xs text-slate-500 mb-4">Ubah password akun {{ $wk->name }} ({{ $wk->email }}):</p>
                                    <form method="POST" action="{{ route('admin.wali-kelas.password', $wk) }}" class="space-y-3">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <input type="password" name="password" required minlength="8" placeholder="Password baru (min. 8 karakter)"
                                                   class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-medium focus:border-blue-600 focus:outline-hidden">
                                        </div>
                                        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                                            <button type="button" @click="showReset = false" class="rounded-xl border border-slate-300 px-3.5 py-2 text-xs font-semibold text-slate-600">Batal</button>
                                            <button type="submit" class="rounded-xl bg-blue-700 px-4 py-2 text-xs font-bold text-white hover:bg-blue-800">Ubah Sandi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-xs text-slate-400">
                            <x-icon name="user-group" class="h-8 w-8 text-slate-300 mx-auto mb-2" />
                            Belum ada akun wali kelas. Klik tombol <b>"+ Tambah Wali Kelas Baru"</b> untuk menambahkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($waliKelases->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $waliKelases->links() }}
        </div>
    @endif
</div>
@endsection

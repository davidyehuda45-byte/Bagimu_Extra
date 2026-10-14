@forelse ($students as $s)
    <tr class="border-b border-slate-100 hover:bg-slate-50/70 transition-colors">
        <td class="py-3 px-3 font-semibold text-slate-900">{{ $s->nis }}</td>
        <td class="py-3 px-3 font-mono text-xs text-slate-500">{{ $s->nisn ?? '—' }}</td>
        <td class="py-3 px-3 font-medium text-slate-800">{{ $s->nama }}</td>
        <td class="py-3 px-3 text-slate-700">{{ $s->kelas }}</td>
        <td class="py-3 px-3 text-slate-600">{{ $s->jurusan?->short() ?? $s->jurusan }}</td>
        <td class="py-3 px-3">
            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $s->status === 'AKTIF' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                {{ $s->status }}
            </span>
        </td>
        <td class="py-3 px-3">
            @if ($s->user?->login_token && !$s->user?->login_token_revoked_at)
                <div class="inline-flex items-center gap-1.5 font-mono font-bold text-xs bg-blue-50 text-blue-900 px-2 py-1 rounded-lg border border-blue-200">
                    <span class="tracking-widest">{{ $s->user->login_token }}</span>
                    <button type="button" onclick="navigator.clipboard.writeText('{{ $s->user->login_token }}'); this.innerText='✓'; setTimeout(()=>this.innerText='❐', 1200)"
                            class="text-blue-400 hover:text-blue-700 font-normal text-[11px] px-1" title="Salin Token">❐</button>
                </div>
                @if ($s->user?->password_set_at)
                    <span class="block text-[10px] text-slate-400 font-medium mt-0.5">+ Password aktif</span>
                @endif
            @else
                <span class="text-xs text-slate-400 italic">Belum ada token</span>
            @endif
        </td>
        <td class="py-3 px-3 text-right">
            <details class="group relative inline-block text-left">
                <summary class="flex h-8 w-8 list-none items-center justify-center rounded-xl text-slate-500 hover:bg-slate-100 cursor-pointer [&::-webkit-details-marker]:hidden focus:outline-none focus:ring-2 focus:ring-blue-500/20" title="Menu Opsi">
                    <x-icon name="more-vertical" class="h-4 w-4" />
                </summary>
                <div class="absolute right-0 z-30 mt-1 w-44 origin-top-right rounded-2xl border border-slate-200 bg-white p-1.5 shadow-xl ring-1 ring-black/5 text-xs text-slate-700">
                    <a href="{{ route('admin.students.show', $s) }}" class="flex items-center gap-2 rounded-xl px-2.5 py-2 hover:bg-slate-50 font-medium text-slate-700">
                        <x-icon name="eye" class="h-3.5 w-3.5 text-slate-400" />
                        <span>Lihat Detail</span>
                    </a>
                    <a href="{{ route('admin.students.edit', $s) }}" class="flex items-center gap-2 rounded-xl px-2.5 py-2 hover:bg-slate-50 font-medium text-slate-700">
                        <x-icon name="pencil" class="h-3.5 w-3.5 text-slate-400" />
                        <span>Ubah Data</span>
                    </a>
                    <form method="POST" action="{{ route('admin.students.token', $s) }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 rounded-xl px-2.5 py-2 hover:bg-blue-50 text-blue-700 font-medium text-left">
                            <x-icon name="key" class="h-3.5 w-3.5 text-blue-500" />
                            <span>{{ $s->user?->login_token ? 'Reset Token' : 'Terbitkan Token' }}</span>
                        </button>
                    </form>
                    @if ($s->user?->login_token && !$s->user?->login_token_revoked_at)
                        <form method="POST" action="{{ route('admin.students.revoke', $s) }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2 rounded-xl px-2.5 py-2 hover:bg-amber-50 text-amber-700 font-medium text-left">
                                <x-icon name="x" class="h-3.5 w-3.5 text-amber-500" />
                                <span>Cabut Token</span>
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('admin.students.kartu', $s) }}" target="_blank" class="flex items-center gap-2 rounded-xl px-2.5 py-2 hover:bg-emerald-50 text-emerald-700 font-medium">
                        <x-icon name="id-card" class="h-3.5 w-3.5 text-emerald-600" />
                        <span>Cetak Kartu</span>
                    </a>
                    <div class="my-1 border-t border-slate-100"></div>
                    <form method="POST" action="{{ route('admin.students.destroy', $s) }}" onsubmit="return confirm('Hapus permanen data siswa {{ $s->nama }} (NIS: {{ $s->nis }})?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex w-full items-center gap-2 rounded-xl px-2.5 py-2 hover:bg-rose-50 text-rose-600 font-medium text-left">
                            <x-icon name="trash" class="h-3.5 w-3.5 text-rose-500" />
                            <span>Hapus Siswa</span>
                        </button>
                    </form>
                </div>
            </details>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="py-10 px-3 text-center text-slate-400">
            <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-2">
                <x-icon name="search" class="h-5 w-5" />
            </div>
            <p class="font-medium text-slate-600">Tidak ada data siswa ditemukan.</p>
            <p class="text-xs text-slate-400 mt-0.5">Coba ubah kata kunci atau filter pencarian Anda.</p>
        </td>
    </tr>
@endforelse


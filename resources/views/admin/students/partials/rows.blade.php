@forelse ($students as $s)
    <tr class="border-b border-slate-100 hover:bg-slate-50/70 transition-colors">
        <td class="py-3 px-3 w-10 text-center">
            <input type="checkbox" class="student-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer h-4 w-4" value="{{ $s->id }}" data-nama="{{ $s->nama }}" data-nis="{{ $s->nis }}" onchange="updateSelectedCount()">
        </td>
        <td class="py-3 px-3 font-semibold text-slate-900 font-mono text-xs">{{ $s->nis }}</td>
        <td class="py-3 px-3 font-mono text-xs text-slate-500">{{ $s->nisn ?? '—' }}</td>
        <td class="py-3 px-3">
            <div class="font-medium text-slate-800">{{ $s->nama }}</div>
            @php $reg = $s->user?->registrations?->first(); @endphp
            @if ($reg)
                <div class="flex items-center gap-1 mt-0.5">
                    <span class="inline-flex items-center gap-1 rounded-md bg-blue-50 text-blue-800 border border-blue-200/70 px-1.5 py-0.5 text-[10px] font-bold">
                        <x-icon name="trophy" class="h-2.5 w-2.5 text-blue-600" />
                        <span>{{ $reg->eskul_utama }}</span>
                    </span>
                </div>
            @else
                <span class="inline-block text-[10px] text-amber-600 font-medium mt-0.5 italic">Belum mendaftar eskul</span>
            @endif
        </td>
        <td class="py-3 px-3">
            <span class="inline-flex items-center rounded-lg bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700 border border-blue-200/80 font-mono">{{ $s->kelas }}</span>
        </td>
        <td class="py-3 px-3 text-slate-600 text-xs">{{ $s->jurusan?->short() ?? $s->jurusan }}</td>
        <td class="py-3 px-3">
            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $s->status === 'AKTIF' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                {{ $s->status }}
            </span>
        </td>
        <td class="py-3 px-3">
            @if ($s->user?->login_token && !$s->user?->login_token_revoked_at)
                @php $waNum = $s->whatsapp ?: ($s->wali_kontak ?: ($reg?->whatsapp ?? '')); @endphp
                <div class="inline-flex items-center gap-1 font-mono font-bold text-xs bg-blue-50 text-blue-900 px-2 py-1 rounded-lg border border-blue-200">
                    <span class="tracking-widest">{{ $s->user->login_token }}</span>
                    <button type="button" onclick="salinToken(this, '{{ $s->user->login_token }}')"
                            class="text-blue-400 hover:text-blue-700 px-0.5" title="Salin Token"><x-icon name="copy" class="h-3.5 w-3.5" /></button>
                    <button type="button" onclick="sendTokenViaWa('{{ addslashes($s->nama) }}', '{{ $s->nis }}', '{{ $s->user->login_token }}', '{{ $waNum }}')"
                            class="text-emerald-600 hover:text-emerald-800 px-1 hover:scale-110 transition-transform" title="Kirim Token via WhatsApp"><x-icon name="send" class="h-3.5 w-3.5" /></button>
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
                <div class="absolute right-0 z-30 mt-1 w-48 origin-top-right rounded-2xl border border-slate-200 bg-white p-1.5 shadow-xl ring-1 ring-black/5 text-xs text-slate-700">
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
                        <button type="button" onclick="sendTokenViaWa('{{ addslashes($s->nama) }}', '{{ $s->nis }}', '{{ $s->user->login_token }}', '{{ $waNum }}')" class="flex w-full items-center gap-2 rounded-xl px-2.5 py-2 hover:bg-emerald-50 text-emerald-700 font-medium text-left">
                            <span class="text-sm">💬</span>
                            <span>Kirim Token ke WA</span>
                        </button>
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
                    <button type="button" onclick="confirmDeleteSingle('{{ $s->id }}', '{{ addslashes($s->nama) }}', '{{ $s->nis }}')" class="flex w-full items-center gap-2 rounded-xl px-2.5 py-2 hover:bg-rose-50 text-rose-600 font-medium text-left">
                        <x-icon name="trash" class="h-3.5 w-3.5 text-rose-500" />
                        <span>Hapus Siswa</span>
                    </button>
                </div>
            </details>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="py-10 px-3 text-center text-slate-400">
            <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-2">
                <x-icon name="search" class="h-5 w-5" />
            </div>
            <p class="font-medium text-slate-600">Tidak ada data siswa ditemukan.</p>
            <p class="text-xs text-slate-400 mt-0.5">Coba ubah kata kunci atau filter pencarian Anda.</p>
        </td>
    </tr>
@endforelse


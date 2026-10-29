@extends('layouts.staff')

@section('title', 'Siswa Rombel — Wali Kelas')

@section('content')
<div class="space-y-6" x-data="{
    bukaModal: false,
    search: '',
    filterStatus: 'all',
    selected: [],
    selectedRombel: [],
    siswaList: {{ Js::from($calonSiswa ?? []) }},
    get filteredList() {
        return this.siswaList.filter(s => {
            const matchSearch = !this.search || 
                s.nama.toLowerCase().includes(this.search.toLowerCase()) || 
                String(s.nis).toLowerCase().includes(this.search.toLowerCase());
            
            if (!matchSearch) return false;

            if (this.filterStatus === 'all') return true;
            if (this.filterStatus === 'belum_ada') return !s.kelas;
            @if ($tingkatKelas && $tingkatKelas > 10)
                if (this.filterStatus === 'naik') {
                    if (!s.kelas) return false;
                    const klsUpper = s.kelas.toUpperCase();
                    @if ($tingkatKelas === 12)
                        return klsUpper.startsWith('XI ') || klsUpper.startsWith('11 ') || klsUpper === 'XI' || klsUpper === '11';
                    @elseif ($tingkatKelas === 11)
                        return klsUpper.startsWith('X ') || klsUpper.startsWith('10 ') || klsUpper === 'X' || klsUpper === '10';
                    @endif
                }
            @endif
            if (this.filterStatus.startsWith('kelas_')) {
                const targetKls = this.filterStatus.replace('kelas_', '');
                return s.kelas === targetKls;
            }
            return true;
        });
    },
    pilihSemua() {
        const visibleIds = this.filteredList.map(s => s.id);
        const allSelected = visibleIds.every(id => this.selected.includes(id));
        if (allSelected) {
            this.selected = this.selected.filter(id => !visibleIds.includes(id));
        } else {
            this.selected = Array.from(new Set([...this.selected, ...visibleIds]));
        }
    },
    isSemuaTerpilih() {
        const visibleIds = this.filteredList.map(s => s.id);
        return visibleIds.length > 0 && visibleIds.every(id => this.selected.includes(id));
    },
    waCopied: false,
    salinWa() {
        const list = {{ Js::from($students->filter(fn($s) => !isset($regPeta[$s->nis]))->values()->map(fn($s) => ['nama' => $s->nama, 'nis' => $s->nis])) }};
        if (!list || list.length === 0) {
            alert('Luar biasa! Seluruh siswa di rombel ini sudah memilih ekstrakurikuler.');
            return;
        }
        let text = '*PENGINGAT PENDAFTARAN EKSTRAKURIKULER*\n';
        text += 'Rombel: {{ $kelas?->nama }}\n';
        text += 'Tahun Ajaran: {{ $periode?->periode ?? '2026/2027' }}\n\n';
        text += 'Yth. Bapak/Ibu Wali Murid dan Siswa,\n';
        text += 'Diberitahukan bahwa batas pendaftaran eskul akan segera ditutup. Berikut adalah daftar siswa yang *BELUM* memilih ekstrakurikuler:\n\n';
        list.forEach((s, idx) => {
            text += (idx + 1) + '. ' + s.nama + ' (NIS: ' + s.nis + ')\n';
        });
        text += '\nBagi siswa di atas, mohon segera memilih eskul melalui portal resmi:\n{{ route('siswa.login') }}\n\n';
        text += 'Terima kasih atas perhatian dan kerjasamanya.\nSalam,\nWali Kelas ({{ $kelas?->wali_kelas_nama ?? auth()->user()->name }})';

        navigator.clipboard.writeText(text).then(() => {
            this.waCopied = true;
            setTimeout(() => this.waCopied = false, 3000);
        });
    }
}">
    @php
        $belumDaftarCount = $students->filter(fn($s) => !isset($regPeta[$s->nis]))->count();
    @endphp
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold text-slate-900">Direktori Siswa Rombel {{ $kelas?->nama }}</h1>
                @if ($kelas)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                        {{ $students->count() }} Siswa
                    </span>
                    @if ($belumDaftarCount > 0)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            {{ $belumDaftarCount }} Belum Memilih
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            100% Lengkap
                        </span>
                    @endif
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Daftar siswa dalam rombel Anda dan status ekstrakurikuler yang diikuti.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if ($kelas)
                @if ($belumDaftarCount > 0)
                    <button type="button" @click="salinWa()" class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-300 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 px-3.5 py-2 text-xs font-bold shadow-2xs transition cursor-pointer">
                        <x-icon name="send" class="h-3.5 w-3.5 text-emerald-600" />
                        <span x-text="waCopied ? 'Teks WA Tersalin!' : 'Salin Pengingat WA ({{ $belumDaftarCount }})'"></span>
                    </button>
                @endif

                <button type="button" @click="bukaModal = true" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-3.5 py-2 text-xs font-bold shadow-xs shadow-blue-600/20 transition cursor-pointer">
                    <x-icon name="plus" class="h-3.5 w-3.5" />
                    <span>+ Kelas Saya</span>
                </button>
            @endif

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
    </div>

    @if (! $kelas)
        <div class="rounded-2xl border border-blue-200/80 bg-blue-50/70 p-4 text-xs font-semibold text-slate-700 flex items-center gap-3 shadow-2xs">
            <x-icon name="info" class="h-5 w-5 text-blue-600 shrink-0" />
            <div>
                <p class="font-bold text-sm text-blue-950">Akun Anda Belum Ditugaskan ke Rombel Kelas</p>
                <p class="mt-0.5 text-slate-600">Hubungi Administrator sekolah untuk menautkan akun Anda ke rombel binaan di menu <b>Kelola Wali Kelas</b>.</p>
            </div>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
        {{-- Floating Bar Aksi Massal Siswa Rombel --}}
        <div x-show="selectedRombel.length > 0"
             x-cloak
             x-transition
             class="mb-4 flex flex-wrap items-center justify-between gap-3 p-3 bg-rose-50 border border-rose-200 rounded-2xl text-xs">
            <div class="flex items-center gap-2">
                <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-rose-100 text-rose-700 font-bold">
                    <x-icon name="info" class="h-3.5 w-3.5" />
                </span>
                <span class="font-bold text-slate-800">
                    <span x-text="selectedRombel.length" class="text-rose-600"></span> siswa rombel dipilih
                </span>
            </div>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('wali-kelas.siswa.keluarkan-massal') }}" onsubmit="return confirm('Keluarkan ' + selectedRombel.length + ' siswa terpilih dari rombel {{ $kelas?->nama }}?')">
                    @csrf
                    @if (request('kelas_id'))
                        <input type="hidden" name="kelas_id" value="{{ request('kelas_id') }}">
                    @endif
                    <template x-for="id in selectedRombel" :key="id">
                        <input type="hidden" name="student_ids[]" :value="id">
                    </template>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition cursor-pointer">
                        <x-icon name="trash" class="h-3.5 w-3.5" />
                        <span>Keluarkan Siswa Terpilih</span>
                    </button>
                </form>
                <button type="button" @click="selectedRombel = []" class="px-2.5 py-1.5 rounded-xl border border-slate-200 bg-white text-slate-600 font-semibold hover:bg-slate-50 transition cursor-pointer">
                    Batal
                </button>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-100">
            <table class="min-w-full text-xs">
                <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider font-bold text-[11px]">
                    <tr>
                        <th class="py-2.5 px-3 text-center w-10">
                            @if ($students->isNotEmpty())
                                <input type="checkbox"
                                       @change="selectedRombel = $event.target.checked ? [{{ $students->pluck('id')->join(',') }}] : []"
                                       :checked="selectedRombel.length > 0 && selectedRombel.length === {{ $students->count() }}"
                                       class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 h-3.5 w-3.5 cursor-pointer"
                                       title="Pilih Semua Siswa Rombel">
                            @endif
                        </th>
                        <th class="py-2.5 px-3 text-center">No</th>
                        <th class="py-2.5 px-4 text-left">NIS</th>
                        <th class="py-2.5 px-4 text-left">Nama Siswa</th>
                        <th class="py-2.5 px-4 text-center">L/P</th>
                        <th class="py-2.5 px-4 text-left">Ekstrakurikuler Diikuti</th>
                        <th class="py-2.5 px-4 text-left">No. WhatsApp</th>
                        <th class="py-2.5 px-4 text-center">Status</th>
                        <th class="py-2.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($students as $index => $s)
                        @php $reg = $regPeta->get($s->nis); @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors" :class="selectedRombel.includes({{ $s->id }}) ? 'bg-rose-50/40' : ''">
                            <td class="py-2.5 px-3 text-center">
                                <input type="checkbox"
                                       :value="{{ $s->id }}"
                                       x-model="selectedRombel"
                                       class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 h-3.5 w-3.5 cursor-pointer">
                            </td>
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
                            <td class="py-2.5 px-4 text-center">
                                @if ($kelas)
                                    <form method="POST" action="{{ route('wali-kelas.siswa.keluarkan') }}" onsubmit="return confirm('Keluarkan {{ $s->nama }} ({{ $s->nis }}) dari rombel {{ $kelas->nama }}?')">
                                        @csrf
                                        <input type="hidden" name="student_id" value="{{ $s->id }}">
                                        @if (request('kelas_id'))
                                            <input type="hidden" name="kelas_id" value="{{ request('kelas_id') }}">
                                        @endif
                                        <button type="submit" class="inline-flex items-center justify-center h-7 w-7 rounded-lg border border-slate-200 bg-white text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 transition shadow-2xs cursor-pointer" title="Keluarkan dari Rombel">
                                            <x-icon name="trash" class="h-3.5 w-3.5" />
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">
                                Tidak ada data siswa di rombel ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL + KELAS SAYA --}}
    @if ($kelas)
        <div x-show="bukaModal"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
             style="display: none;"
             @keydown.escape.window="bukaModal = false">

            <div class="w-full max-w-3xl rounded-3xl bg-white shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh]"
                 @click.outside="bukaModal = false">

                {{-- Header Modal --}}
                <div class="flex items-center justify-between p-5 border-b border-slate-100 bg-slate-50/70">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                            <x-icon name="user" class="h-5 w-5" />
                        </span>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Masukkan Siswa ke Rombel {{ $kelas->nama }}</h3>
                            <p class="text-xs text-slate-500">Centang kotak pada sisi kanan siswa yang menjadi anggota kelas Anda.</p>
                        </div>
                    </div>
                    <button type="button" @click="bukaModal = false" class="rounded-xl p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                        <x-icon name="x" class="h-5 w-5" />
                    </button>
                </div>

                {{-- Catatan Panduan --}}
                <div class="p-4 bg-blue-50/70 border-b border-blue-100 text-xs text-blue-900 flex items-start gap-2.5">
                    <x-icon name="info" class="h-4 w-4 text-blue-600 shrink-0 mt-0.5" />
                    <span><b>Catatan:</b> Mohon pastikan siswa yang dipilih benar-benar sesuai dengan pembagian kelas yang telah ditetapkan agar tidak terjadi kesalahan penempatan data.</span>
                </div>

                {{-- Filter & Search Controls --}}
                <div class="p-4 border-b border-slate-100 bg-white grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Filter Status Siswa / Jenjang</label>
                        <select x-model="filterStatus" class="w-full border-slate-300 rounded-xl py-2 px-3 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:ring-blue-500">
                            <option value="all">Semua Calon Siswa ({{ $calonSiswa->count() }})</option>
                            <option value="belum_ada">Siswa Belum Berkelas / Baru</option>
                            @if ($tingkatKelas && $tingkatKelas > 10)
                                <option value="naik">Naik ke Kelas {{ $tingkatKelas == 12 ? 'XII' : ($tingkatKelas == 11 ? 'XI' : $tingkatKelas) }} (Tingkat {{ $tingkatKelas - 1 }})</option>
                            @endif
                            @foreach ($daftarKelasAsal as $ka)
                                <option value="kelas_{{ $ka }}">Asal Rombel: {{ $ka }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Cari Nama atau NIS</label>
                        <div class="relative">
                            <input type="text" x-model="search" placeholder="Ketik nama siswa atau NIS..." class="w-full border-slate-300 rounded-xl py-2 pl-8 pr-3 text-xs text-slate-800 focus:border-blue-500 focus:ring-blue-500">
                            <div class="absolute left-2.5 top-2.5 text-slate-400 pointer-events-none">
                                <x-icon name="search" class="h-3.5 w-3.5" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form & Tabel Calon Siswa --}}
                <form method="POST" action="{{ route('wali-kelas.siswa.masukkan') }}" class="flex-1 flex flex-col min-h-0">
                    @csrf
                    @if (request('kelas_id'))
                        <input type="hidden" name="kelas_id" value="{{ request('kelas_id') }}">
                    @endif

                    <div class="p-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between text-xs">
                        <button type="button" @click="pilihSemua()" class="font-bold text-blue-600 hover:text-blue-800 hover:underline cursor-pointer">
                            <span x-text="isSemuaTerpilih() ? 'Batalkan Pilih Semua' : 'Centang Semua yang Ditampilkan'"></span>
                        </button>
                        <span class="font-bold text-slate-600">
                            <span x-text="selected.length" class="text-blue-600"></span> siswa dipilih
                        </span>
                    </div>

                    <div class="flex-1 overflow-y-auto p-4 min-h-[220px]">
                        <table class="min-w-full text-xs">
                            <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider font-bold text-[11px] sticky top-0 z-10 shadow-2xs">
                                <tr>
                                    <th class="py-2 px-3 text-left">NIS</th>
                                    <th class="py-2 px-3 text-left">Nama Siswa</th>
                                    <th class="py-2 px-3 text-center">L/P</th>
                                    <th class="py-2 px-3 text-left">Kelas Saat Ini</th>
                                    <th class="py-2 px-3 text-center">Pilih</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="s in filteredList" :key="s.id">
                                    <tr class="hover:bg-blue-50/40 transition-colors" :class="selected.includes(s.id) ? 'bg-blue-50/60 font-semibold' : ''">
                                        <td class="py-2 px-3 font-mono font-bold text-slate-800" x-text="s.nis"></td>
                                        <td class="py-2 px-3 text-slate-900" x-text="s.nama"></td>
                                        <td class="py-2 px-3 text-center text-slate-500 font-bold" x-text="s.gender ? (s.gender.value ? s.gender.value[0] : s.gender[0]) : '-'"></td>
                                        <td class="py-2 px-3">
                                            <span x-show="s.kelas" class="inline-flex items-center px-2 py-0.5 rounded-md font-semibold bg-slate-100 text-slate-600 text-[10px]" x-text="s.kelas"></span>
                                            <span x-show="!s.kelas" class="inline-flex items-center px-2 py-0.5 rounded-md font-semibold bg-amber-50 text-amber-700 text-[10px]">Belum Ada Kelas</span>
                                        </td>
                                        <td class="py-2 px-3 text-center">
                                            <input type="checkbox" name="student_ids[]" :value="s.id" x-model="selected" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 h-4 w-4 cursor-pointer">
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="filteredList.length === 0">
                                    <td colspan="5" class="py-8 text-center text-slate-400">
                                        Tidak ada data siswa yang cocok dengan filter atau pencarian.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Footer Modal --}}
                    <div class="p-4 border-t border-slate-100 bg-slate-50/80 flex items-center justify-between gap-3">
                        <button type="button" @click="bukaModal = false" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                            Tutup
                        </button>
                        <button type="submit" :disabled="selected.length === 0" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-5 py-2 text-xs font-bold shadow-xs transition cursor-pointer">
                            <x-icon name="check" class="h-3.5 w-3.5" />
                            <span>Simpan ke Rombel (<span x-text="selected.length"></span>)</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection


@extends('layouts.siswa')

@section('title', $existing ? 'Status Pendaftaran' : 'Pilih Ekstrakurikuler')

@section('content')
<div class="space-y-5 w-full">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-900">{{ $existing ? 'Pendaftaran Selesai' : 'Pilih Ekstrakurikuler' }}</h1>
        @if ($existing)
            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold bg-emerald-100 text-emerald-800">
                <x-icon name="check" class="h-3.5 w-3.5" />
                Resmi Terdaftar
            </span>
        @endif
    </div>

    @if (!$periode)
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-500 mb-3">
                <x-icon name="clock" class="h-6 w-6" />
            </div>
            <p class="font-bold text-base text-slate-900">Belum Ada Periode Pendaftaran yang Dibuka</p>
            <p class="mt-1 text-sm text-slate-500">Silakan hubungi bagian kesiswaan atau TU untuk informasi periode aktif.</p>
        </div>
    @elseif (!$windowOpen && !$existing)
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-700 mb-3">
                <x-icon name="clock" class="h-6 w-6" />
            </div>
            <p class="font-bold text-base text-slate-900">Pendaftaran Belum Dibuka</p>
            <p class="mt-1 text-sm text-slate-500">{{ $windowLabel }}</p>
        </div>
    @else
        {{-- Profil akun siswa --}}
        <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center gap-3 min-w-0">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-100 text-base font-bold text-blue-800">
                    {{ strtoupper(substr($user->name ?? '?', 0, 1)) }}
                </span>
                <div class="min-w-0 leading-tight">
                    <p class="truncate font-bold text-slate-900">{{ $user->name }}</p>
                    <p class="truncate text-xs text-slate-500 font-mono">NIS: {{ $user->student?->nis ?? $user->username }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-rose-50 hover:text-rose-600 transition-colors">
                    <x-icon name="logout" class="h-3.5 w-3.5" />
                    <span>Keluar</span>
                </button>
            </form>
        </div>

        @if ($existing)
            {{-- Halaman sukses siswa setelah mendaftar --}}
            <div class="rounded-3xl border border-emerald-200 bg-gradient-to-br from-emerald-50/80 via-white to-emerald-50/50 p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-4">
                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-md shadow-emerald-600/20">
                        <x-icon name="check" class="h-8 w-8" />
                    </span>
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-bold bg-emerald-100 text-emerald-800">
                            Otomatis Diterima
                        </span>
                        <h2 class="mt-1 text-2xl font-extrabold text-slate-900">{{ $existing->eskul_utama }}</h2>
                        <p class="text-xs text-slate-500">Periode Akademik {{ $periode->periode }}</p>
                    </div>
                </div>

                {{-- Detail jadwal & pembina eskul yang dipilih --}}
                <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-4">
                    <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                        <x-icon name="calendar" class="h-4 w-4 text-blue-600" />
                        Rincian Kegiatan Ekstrakurikuler
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="rounded-xl bg-slate-50 p-3.5">
                            <span class="text-xs font-semibold uppercase text-slate-400">Pembina Eskul</span>
                            <p class="mt-1 font-bold text-slate-900">{{ $existingEskul?->pembina?->name ?? 'Belum ditentukan' }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3.5">
                            <span class="text-xs font-semibold uppercase text-slate-400">Status Keikutsertaan</span>
                            <p class="mt-1 font-bold text-emerald-700">Resmi Terdaftar (Aktif)</p>
                        </div>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-3.5 text-sm">
                        <span class="text-xs font-semibold uppercase text-slate-400">Jadwal & Tempat Latihan</span>
                        @if ($existingEskul && $existingEskul->jadwals->isNotEmpty())
                            <ul class="mt-2 space-y-1.5">
                                @foreach ($existingEskul->jadwals as $j)
                                    <li class="flex items-center gap-2 text-slate-800 font-medium">
                                        <x-icon name="clock" class="h-4 w-4 text-slate-400 shrink-0" />
                                        <span><b>{{ ucfirst(strtolower($j->hari)) }}</b>, {{ $j->jam_mulai }}–{{ $j->jam_selesai }} WIB</span>
                                        <span class="text-xs text-slate-500 font-normal">({{ $j->tempat }})</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mt-1 text-slate-600">Jadwal latihan menyusul dari pembina.</p>
                        @endif
                    </div>

                    @if ($existing->eskul_cadangan)
                        <div class="text-xs text-slate-500">
                            Pilihan cadangan: <span class="font-semibold text-slate-700">{{ $existing->eskul_cadangan }}</span>
                        </div>
                    @endif
                </div>

                {{-- Keterangan final --}}
                <div class="mt-5 rounded-2xl bg-blue-50 border border-blue-200 p-4 text-xs text-blue-900 leading-relaxed">
                    <p class="font-bold">Pendaftaran Telah Selesai</p>
                    <p class="mt-1">Pilihan eskul bersifat final dan tidak dapat diubah lagi. Anda dapat keluar dari sistem sekarang atau melihat bukti kartu digital.</p>
                </div>

                {{-- Aksi Logout Utama --}}
                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                    <form method="POST" action="{{ route('logout') }}" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 rounded-2xl bg-rose-600 px-6 py-3.5 text-sm font-bold text-white shadow-md shadow-rose-600/20 hover:bg-rose-700 transition-colors">
                            <x-icon name="logout" class="h-4 w-4" />
                            Keluar dari Sistem (Logout)
                        </button>
                    </form>
                    <a href="{{ route('siswa.status') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-300 bg-white px-5 py-3.5 text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                        <x-icon name="id-card" class="h-4 w-4 text-slate-500" />
                        Lihat Status & E-Card
                    </a>
                </div>
            </div>
        @elseif ($windowOpen)
            {{-- Form Pemilihan Eskul Siswa --}}
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-900">
                <p class="font-bold">Pemberitahuan:</p>
                <p class="mt-0.5">Pendaftaran eskul hanya dilakukan <b>satu kali</b> dan langsung diterima. Pilihan yang sudah dikirim tidak dapat diubah lagi.</p>
            </div>

            <form id="form-pendaftaran" method="POST" action="{{ route('siswa.daftar.store') }}" class="space-y-5 rounded-3xl border border-slate-200 bg-white p-5 sm:p-7 shadow-sm">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div class="sm:col-span-2">
                        <label for="kelas" class="block font-semibold text-slate-700 mb-1">Kelas <span class="text-rose-500">*</span></label>
                        <input id="kelas" name="kelas" type="text" value="{{ old('kelas', $user->student?->kelas ?? '') }}" required placeholder="Contoh: 10 RPL 1 atau XI RPL 1"
                               class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="mt-1 text-[11px] text-slate-400">Format: 10/11/12 + jurusan + nomor (cth: 10 RPL 1).</p>
                    </div>

                    <div>
                        <label for="jurusan" class="block font-semibold text-slate-700 mb-1">Jurusan <span class="text-rose-500">*</span></label>
                        <select id="jurusan" name="jurusan" required class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">— Pilih Jurusan —</option>
                            @foreach ($jurusans as $j)
                                <option value="{{ $j->value }}" @selected(old('jurusan', $user->student?->jurusan?->value) === $j->value)>{{ $j->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="usia" class="block font-semibold text-slate-700 mb-1">Usia <span class="text-rose-500">*</span></label>
                        <input id="usia" name="usia" type="number" min="13" max="20" value="{{ old('usia', 16) }}" required
                               class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="no_absen" class="block font-semibold text-slate-700 mb-1">No. Absen <span class="text-rose-500">*</span></label>
                        <input id="no_absen" name="no_absen" type="number" min="1" max="99" value="{{ old('no_absen', $user->student?->no_absen ?? '') }}" required
                               class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="nama_wali_kelas" class="block font-semibold text-slate-700 mb-1">Nama Wali Kelas</label>
                        <input id="nama_wali_kelas" name="nama_wali_kelas" type="text" value="{{ old('nama_wali_kelas') }}" placeholder="Opsional"
                               class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="sm:col-span-2">
                        <span class="block font-semibold text-slate-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></span>
                        <div class="flex gap-6 mt-1">
                            @foreach ($genders as $g)
                                <label class="inline-flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                                    <input type="radio" name="gender" value="{{ $g->value }}" required
                                           @checked(old('gender', $user->student?->gender?->value) === $g->value)
                                           class="rounded-full border-slate-300 text-blue-700 focus:ring-blue-500">
                                    {{ $g->label() }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="whatsapp" class="block font-semibold text-slate-700 mb-1">No. WhatsApp Siswa</label>
                        <input id="whatsapp" name="whatsapp" type="text" value="{{ old('whatsapp') }}" placeholder="Contoh: 081234567890"
                               class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                {{-- Pemilihan Eskul & Detail Jadwal Interaktif --}}
                <div class="border-t border-slate-100 pt-5 space-y-4">
                    <div>
                        <label for="eskul_utama" class="block font-semibold text-slate-900 mb-1">Pilih Ekstrakurikuler <span class="text-rose-500">*</span></label>
                        <select id="eskul_utama" name="eskul_utama" required class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 pr-10">
                            <option value="">— Pilih Salah Satu Eskul —</option>
                            @foreach ($eskuls as $o)
                                <option value="{{ $o['name'] }}"
                                    @selected(old('eskul_utama') === $o['name'])
                                    @disabled($o['penuh'])
                                    data-pembina="{{ $o['pembina_nama'] }}"
                                    data-jadwal="{{ $o['jadwal'] }}"
                                    data-jadwals='@json($o['jadwals'])'
                                    data-kuota="{{ $o['kuota_aktif'] ? $o['terisi'].'/'.$o['kuota'].($o['penuh'] ? ' (Penuh)' : '') : 'Bebas' }}"
                                    data-desc="{{ $o['description'] }}">
                                    {{ $o['name'] }} @if($o['kuota_aktif'] && $o['penuh']) [PENUH] @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Box detail eskul terpilih (Jadwal & Pembina dari Master Eskul) --}}
                    <div id="detail-eskul-box" class="hidden rounded-2xl border border-blue-200 bg-blue-50/70 p-4 text-sm text-slate-800 transition-all">
                        <div class="flex items-center justify-between border-b border-blue-200/60 pb-2.5 mb-2.5">
                            <p class="font-bold text-blue-900" id="detail-eskul-title">Detail Eskul</p>
                            <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-800" id="detail-eskul-kuota"></span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="font-semibold text-slate-500 uppercase tracking-wider text-[10px]">Pembina:</span>
                                <p class="text-slate-900 font-bold text-sm mt-0.5" id="detail-eskul-pembina">-</p>
                            </div>
                            <div>
                                <span class="font-semibold text-slate-500 uppercase tracking-wider text-[10px]">Deskripsi:</span>
                                <p class="text-slate-700 mt-0.5 line-clamp-2" id="detail-eskul-desc">-</p>
                            </div>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-blue-200/60">
                            <span class="font-semibold text-slate-500 uppercase tracking-wider text-[10px]">Jadwal & Tempat:</span>
                            <div class="mt-1" id="detail-eskul-jadwal-list"></div>
                        </div>
                    </div>

                    <div>
                        <label for="eskul_cadangan" class="block font-semibold text-slate-700 mb-1">Eskul Cadangan (opsional)</label>
                        <select id="eskul_cadangan" name="eskul_cadangan" class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 pr-10">
                            <option value="">— Tidak Ada Pilihan Cadangan —</option>
                            @foreach ($eskuls as $o)
                                <option value="{{ $o['name'] }}" @selected(old('eskul_cadangan') === $o['name'])>
                                    {{ $o['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-[11px] text-slate-400">Cadangan akan digunakan jika eskul utama tiba-tiba penuh saat pendaftaran diproses.</p>
                    </div>

                    <div>
                        <label for="alasan" class="block font-semibold text-slate-700 mb-1">Alasan Memilih (opsional)</label>
                        <textarea id="alasan" name="alasan" rows="2" maxlength="1000" placeholder="Ceritakan motivasi atau minat Anda..."
                                  class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('alasan') }}</textarea>
                    </div>
                </div>

                {{-- Tombol Buka Konfirmasi Modal --}}
                <div class="pt-2">
                    <button type="button" onclick="bukaModalKonfirmasi()" class="w-full rounded-2xl bg-blue-700 px-6 py-4 text-base font-bold text-white shadow-md shadow-blue-700/20 hover:bg-blue-800 transition-colors">
                        Daftar Ekstrakurikuler Sekarang
                    </button>
                </div>
            </form>

            {{-- Dialog Modal Konfirmasi Pendaftaran --}}
            <div id="modal-konfirmasi" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-fade-in" aria-modal="true" role="dialog">
                <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl border border-slate-100 transform transition-all">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-700 mb-4">
                        <x-icon name="clipboard" class="h-7 w-7" />
                    </div>

                    <h3 class="text-center text-lg font-bold text-slate-900">Konfirmasi Pilihan Eskul</h3>
                    <p class="mt-2 text-center text-sm text-slate-600 leading-relaxed">
                        Apakah Anda yakin dengan pilihan ini? Eskul tidak dapat diubah setelah pendaftaran.
                    </p>

                    <div class="mt-4 rounded-2xl bg-slate-50 border border-slate-200/80 p-3.5 text-center">
                        <span class="text-xs text-slate-400 uppercase font-semibold">Eskul yang Dipilih:</span>
                        <p id="modal-eskul-target" class="text-lg font-extrabold text-blue-900 mt-0.5">-</p>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="button" onclick="tutupModalKonfirmasi()" class="flex-1 rounded-xl border border-slate-300 bg-white py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="button" onclick="submitPendaftaran()" class="flex-1 rounded-xl bg-blue-700 py-3 text-sm font-bold text-white shadow hover:bg-blue-800 transition-colors">
                            Ya, Daftar
                        </button>
                    </div>
                </div>
            </div>

            <script>
            (function () {
                const sel = document.getElementById('eskul_utama');
                const box = document.getElementById('detail-eskul-box');
                const title = document.getElementById('detail-eskul-title');
                const kuota = document.getElementById('detail-eskul-kuota');
                const pembina = document.getElementById('detail-eskul-pembina');
                const desc = document.getElementById('detail-eskul-desc');
                const jadwalList = document.getElementById('detail-eskul-jadwal-list');

                function updateDetail() {
                    const opt = sel.options[sel.selectedIndex];
                    if (!opt || !opt.value) {
                        box.classList.add('hidden');
                        return;
                    }

                    title.textContent = opt.value;
                    kuota.textContent = 'Kuota: ' + (opt.getAttribute('data-kuota') || 'Bebas');
                    pembina.textContent = opt.getAttribute('data-pembina') || 'Belum ditentukan';
                    desc.textContent = opt.getAttribute('data-desc') || 'Tidak ada deskripsi.';

                    let jadwals = [];
                    try {
                        jadwals = JSON.parse(opt.getAttribute('data-jadwals') || '[]');
                    } catch (e) {}

                    if (jadwals && jadwals.length > 0) {
                        jadwalList.innerHTML = '<ul class="space-y-1">' + jadwals.map(j =>
                            '<li class="flex items-center gap-1.5 text-xs text-slate-800">' +
                            '<b>' + j.hari + ':</b> ' + j.jam + ' WIB ' +
                            (j.tempat ? '<span class="text-slate-500">(' + j.tempat + ')</span>' : '') +
                            '</li>'
                        ).join('') + '</ul>';
                    } else {
                        jadwalList.innerHTML = '<p class="text-xs text-slate-500">' + (opt.getAttribute('data-jadwal') || 'Jadwal menyusul.') + '</p>';
                    }

                    box.classList.remove('hidden');
                }

                sel.addEventListener('change', updateDetail);
                updateDetail();
            })();

            function bukaModalKonfirmasi() {
                const form = document.getElementById('form-pendaftaran');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const sel = document.getElementById('eskul_utama');
                const namaEskul = sel.options[sel.selectedIndex]?.value;
                if (!namaEskul) {
                    sel.focus();
                    return;
                }

                document.getElementById('modal-eskul-target').textContent = namaEskul;
                document.getElementById('modal-konfirmasi').classList.remove('hidden');
            }

            function tutupModalKonfirmasi() {
                document.getElementById('modal-konfirmasi').classList.add('hidden');
            }

            function submitPendaftaran() {
                tutupModalKonfirmasi();
                document.getElementById('form-pendaftaran').submit();
            }
            </script>
        @endif
    @endif
</div>
@endsection

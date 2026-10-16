@extends('layouts.siswa')

@section('title', $existing ? 'Status Pendaftaran' : 'Pilih Ekstrakurikuler')

@section('content')
<div class="space-y-6 w-full max-w-4xl mx-auto">
    {{-- Header Halaman & Identitas Siswa --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white rounded-3xl border border-slate-200/80 p-5 shadow-xs">
        <div class="flex items-center gap-3.5">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-600 text-white font-black text-lg shadow-md shadow-blue-500/20">
                {{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}
            </span>
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <h1 class="text-base sm:text-lg font-bold text-slate-900 truncate">{{ $user->name }}</h1>
                    @if ($existing)
                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-bold bg-emerald-100 text-emerald-800">
                            <x-icon name="check" class="h-3 w-3" />
                            Terdaftar
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-blue-100 text-blue-800">
                            Siswa
                        </span>
                    @endif
                </div>
                <div class="flex flex-wrap items-center gap-2 mt-0.5 text-xs text-slate-500">
                    <span class="font-mono bg-slate-100 px-2 py-0.5 rounded-md text-slate-700 font-semibold">NIS: {{ $user->student?->nis ?? $user->username }}</span>
                    @if ($periode)
                        <span class="text-slate-400">•</span>
                        <span class="text-blue-700 font-medium">T.A. {{ $periode->periode }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2 self-end sm:self-center">
            @if ($existing)
                <a href="{{ route('siswa.status') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-3.5 py-2 text-xs font-bold text-blue-700 hover:bg-blue-100 transition-colors">
                    <x-icon name="id-card" class="h-3.5 w-3.5" />
                    <span>Kartu Digital</span>
                </a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="inline-block m-0">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors cursor-pointer" title="Keluar">
                    <x-icon name="logout" class="h-3.5 w-3.5" />
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>

    @if (!$periode)
        {{-- Periode Tidak Aktif --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-10 text-center shadow-xs">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-3">
                <x-icon name="clock" class="h-7 w-7" />
            </div>
            <h2 class="font-bold text-lg text-slate-900">Belum Ada Periode Pendaftaran Aktif</h2>
            <p class="mt-1 text-xs text-slate-500 max-w-md mx-auto">Pendaftaran ekstrakurikuler saat ini belum dibuka oleh pihak sekolah. Silakan hubungi bagian kesiswaan atau wali kelas Anda.</p>
        </div>
    @elseif (!$windowOpen && !$existing)
        {{-- Jadwal Pendaftaran Tertutup --}}
        <div class="rounded-3xl border border-amber-200 bg-amber-50/70 p-10 text-center shadow-xs">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 text-amber-700 mb-3">
                <x-icon name="clock" class="h-7 w-7" />
            </div>
            <h2 class="font-bold text-lg text-amber-950">Pendaftaran Sedang Ditutup</h2>
            <p class="mt-1 text-xs text-amber-800 max-w-md mx-auto">{{ $windowLabel }}</p>
        </div>
    @else
        @if ($existing)
            {{-- Halaman Sukses: Siswa Sudah Mendaftar --}}
            <div class="rounded-3xl border border-emerald-200 bg-gradient-to-br from-emerald-50/80 via-white to-emerald-50/40 p-6 sm:p-8 shadow-xs">
                <div class="flex items-start gap-4">
                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-md shadow-emerald-600/25">
                        <x-icon name="check" class="h-7 w-7" />
                    </span>
                    <div class="min-w-0">
                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold bg-emerald-100 text-emerald-800">
                            Pendaftaran Berhasil & Diterima
                        </span>
                        <h2 class="mt-1.5 text-2xl font-black text-slate-900">{{ $existing->eskul_utama }}</h2>
                        <p class="text-xs text-slate-500">Tahun Ajaran {{ $periode->periode }} • {{ \App\Models\AppSetting::get('school.name', 'Sekolah') }}</p>
                    </div>
                </div>

                {{-- Detail Rincian Eskul --}}
                <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-xs space-y-4">
                    <h3 class="font-bold text-xs uppercase tracking-wider text-slate-500 flex items-center gap-2 border-b border-slate-100 pb-3">
                        <x-icon name="calendar" class="h-4 w-4 text-blue-600" />
                        Rincian Kegiatan Ekstrakurikuler
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="rounded-xl bg-slate-50 p-3.5 border border-slate-100">
                            <span class="font-semibold text-slate-400 uppercase text-[10px]">Pembina Ekstrakurikuler:</span>
                            <p class="mt-1 font-bold text-slate-900 text-sm">{{ $existingEskul?->pembina?->name ?? 'Belum ditentukan' }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3.5 border border-slate-100">
                            <span class="font-semibold text-slate-400 uppercase text-[10px]">Status Keikutsertaan:</span>
                            <p class="mt-1 font-bold text-emerald-700 text-sm">Resmi Terdaftar (Aktif)</p>
                        </div>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-3.5 border border-slate-100 text-xs">
                        <span class="font-semibold text-slate-400 uppercase text-[10px]">Jadwal & Tempat Pertemuan:</span>
                        @if ($existingEskul && $existingEskul->jadwals->isNotEmpty())
                            <ul class="mt-2 space-y-2">
                                @foreach ($existingEskul->jadwals as $j)
                                    <li class="flex items-center gap-2 text-slate-800">
                                        <span class="inline-flex items-center justify-center rounded-lg bg-blue-100 text-blue-800 font-bold px-2 py-0.5 text-[11px]">
                                            {{ ucfirst(strtolower($j->hari)) }}
                                        </span>
                                        <span class="font-semibold">{{ $j->jam_mulai }}–{{ $j->jam_selesai }} WIB</span>
                                        <span class="text-slate-500">di {{ $j->tempat }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mt-1 text-slate-500 italic">Jadwal latihan akan diinformasikan oleh pembina.</p>
                        @endif
                    </div>

                </div>

                {{-- Alert Informasi Selesai --}}
                <div class="mt-5 rounded-2xl bg-blue-50/80 border border-blue-200 p-4 text-xs text-blue-900 leading-relaxed">
                    <p class="font-bold">Informasi Penting:</p>
                    <p class="mt-0.5">Pendaftaran Anda telah disimpan secara permanen. Anda dapat mencetak atau menyimpan bukti pendaftaran kartu digital sekarang.</p>
                </div>

                {{-- Tombol Aksi --}}
                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('siswa.status') }}" class="flex-1 inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-700 px-6 py-3.5 text-xs font-bold text-white shadow-md shadow-blue-700/20 hover:bg-blue-800 transition-colors">
                        <x-icon name="id-card" class="h-4 w-4" />
                        <span>Lihat Bukti Pendaftaran (E-Card)</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="sm:w-auto">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-300 bg-white px-5 py-3.5 text-xs font-bold text-slate-700 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors cursor-pointer">
                            <x-icon name="logout" class="h-4 w-4" />
                            <span>Keluar dari Akun</span>
                        </button>
                    </form>
                </div>
            </div>
        @elseif ($windowOpen)
            {{-- Pemberitahuan Pendaftaran --}}
            <div class="flex items-start gap-3.5 rounded-2xl border border-amber-200 bg-amber-50/80 p-4 text-xs text-amber-900 shadow-2xs">
                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-amber-200 text-amber-900 mt-0.5">
                    <x-icon name="alert" class="h-4 w-4" />
                </div>
                <div class="leading-relaxed">
                    <strong class="font-bold text-amber-950">Pemberitahuan Penting:</strong>
                    <p class="mt-0.5">Pendaftaran ekstrakurikuler hanya dapat dilakukan <b>1 (satu) kali</b> dan langsung resmi diterima. Pastikan data dan pilihan eskul Anda sudah benar sebelum dikirim, karena data yang telah terkirim <b>tidak dapat diubah kembali</b>.</p>
                </div>
            </div>

            {{-- Formulir Pendaftaran --}}
            <form id="form-pendaftaran" method="POST" action="{{ route('siswa.daftar.store') }}" class="space-y-6 rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-xs">
                @csrf

                {{-- Bagian 1: Identitas & Akademik Siswa (Terkoneksi Kartu Pelajar) --}}
                <div class="rounded-2xl border border-slate-200/90 bg-slate-50/70 p-4 sm:p-5">
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200/80">
                        <div class="flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-100 text-blue-700">
                                <x-icon name="user" class="h-4 w-4" />
                            </div>
                            <div>
                                <h2 class="text-xs font-bold text-slate-900">Data Siswa (Terkoneksi Kartu Pelajar)</h2>
                                <p class="text-[10px] text-slate-500">Data identitas otomatis sinkron dari direktori siswa</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                            <x-icon name="check" class="h-3 w-3" />
                            Terverifikasi
                        </span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                        <div class="bg-white p-2.5 rounded-xl border border-slate-200/60 shadow-2xs">
                            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">NIS</span>
                            <span class="font-mono font-bold text-slate-800">{{ $user->student?->nis ?? $user->username }}</span>
                        </div>
                        <div class="bg-white p-2.5 rounded-xl border border-slate-200/60 shadow-2xs">
                            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Kelas</span>
                            <span class="font-bold text-slate-800">{{ $user->student?->kelas ?? '-' }}</span>
                        </div>
                        <div class="bg-white p-2.5 rounded-xl border border-slate-200/60 shadow-2xs">
                            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Jurusan</span>
                            <span class="font-bold text-slate-800">{{ $user->student?->jurusan?->label() ?? '-' }}</span>
                        </div>
                        <div class="bg-white p-2.5 rounded-xl border border-slate-200/60 shadow-2xs">
                            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">No. Absen</span>
                            <span class="font-bold text-slate-800">{{ $user->student?->no_absen ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Bagian 2: Pemilihan Ekstrakurikuler --}}
                <div class="pt-2">
                    <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100">
                        <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                            <x-icon name="trophy" class="h-4 w-4" />
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Pilihan Ekstrakurikuler</h2>
                            <p class="text-[11px] text-slate-400">Tentukan satu eskul yang diminati. Bila kuota penuh, silakan pilih eskul lain.</p>
                        </div>
                    </div>

                    <div class="space-y-4 text-xs">
                        {{-- Eskul Utama --}}
                        <div>
                            <label for="eskul_utama" class="block font-bold text-slate-900 mb-1">
                                Pilih Ekstrakurikuler Utama <span class="text-rose-500">*</span>
                            </label>
                            <select id="eskul_utama" name="eskul_utama" required class="w-full rounded-xl border-slate-300 text-xs shadow-2xs focus:border-blue-500 focus:ring-blue-500 pr-10 py-2.5 font-bold text-slate-900">
                                <option value="">— Silakan Pilih Salah Satu Ekstrakurikuler —</option>
                                @foreach ($eskuls as $o)
                                    <option value="{{ $o['name'] }}"
                                        @selected(old('eskul_utama', $pilihanAwal ?? null) === $o['name'])
                                        @disabled($o['penuh'])
                                        data-pembina="{{ $o['pembina_nama'] }}"
                                        data-jadwal="{{ $o['jadwal'] }}"
                                        data-jadwals='@json($o['jadwals'])'
                                        data-kuota-aktif="{{ $o['kuota_aktif'] ? '1' : '0' }}"
                                        data-kuota="{{ $o['kuota'] }}"
                                        data-terisi="{{ $o['terisi'] }}"
                                        data-kuota-text="{{ $o['kuota_aktif'] ? $o['terisi'].'/'.$o['kuota'].($o['penuh'] ? ' (Penuh)' : '') : 'Bebas' }}"
                                        data-desc="{{ $o['description'] }}">
                                        {{ $o['name'] }}{{ $o['kuota_aktif'] ? ' [Kuota: '.$o['terisi'].'/'.$o['kuota'].($o['penuh'] ? ' - PENUH' : '').']' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('eskul_utama')
                                <p class="mt-1 text-[11px] font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Card Detail Eskul Terpilih (Interaktif & Modern) --}}
                        <div id="detail-eskul-box" class="hidden rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50/90 via-slate-50/50 to-blue-50/40 p-5 shadow-2xs transition-all animate-fade-in">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-blue-100 pb-3 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-600 text-white"><x-icon name="star" class="h-4 w-4" /></span>
                                    <h4 class="font-extrabold text-blue-950 text-sm" id="detail-eskul-title">Detail Eskul</h4>
                                </div>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1 text-xs font-bold text-blue-800 border border-blue-200 shadow-2xs self-start sm:self-auto" id="detail-eskul-kuota"></span>
                            </div>

                            {{-- Kuota Progress Bar (Muncul jika ada kuota) --}}
                            <div id="kuota-progress-wrap" class="hidden mb-3">
                                <div class="flex items-center justify-between text-[11px] font-semibold text-slate-600 mb-1">
                                    <span>Kapasitas Kuota Peserta</span>
                                    <span id="kuota-progress-percent">0%</span>
                                </div>
                                <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200">
                                    <div id="kuota-progress-bar" class="h-full bg-blue-600 rounded-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div class="rounded-xl bg-white p-3 border border-blue-100 shadow-2xs">
                                    <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px] block">Guru Pembina:</span>
                                    <p class="text-slate-900 font-bold text-xs mt-0.5" id="detail-eskul-pembina">-</p>
                                </div>
                                <div class="rounded-xl bg-white p-3 border border-blue-100 shadow-2xs">
                                    <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px] block">Deskripsi Kegiatan:</span>
                                    <p class="text-slate-600 text-xs mt-0.5 line-clamp-2" id="detail-eskul-desc">-</p>
                                </div>
                            </div>

                            <div class="mt-3.5 pt-3 border-t border-blue-100">
                                <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px] block mb-1.5">Jadwal & Lokasi Latihan:</span>
                                <div id="detail-eskul-jadwal-list" class="space-y-1.5"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tombol Buka Konfirmasi Modal --}}
                <div class="pt-4 border-t border-slate-100">
                    <button type="button" onclick="bukaModalKonfirmasi()" class="w-full flex items-center justify-center gap-2 rounded-2xl bg-blue-700 px-6 py-4 text-sm font-bold text-white shadow-lg shadow-blue-700/25 hover:bg-blue-800 hover:shadow-blue-800/30 transition-all cursor-pointer">
                        <x-icon name="clipboard" class="h-4 w-4" />
                        <span>Daftar Ekstrakurikuler Sekarang</span>
                    </button>
                    <p class="text-center text-[11px] text-slate-400 mt-2">Dengan mendaftar, Anda berkomitmen untuk aktif mengikuti jadwal kegiatan ekstrakurikuler terpilih.</p>
                </div>
            </form>

            {{-- Dialog Modal Konfirmasi Pendaftaran (Ala E-Voting) --}}
            <div id="modal-konfirmasi" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4" aria-modal="true" role="dialog">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" onclick="tutupModalKonfirmasi()"></div>
                <div class="relative w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl border border-slate-100 text-left">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-700 border border-blue-200">
                            <x-icon name="clipboard" class="h-6 w-6" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Konfirmasi Pilihan Eskul</h3>
                            <p class="text-[11px] text-slate-500">Periksa kembali data Anda sebelum mengirimkan pendaftaran.</p>
                        </div>
                    </div>

                    {{-- Ringkasan Pilihan --}}
                    <div class="rounded-2xl bg-slate-50 border border-slate-200/80 p-4 space-y-2.5 text-xs mb-4">
                        <div class="flex justify-between items-center pb-2 border-b border-slate-200/60">
                            <span class="text-slate-500">Nama Siswa:</span>
                            <span class="font-bold text-slate-900">{{ $user->name }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-slate-200/60">
                            <span class="text-slate-500">Kelas / Rombel:</span>
                            <span class="font-bold text-slate-900" id="modal-review-kelas">-</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">Eskul Pilihan:</span>
                            <span class="font-extrabold text-blue-700 text-sm" id="modal-review-eskul-utama">-</span>
                        </div>
                    </div>

                    <div class="rounded-xl bg-amber-50 p-3 text-[11px] text-amber-900 border border-amber-200/80 mb-5 leading-relaxed flex items-start gap-2">
                        <x-icon name="alert" class="h-4 w-4 shrink-0 mt-px" />
                        <span><strong>Penting:</strong> Pendaftaran bersifat permanen dan <strong>tidak dapat diubah</strong> setelah formulir dikirim.</span>
                    </div>

                    <div class="flex gap-2">
                        <button type="button" onclick="tutupModalKonfirmasi()" class="flex-1 rounded-xl border border-slate-300 bg-white py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer">
                            Periksa Lagi
                        </button>
                        <button type="button" id="btn-submit-pendaftaran" onclick="submitPendaftaran()" class="flex-1 rounded-xl bg-blue-700 py-2.5 text-xs font-bold text-white shadow-md shadow-blue-700/20 hover:bg-blue-800 transition-colors cursor-pointer">
                            Ya, Kirim Sekarang
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
                const progressWrap = document.getElementById('kuota-progress-wrap');
                const progressBar = document.getElementById('kuota-progress-bar');
                const progressPercent = document.getElementById('kuota-progress-percent');

                function updateDetail() {
                    const opt = sel.options[sel.selectedIndex];
                    if (!opt || !opt.value) {
                        box.classList.add('hidden');
                        return;
                    }

                    title.textContent = opt.value;
                    kuota.textContent = 'Kuota: ' + (opt.getAttribute('data-kuota-text') || 'Bebas');
                    pembina.textContent = opt.getAttribute('data-pembina') || 'Belum ditentukan';
                    desc.textContent = opt.getAttribute('data-desc') || 'Tidak ada deskripsi eskul.';

                    // Progress kuota
                    const isKuotaAktif = opt.getAttribute('data-kuota-aktif') === '1';
                    if (isKuotaAktif) {
                        const total = parseInt(opt.getAttribute('data-kuota') || '0', 10);
                        const terisi = parseInt(opt.getAttribute('data-terisi') || '0', 10);
                        if (total > 0) {
                            const pct = Math.min(100, Math.round((terisi / total) * 100));
                            progressBar.style.width = pct + '%';
                            progressPercent.textContent = pct + '% (' + (total - terisi) + ' kursi tersisa)';
                            if (pct >= 90) {
                                progressBar.className = 'h-full bg-rose-600 rounded-full transition-all duration-300';
                            } else if (pct >= 70) {
                                progressBar.className = 'h-full bg-amber-500 rounded-full transition-all duration-300';
                            } else {
                                progressBar.className = 'h-full bg-blue-600 rounded-full transition-all duration-300';
                            }
                            progressWrap.classList.remove('hidden');
                        } else {
                            progressWrap.classList.add('hidden');
                        }
                    } else {
                        progressWrap.classList.add('hidden');
                    }

                    // Render Jadwal
                    let jadwals = [];
                    try {
                        jadwals = JSON.parse(opt.getAttribute('data-jadwals') || '[]');
                    } catch (e) {}

                    if (jadwals && jadwals.length > 0) {
                        jadwalList.innerHTML = jadwals.map(j =>
                            '<div class="flex items-center gap-2 text-xs text-slate-800 bg-white/80 border border-blue-100 rounded-lg p-2">' +
                            '<span class="inline-flex items-center rounded-md bg-blue-100 px-2 py-0.5 text-[11px] font-bold text-blue-800">' + j.hari + '</span>' +
                            '<span class="font-semibold">' + j.jam + ' WIB</span>' +
                            (j.tempat ? '<span class="text-slate-500">di ' + j.tempat + '</span>' : '') +
                            '</div>'
                        ).join('');
                    } else {
                        jadwalList.innerHTML = '<p class="text-xs text-slate-500 italic">' + (opt.getAttribute('data-jadwal') || 'Jadwal latihan menyusul.') + '</p>';
                    }

                    box.classList.remove('hidden');
                }

                sel.addEventListener('change', updateDetail);
                if (sel.value) updateDetail();
            })();

            function bukaModalKonfirmasi() {
                const form = document.getElementById('form-pendaftaran');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const selUtama = document.getElementById('eskul_utama');
                const namaEskulUtama = selUtama.options[selUtama.selectedIndex]?.value;
                if (!namaEskulUtama) {
                    selUtama.focus();
                    return;
                }

                const kelasText = @json($user->student?->kelas ?? '-');

                document.getElementById('modal-review-kelas').textContent = kelasText;
                document.getElementById('modal-review-eskul-utama').textContent = namaEskulUtama;

                const modal = document.getElementById('modal-konfirmasi');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function tutupModalKonfirmasi() {
                const modal = document.getElementById('modal-konfirmasi');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            function submitPendaftaran() {
                const btn = document.getElementById('btn-submit-pendaftaran');
                btn.disabled = true;
                btn.innerHTML = `<svg class="animate-spin h-3.5 w-3.5 text-white inline mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Mengirim...`;
                document.getElementById('form-pendaftaran').submit();
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') tutupModalKonfirmasi();
            });
            </script>
        @endif
    @endif
</div>
@endsection

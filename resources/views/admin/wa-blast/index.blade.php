@extends('layouts.staff')

@section('title', 'WhatsApp Blast')

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-xl font-bold text-slate-900">WhatsApp Blast</h1>
        <div>
            @if ($fonnteStatus['connected'])
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">
                    <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                    Gateway Aktif ({{ $fonnteStatus['device_status'] === 'connect' ? 'Online' : 'Offline' }})
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800 border border-amber-200">
                    <span class="h-2 w-2 rounded-full bg-amber-600"></span>
                    Belum Terhubung
                </span>
            @endif
        </div>
    </div>

    {{-- Kartu 1: Koneksi Fonnte Gateway --}}
    <div x-data="{
            token: '',
            loading: false,
            valid: null,
            message: '',
            deviceInfo: null,
            isConnected: @js($fonnteStatus['connected']),
            checkToken() {
                let t = this.token.trim();
                if (!t) {
                    this.valid = null;
                    this.message = '';
                    this.deviceInfo = null;
                    return;
                }
                this.loading = true;
                this.message = 'Memeriksa token...';
                this.valid = null;
                
                fetch('{{ route('admin.wa-blast.check') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ token: t })
                })
                .then(res => res.json())
                .then(data => {
                    this.loading = false;
                    this.valid = data.valid;
                    this.message = data.message;
                    if (data.valid) {
                        this.deviceInfo = data;
                    } else {
                        this.deviceInfo = null;
                    }
                })
                .catch(() => {
                    this.loading = false;
                    this.valid = false;
                    this.message = 'Gagal verifikasi ke Fonnte.';
                });
            }
        }"
        class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-xs">
        
        <div class="flex items-center gap-2.5 mb-3">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200">
                <x-icon name="phone" class="h-4 w-4" />
            </div>
            <h2 class="font-bold text-slate-900 text-sm">Koneksi Fonnte API Gateway</h2>
        </div>

        @if ($fonnteStatus['connected'])
            {{-- Panel info saat sudah terhubung --}}
            <div class="rounded-xl border border-emerald-200 bg-emerald-50/40 p-3.5">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs mb-3">
                    <div>
                        <span class="block text-slate-400 font-medium">API Key</span>
                        <span class="font-mono font-bold text-slate-700 text-sm">{{ $fonnteStatus['masked_token'] }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-medium">Pengirim</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $fonnteStatus['name'] }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-medium">Nomor WA</span>
                        <span class="font-bold text-emerald-700 font-mono text-sm">{{ $fonnteStatus['device'] }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2.5 border-t border-emerald-200/70">
                    <form method="POST" action="{{ route('admin.wa-blast.disconnect') }}" onsubmit="return confirm('Putus koneksi Fonnte Gateway?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-rose-300 bg-white px-3 py-1 text-xs font-bold text-rose-600 hover:bg-rose-50 transition cursor-pointer shadow-2xs">
                            <x-icon name="trash" class="h-3.5 w-3.5" />
                            <span>Putus Koneksi</span>
                        </button>
                    </form>
                    <button type="button" @click="isConnected = false" class="text-xs text-blue-600 font-semibold hover:underline cursor-pointer">
                        Ganti API Key
                    </button>
                </div>
            </div>
        @endif

        {{-- Form Input / Ganti Token --}}
        <div x-show="!isConnected" class="{{ $fonnteStatus['connected'] ? 'mt-3 pt-3 border-t border-slate-100' : '' }}">
            <form method="POST" action="{{ route('admin.wa-blast.save-token') }}" class="space-y-3">
                @csrf
                <div class="relative">
                    <input 
                        type="password" 
                        id="fonnte_token_input"
                        name="token" 
                        x-model="token" 
                        @paste="setTimeout(() => checkToken(), 100)"
                        @input.debounce.500ms="checkToken()"
                        placeholder="Tempelkan Fonnte API Key di sini..." 
                        required 
                        class="w-full border-slate-300 rounded-xl text-sm font-mono focus:border-emerald-500 focus:ring-emerald-500 pr-24"
                    />
                    <button 
                        type="button" 
                        @click="checkToken()" 
                        :disabled="loading || !token.trim()"
                        class="absolute right-1.5 top-1.5 bottom-1.5 px-3 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition disabled:opacity-50 cursor-pointer">
                        <span x-show="!loading">Cek Valid</span>
                        <span x-show="loading" class="inline-flex items-center gap-1">
                            <svg class="animate-spin h-3 w-3 text-slate-600" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                            Cek...
                        </span>
                    </button>
                </div>

                {{-- Status Validasi Realtime --}}
                <div x-show="message" x-cloak class="rounded-xl p-2.5 text-xs" :class="valid === true ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : (valid === false ? 'bg-rose-50 text-rose-800 border border-rose-200' : 'bg-slate-50 text-slate-700 border border-slate-200')">
                    <div class="flex items-start gap-2">
                        <template x-if="valid === true">
                            <x-icon name="check" class="h-4 w-4 text-emerald-600 shrink-0 mt-0.5" />
                        </template>
                        <template x-if="valid === false">
                            <x-icon name="x" class="h-4 w-4 text-rose-600 shrink-0 mt-0.5" />
                        </template>
                        <template x-if="valid === null">
                            <svg class="animate-spin h-4 w-4 text-slate-500 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                        </template>
                        <div class="flex-1">
                            <p class="font-semibold" x-text="message"></p>
                            <template x-if="deviceInfo">
                                <p class="mt-0.5 text-[11px] text-emerald-700">
                                    Device: <b x-text="deviceInfo.device"></b> (<span x-text="deviceInfo.name"></span>) • Status: <b x-text="deviceInfo.device_status"></b>
                                </p>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button 
                        type="submit" 
                        :disabled="valid !== true"
                        class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition shadow-xs disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                        Simpan Gateway
                    </button>
                    @if ($fonnteStatus['connected'])
                        <button type="button" @click="isConnected = true" class="px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Kartu 2: Statistik Ringkas Siswa & Status Blast --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-2xs">
            <p class="text-xs text-slate-500 font-medium">Total Siswa Aktif</p>
            <p class="text-xl font-black text-slate-900 mt-0.5">{{ number_format($totalSiswa) }}</p>
            <p class="text-[10px] text-slate-400 mt-1">{{ number_format($totalDenganWa) }} memiliki nomor WA</p>
        </div>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-3.5 shadow-2xs">
            <p class="text-xs text-emerald-800 font-medium">Sudah Dikirimi Periode {{ $currentPeriode }}</p>
            <p class="text-xl font-black text-emerald-700 mt-0.5">{{ number_format($totalSudahBlasted) }}</p>
            <p class="text-[10px] text-emerald-600 mt-1 font-semibold">Tercatat di sistem</p>
        </div>
        <div class="rounded-xl border border-blue-200 bg-blue-50/50 p-3.5 shadow-2xs">
            <p class="text-xs text-blue-800 font-medium">Belum Menerima Blast</p>
            <p class="text-xl font-black text-blue-700 mt-0.5">{{ number_format($totalBelumBlasted) }}</p>
            <p class="text-[10px] text-blue-600 mt-1 font-semibold">Siap dikirim bertahap</p>
        </div>
        <div class="rounded-xl border border-rose-200 bg-rose-50/50 p-3.5 shadow-2xs">
            <p class="text-xs text-rose-800 font-medium">Belum Memilih Eskul</p>
            <p class="text-xl font-black text-rose-700 mt-0.5">{{ number_format($totalBelumDaftar) }}</p>
            <p class="text-[10px] text-rose-600 mt-1 font-semibold">Prioritas sosialisasi</p>
        </div>
    </div>

    {{-- Kartu 3: Form Eksekusi WA Blast Massal --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-xs space-y-4">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 border border-blue-200">
                    <x-icon name="send" class="h-4 w-4" />
                </div>
                <div>
                    <h2 class="font-bold text-slate-900 text-sm">Kirim WA Blast</h2>
                    <p class="text-[11px] text-slate-500">Kirim pemberitahuan dan kredensial login via WhatsApp secara aman dan bertahap.</p>
                </div>
            </div>
            <div class="hidden sm:inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <x-icon name="shield" class="h-3.5 w-3.5" />
                <span>Proteksi Anti-Banned Aktif</span>
            </div>
        </div>

        {{-- Banner Edukasi Keamanan WA Gateway --}}
        <div class="rounded-xl border border-emerald-200/80 bg-emerald-50/60 p-3.5 text-xs text-emerald-950 shadow-2xs flex items-start gap-2.5">
            <x-icon name="info" class="h-4 w-4 text-emerald-700 shrink-0 mt-0.5" />
            <div class="leading-relaxed">
                <span class="font-bold">Keamanan Nomor Sekolah Terjamin:</span>
                Sistem mengirim pesan dengan jeda manusiawi <strong>1,5 detik</strong> per pesan dan secara otomatis membatasi <strong>hanya 1 kali pengiriman per siswa</strong> pada periode berjalan ({{ $currentPeriode }}) agar nomor WhatsApp sekolah terhindar dari pemblokiran Meta/WhatsApp.
            </div>
        </div>

        @if (!$fonnteStatus['connected'])
            <div class="rounded-xl bg-blue-50/70 px-3.5 py-2.5 border border-blue-200/80 text-slate-700 text-xs flex items-center gap-2 shadow-2xs">
                <x-icon name="info" class="h-4 w-4 text-blue-600 shrink-0" />
                <span>Hubungkan Fonnte API Key pada kartu di atas terlebih dahulu.</span>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.wa-blast.send') }}" class="space-y-4" onsubmit="return confirm('Mulai pengiriman WhatsApp Blast gelombang ini ke siswa yang ditargetkan?')">
            @csrf
            
            {{-- Target Penerima --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Target Siswa</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-emerald-300 bg-emerald-50/30 hover:bg-emerald-50 cursor-pointer">
                        <input type="radio" name="mode" value="unblasted" checked class="text-emerald-600 focus:ring-emerald-500">
                        <div class="text-xs">
                            <span class="font-bold text-slate-900 block">Belum Dikirimi Blast</span>
                            <span class="text-emerald-700 font-semibold text-[11px]">{{ number_format($totalBelumBlasted) }} siswa (Rekomendasi)</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                        <input type="radio" name="mode" value="unregistered" class="text-emerald-600 focus:ring-emerald-500">
                        <div class="text-xs">
                            <span class="font-bold text-slate-800 block">Belum Memilih Eskul</span>
                            <span class="text-slate-500 text-[11px]">{{ number_format($totalBelumDaftar) }} siswa</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                        <input type="radio" name="mode" value="untokenized" class="text-emerald-600 focus:ring-emerald-500">
                        <div class="text-xs">
                            <span class="font-bold text-slate-800 block">Belum Ada Token</span>
                            <span class="text-slate-500 text-[11px]">{{ number_format($totalBelumToken) }} siswa</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                        <input type="radio" name="mode" value="kelas" class="text-emerald-600 focus:ring-emerald-500" id="radio-mode-kelas">
                        <div class="text-xs flex-1">
                            <span class="font-bold text-slate-800 block mb-1">Per Kelas Tertentu</span>
                            <select name="kelas" onchange="document.getElementById('radio-mode-kelas').checked = true" class="block w-full rounded-lg border-slate-300 text-xs py-1">
                                <option value="">— Pilih Kelas —</option>
                                @foreach ($kelasList as $k)
                                    <option value="{{ $k }}">{{ $k }}</option>
                                @endforeach
                            </select>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Pengaturan Gelombang & Pencegahan Duplikasi --}}
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2">
                    <label for="batch_limit" class="font-bold text-slate-700 shrink-0">Batas Gelombang:</label>
                    <select name="batch_limit" id="batch_limit" class="rounded-lg border-slate-300 text-xs py-1 font-semibold text-slate-800">
                        <option value="25" selected>25 Siswa / Gelombang (Sangat Aman)</option>
                        <option value="50">50 Siswa / Gelombang (Aman)</option>
                        <option value="0">Semua Target Sekaligus</option>
                    </select>
                </div>
                <label class="flex items-center gap-2 cursor-pointer font-medium text-slate-700 select-none">
                    <input type="checkbox" name="skip_already_blasted" value="1" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span>Lewati siswa yang sudah pernah menerima blast pada periode {{ $currentPeriode }}</span>
                </label>
            </div>

            {{-- Template Pesan --}}
            <div>
                <div class="flex items-center justify-between gap-2 mb-1.5">
                    <label class="text-xs font-bold text-slate-700">Isi Pesan</label>
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="setPageTemplate('token')" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition cursor-pointer">
                            <x-icon name="key" class="h-3.5 w-3.5" />
                            <span>Token Login</span>
                        </button>
                        <button type="button" onclick="setPageTemplate('pengumuman')" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition cursor-pointer">
                            <x-icon name="megaphone" class="h-3.5 w-3.5" />
                            <span>Pengumuman</span>
                        </button>
                        <button type="button" onclick="setPageTemplate('custom')" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 transition cursor-pointer">
                            <x-icon name="pencil" class="h-3.5 w-3.5" />
                            <span>Kustom</span>
                        </button>
                    </div>
                </div>

                {{-- Variabel Pembantu --}}
                <div class="flex flex-wrap items-center gap-1 text-xs text-slate-500 mb-2">
                    <span class="text-[11px] font-semibold text-slate-400">Variabel:</span>
                    <button type="button" onclick="insertPageTag('{nama}')" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono text-[11px] cursor-pointer">{nama}</button>
                    <button type="button" onclick="insertPageTag('{token}')" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono text-[11px] cursor-pointer">{token}</button>
                    <button type="button" onclick="insertPageTag('{nis}')" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono text-[11px] cursor-pointer">{nis}</button>
                    <button type="button" onclick="insertPageTag('{kelas}')" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono text-[11px] cursor-pointer">{kelas}</button>
                    <button type="button" onclick="insertPageTag('{login_url}')" class="px-1.5 py-0.5 rounded bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200 font-mono text-[11px] cursor-pointer" title="Tautan halaman login siswa">{login_url}</button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                    <div>
                        <textarea 
                            name="pesan" 
                            id="page-wa-blast-input" 
                            rows="6" 
                            required 
                            oninput="updatePagePreview()"
                            placeholder="Ketik isi pesan WhatsApp di sini..."
                            class="w-full border-slate-300 rounded-xl text-xs sm:text-sm font-sans focus:border-emerald-500 focus:ring-emerald-500 leading-relaxed"></textarea>
                    </div>

                    <div>
                        <div class="rounded-xl border border-emerald-200 bg-[#eef7ee] p-3 text-xs text-slate-800 whitespace-pre-wrap font-sans shadow-2xs leading-relaxed min-h-[140px]" id="page-wa-preview-box">
                            (Pratinjau pesan WhatsApp)
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 flex items-center justify-end">
                <button 
                    type="submit" 
                    {{ !$fonnteStatus['connected'] ? 'disabled' : '' }}
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2 text-xs font-bold text-white hover:bg-emerald-700 transition shadow-xs disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                    <x-icon name="send" class="h-3.5 w-3.5" />
                    <span>Kirim WA Blast</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Kartu 4: Daftar Target Siswa --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
            <div class="flex flex-wrap items-center gap-2">
                <h2 class="font-bold text-slate-900 text-sm mr-2">Daftar Kontak Siswa</h2>
                <div class="inline-flex rounded-xl bg-slate-100 p-0.5 text-xs font-semibold">
                    <a href="{{ route('admin.wa-blast.index', array_merge(request()->except('filter_blast', 'page'), [])) }}" class="px-2.5 py-1 rounded-lg transition {{ !request('filter_blast') ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">Semua</a>
                    <a href="{{ route('admin.wa-blast.index', array_merge(request()->except('filter_blast', 'page'), ['filter_blast' => 'belum_kirim'])) }}" class="px-2.5 py-1 rounded-lg transition {{ request('filter_blast') === 'belum_kirim' ? 'bg-white text-blue-700 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900' }}">Belum Dikirim ({{ number_format($totalBelumBlasted) }})</a>
                    <a href="{{ route('admin.wa-blast.index', array_merge(request()->except('filter_blast', 'page'), ['filter_blast' => 'sudah_kirim'])) }}" class="px-2.5 py-1 rounded-lg transition {{ request('filter_blast') === 'sudah_kirim' ? 'bg-white text-emerald-700 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900' }}">Sudah Terkirim ({{ number_format($totalSudahBlasted) }})</a>
                </div>
            </div>
            <form method="GET" action="{{ route('admin.wa-blast.index') }}" class="flex items-center gap-2">
                @if(request('filter_blast'))
                    <input type="hidden" name="filter_blast" value="{{ request('filter_blast') }}">
                @endif
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / NIS / WA..." class="rounded-xl border-slate-300 text-xs py-1.5 px-3">
                <button class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-semibold text-xs hover:bg-slate-200 cursor-pointer">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-left text-slate-400 font-bold uppercase tracking-wider">
                        <th class="py-2 px-3">NIS</th>
                        <th class="py-2 px-3">Nama</th>
                        <th class="py-2 px-3">Kelas</th>
                        <th class="py-2 px-3">WhatsApp</th>
                        <th class="py-2 px-3">Token</th>
                        <th class="py-2 px-3">Blast ({{ $currentPeriode }})</th>
                        <th class="py-2 px-3 text-right">Status Kontak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($students as $s)
                        @php
                            $wa = $s->whatsapp ?: ($s->wali_kontak ?: ($s->registrations?->first()?->whatsapp ?? ''));
                            $isBlasted = isset($blastedNisList[$s->nis]);
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="py-2 px-3 font-mono font-bold text-slate-800">{{ $s->nis }}</td>
                            <td class="py-2 px-3 font-medium text-slate-900">{{ $s->nama }}</td>
                            <td class="py-2 px-3 font-mono text-slate-600">{{ $s->kelas }}</td>
                            <td class="py-2 px-3 font-mono">
                                @if ($wa)
                                    <span class="text-emerald-700 font-semibold">{{ $wa }}</span>
                                @else
                                    <span class="text-slate-400 italic">Belum ada</span>
                                @endif
                            </td>
                            <td class="py-2 px-3 font-mono">
                                @if ($s->user?->login_token && !$s->user?->login_token_revoked_at)
                                    <span class="bg-blue-50 text-blue-800 px-2 py-0.5 rounded font-bold">{{ $s->user->login_token }}</span>
                                @else
                                    <span class="text-slate-400 italic">Belum terbit</span>
                                @endif
                            </td>
                            <td class="py-2 px-3">
                                @if ($isBlasted)
                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                        Sudah Dikirim
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold bg-slate-100 text-slate-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                        Belum Dikirim
                                    </span>
                                @endif
                            </td>
                            <td class="py-2 px-3 text-right">
                                @if ($wa)
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Siap
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        Tanpa WA
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-5 text-center text-slate-400">Tidak ada data siswa ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-2.5 border-t border-slate-100 pt-2.5">
            {{ $students->links() }}
        </div>
    </div>
</div>

<script>
const PAGE_WA_TEMPLATES = {
    token: "Halo {nama} ({kelas}),\nBerikut adalah kredensial akun kamu untuk Sistem Ekstrakurikuler:\n\n*NIS:* {nis}\n*Token Login:* {token}\n\nSilakan buka tautan login berikut lalu masukkan NIS dan Token kamu:\n{login_url}\n\nSimpan pesan ini dengan baik. Terima kasih!",
    pengumuman: "Halo {nama} ({kelas}),\nPemberitahuan resmi: Pendaftaran kegiatan ekstrakurikuler untuk periode baru telah dibuka!\n\nSilakan segera login ke sistem melalui tautan di bawah dan tentukan pilihan eskul favoritmu:\n{login_url}\n\nJangan sampai terlambat ya!",
    custom: "Halo {nama},\n\n[Tuliskan pesan atau pengumuman Anda di sini]\n\nSalam,\nPengurus Ekstrakurikuler"
};

function setPageTemplate(type) {
    const textarea = document.getElementById('page-wa-blast-input');
    if (textarea && PAGE_WA_TEMPLATES[type]) {
        textarea.value = PAGE_WA_TEMPLATES[type];
        updatePagePreview();
    }
}

function insertPageTag(tag) {
    const textarea = document.getElementById('page-wa-blast-input');
    if (!textarea) return;
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const val = textarea.value;
    textarea.value = val.substring(0, start) + tag + val.substring(end);
    textarea.focus();
    textarea.selectionStart = textarea.selectionEnd = start + tag.length;
    updatePagePreview();
}

function updatePagePreview() {
    const textarea = document.getElementById('page-wa-blast-input');
    const previewBox = document.getElementById('page-wa-preview-box');
    if (!textarea || !previewBox) return;

    let text = textarea.value;
    text = text.replace(/\{nama\}/g, 'Budi Santoso')
               .replace(/\{nis\}/g, '10234')
               .replace(/\{kelas\}/g, '10 RPL 1')
               .replace(/\{token\}/g, '98231456')
               .replace(/\{link\}/g, window.location.origin + '/siswa/login')
               .replace(/\{login_url\}/g, window.location.origin + '/siswa/login')
               .replace(/\{aplikasi\}/g, 'Bagimu Extra');

    previewBox.textContent = text || '(Pratinjau pesan WhatsApp)';
}

document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('page-wa-blast-input');
    if (textarea && !textarea.value.trim()) {
        textarea.value = PAGE_WA_TEMPLATES.token;
        updatePagePreview();
    }
});
</script>
@endsection

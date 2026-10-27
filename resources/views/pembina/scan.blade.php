@extends('layouts.staff')

@section('title', 'Scan Presensi Kamera HP — '.$eskul->name)

@section('content')
<div class="mx-auto w-full max-w-2xl space-y-4">
    <div>
        <a href="{{ route('pembina.dashboard') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">← Kembali ke Dashboard Pembina</a>
        <h1 class="mt-1 text-xl font-bold text-slate-900">Scan Presensi Siswa (Kamera HP)</h1>
        <p class="text-xs sm:text-sm text-slate-500">{{ $eskul->name }} • Hasil pemindaian langsung mencatat kehadiran siswa dan tersimpan ke daftar hadir.</p>
        @if (! empty($pertemuan))
            <p class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-800">
                <x-icon name="calendar-check" class="h-3.5 w-3.5" /> Pertemuan Ke-{{ $pertemuan->nomor }} • {{ $pertemuan->tanggal->format('d/m/Y') }}
            </p>
        @endif
    </div>

    {{-- Tanggal Presensi --}}
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <label for="scan-tanggal" class="block text-xs font-bold text-slate-700">Tanggal Pertemuan Presensi</label>
                <p class="text-[11px] text-slate-400">Pilih tanggal jika ingin mencatat untuk tanggal hari ini atau sebelumnya</p>
            </div>
            <input id="scan-tanggal" type="date" value="{{ $tanggal }}" max="{{ today()->toDateString() }}"
                   class="rounded-xl border-slate-300 text-xs sm:text-sm font-semibold focus:border-blue-500 focus:ring-blue-500 py-1.5 px-3">
        </div>
    </x-card>

    {{-- Area Kamera Scanner --}}
    <x-card>
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
            <h2 class="flex items-center gap-2 font-bold text-slate-900 text-sm">
                <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 7h.01"/><path d="M17 7h.01"/><path d="M7 17h.01"/><path d="M17 17h.01"/></svg>
                Pemindai Kamera HP / Barcode Kartu Pelajar
            </h2>
            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">
                Kamera Real-time
            </span>
        </div>

        <div id="scanner-box" class="overflow-hidden rounded-2xl bg-slate-900 text-white relative min-h-[260px] flex flex-col items-center justify-center p-3">
            <div id="reader" class="w-full"></div>
            <div id="camera-placeholder" class="text-center py-10 space-y-3">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 text-white">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
                </div>
                <p class="text-xs text-slate-300 max-w-xs px-4">Arahkan kamera ke QR Code atau Barcode yang tertera di Kartu Pelajar siswa.</p>
                <button id="btn-start-camera" type="button" class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-5 py-2.5 text-xs font-bold text-white hover:bg-emerald-600 transition shadow-sm cursor-pointer">
                    <span>Mulai Kamera HP</span>
                </button>
            </div>
            <div id="camera-active-controls" class="hidden mt-3 w-full flex flex-wrap items-center justify-between bg-slate-800/90 p-3 rounded-xl text-xs gap-2">
                <span class="text-emerald-400 font-semibold flex items-center gap-1.5 shrink-0">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span id="camera-mode-label">Kamera Belakang Aktif</span>
                </span>
                <div class="flex items-center gap-2">
                    <button id="btn-flip-camera" type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-600 bg-slate-700/80 px-3 py-1.5 font-bold text-white hover:bg-slate-700 transition cursor-pointer">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 21h5v-5"/></svg>
                        <span>Ganti Kamera (Flip)</span>
                    </button>
                    <button id="btn-stop-camera" type="button" class="rounded-lg bg-rose-600 px-3 py-1.5 font-bold text-white hover:bg-rose-700 transition cursor-pointer">
                        Hentikan
                    </button>
                </div>
            </div>
        </div>

        {{-- Log Hasil Scan --}}
        <div id="scan-hasil" aria-live="polite" class="mt-4 space-y-2"></div>
    </x-card>

    {{-- Alternatif Ketik Manual NIS --}}
    <x-card>
        <h2 class="flex items-center gap-2 font-bold text-slate-900 text-sm">
            <x-icon name="pencil" class="h-4 w-4 text-blue-700" /> Input Cepat Manual (NIS Siswa)
        </h2>
        <p class="mt-1 text-xs text-slate-500">
            Jika kartu pelajar buram atau kamera tidak dapat digunakan, Anda dapat mengetik NIS siswa di bawah ini:
        </p>
        <form id="scan-manual" method="POST" action="{{ route('pembina.scan.store', $eskul) }}" class="mt-3 flex gap-2">
            @csrf
            <input type="hidden" name="tanggal" id="manual-tanggal" value="{{ $tanggal }}">
            <input name="kode" type="text" required placeholder="Ketik NIS siswa (cth: 10001)"
                   class="min-w-0 flex-1 rounded-xl border-slate-300 font-mono text-xs sm:text-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
            <button class="shrink-0 rounded-xl bg-blue-700 px-4 py-2 text-xs font-bold text-white hover:bg-blue-800 transition shadow-xs cursor-pointer">
                Catat Hadir
            </button>
        </form>
        @error('kode')
            <p class="mt-1.5 text-xs font-semibold text-rose-600">{{ $message }}</p>
        @enderror
    </x-card>

    {{-- Daftar Terpindai Sesi Ini --}}
    <x-card>
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <h2 class="font-bold text-slate-900 text-sm">Siswa Terpindai Sesi Ini</h2>
            <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-bold text-blue-700">
                <span id="scan-count">0</span> Siswa Hadir
            </span>
        </div>
        <ul id="scan-daftar" class="mt-3 divide-y divide-slate-100 text-xs sm:text-sm">
            <li id="scan-kosong" class="py-4 text-center text-slate-400 italic">Belum ada siswa yang dipindai pada sesi ini.</li>
        </ul>
    </x-card>
</div>

{{-- Library HTML5-QRCode untuk dukungan segala merk HP (Android & iOS Safari) --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
(function () {
    const btnStart = document.getElementById('btn-start-camera');
    const btnStop = document.getElementById('btn-stop-camera');
    const btnFlip = document.getElementById('btn-flip-camera');
    const modeLabel = document.getElementById('camera-mode-label');
    const cameraPlaceholder = document.getElementById('camera-placeholder');
    const activeControls = document.getElementById('camera-active-controls');
    const hasil = document.getElementById('scan-hasil');
    const daftar = document.getElementById('scan-daftar');
    const kosong = document.getElementById('scan-kosong');
    const count = document.getElementById('scan-count');
    const inputTanggal = document.getElementById('scan-tanggal');
    const manualTanggal = document.getElementById('manual-tanggal');
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const url = @json(route('pembina.scan.store', $eskul));

    let html5QrCode = null;
    let currentFacingMode = 'environment';
    let isProcessing = false;
    let lastScannedCode = '';
    let lastScanTime = 0;
    let jumlah = 0;
    const recordedSet = new Set();

    inputTanggal.addEventListener('change', () => {
        manualTanggal.value = inputTanggal.value;
    });

    // Suara Beep Sukses via Web Audio API (Tanpa asset external)
    function playBeep() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, ctx.currentTime);
            gain.gain.setValueAtTime(0.15, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.15);
            osc.start();
            osc.stop(ctx.currentTime + 0.15);
        } catch (e) {}
    }

    function showFeedback(ok, teks) {
        const div = document.createElement('div');
        div.className = 'rounded-2xl border p-3.5 text-xs sm:text-sm font-semibold flex items-center justify-between gap-2 shadow-2xs ' +
            (ok ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-rose-200 bg-rose-50 text-rose-700');
        div.innerHTML = `<span>${teks}</span><span class="text-[11px] opacity-75">${new Date().toLocaleTimeString()}</span>`;
        hasil.prepend(div);
        while (hasil.children.length > 3) hasil.lastChild.remove();
    }

    function addToList(nama, kelas) {
        kosong?.remove();
        jumlah++;
        count.textContent = jumlah;
        const li = document.createElement('li');
        li.className = 'py-2.5 flex items-center justify-between';
        li.innerHTML = `
            <div class="flex items-center gap-2">
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold"><svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                <span class="font-bold text-slate-800">${nama}</span>
            </div>
            <span class="text-xs font-semibold text-slate-500">${kelas || 'Siswa'}</span>
        `;
        daftar.prepend(li);
    }

    async function sendScanData(scannedText) {
        if (isProcessing) return;
        const now = Date.now();
        if (scannedText === lastScannedCode && (now - lastScanTime) < 3000) {
            return; // Debounce scan yang sama dalam 3 detik
        }
        lastScannedCode = scannedText;
        lastScanTime = now;
        isProcessing = true;

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({ kode: scannedText, tanggal: inputTanggal.value }),
            });
            const data = await res.json().catch(() => ({}));
            if (res.ok && data.ok) {
                playBeep();
                showFeedback(true, `HADIR: ${data.nama} (${data.kelas || '-'})`);
                const uniqueKey = scannedText + '|' + inputTanggal.value;
                if (!recordedSet.has(uniqueKey)) {
                    recordedSet.add(uniqueKey);
                    addToList(data.nama, data.kelas);
                }
            } else {
                showFeedback(false, data.pesan || 'Kartu pelajar / NIS tidak terdaftar pada eskul ini.');
            }
        } catch (err) {
            showFeedback(false, 'Terjadi gangguan jaringan saat mengirim data.');
        } finally {
            setTimeout(() => { isProcessing = false; }, 800);
        }
    }

    async function startScanner() {
        cameraPlaceholder.classList.add('hidden');
        activeControls.classList.remove('hidden');

        try {
            if (html5QrCode) {
                try { await html5QrCode.stop(); html5QrCode.clear(); } catch (e) {}
            }
            html5QrCode = new Html5Qrcode('reader');
            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0,
            };

            await html5QrCode.start(
                { facingMode: currentFacingMode },
                config,
                (decodedText) => {
                    sendScanData(decodedText);
                },
                (errorMessage) => {
                    // Ignore transient frame errors
                }
            );

            if (modeLabel) {
                modeLabel.textContent = currentFacingMode === 'environment' ? 'Kamera Belakang Aktif' : 'Kamera Depan Aktif';
            }
        } catch (err) {
            showFeedback(false, 'Tidak dapat mengakses kamera HP. Pastikan izin kamera telah diberikan di browser.');
            stopScanner();
        }
    }

    async function stopScanner() {
        if (html5QrCode) {
            try {
                await html5QrCode.stop();
                html5QrCode.clear();
            } catch (e) {}
            html5QrCode = null;
        }
        cameraPlaceholder.classList.remove('hidden');
        activeControls.classList.add('hidden');
    }

    async function flipCamera() {
        currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
        await startScanner();
    }

    btnStart.addEventListener('click', startScanner);
    btnStop.addEventListener('click', stopScanner);
    if (btnFlip) btnFlip.addEventListener('click', flipCamera);
    window.addEventListener('beforeunload', stopScanner);
})();
</script>
@endsection

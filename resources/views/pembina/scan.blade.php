@extends('layouts.staff')

@section('title', 'Scan Presensi — '.$eskul->name)

@section('content')
<div class="mx-auto w-full max-w-2xl space-y-4">
    <div>
        <a href="{{ route('pembina.eskul.show', $eskul) }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">← Kembali ke {{ $eskul->name }}</a>
        <h1 class="mt-1 text-xl font-bold text-slate-900">Scan Presensi E-Card</h1>
        <p class="text-sm text-slate-500">{{ $eskul->name }} • hasil scan otomatis tercatat HADIR dan tersambung ke absensi.</p>
    </div>

    <x-card>
        <label for="scan-tanggal" class="block text-xs font-semibold text-slate-600">Tanggal presensi</label>
        <input id="scan-tanggal" type="date" value="{{ $tanggal }}" max="{{ today()->toDateString() }}"
               class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
    </x-card>

    <x-card>
        <h2 class="flex items-center gap-2 font-bold text-slate-900">
            <x-icon name="qr" class="h-5 w-5 text-blue-700" /> Pindai QR E-Card
        </h2>
        <div id="scan-unsupported" class="hidden">
            <x-empty-state icon="qr" title="Kamera tidak didukung" message="Browser ini tidak mendukung pemindai kamera. Ketik kode ESK manual di bawah." />
        </div>
        <div id="scan-area" class="mt-3">
            <video id="scan-video" playsinline muted class="aspect-video w-full rounded-xl bg-slate-900 object-cover"></video>
            <div class="mt-3 flex flex-wrap gap-2">
                <button id="scan-start" type="button" class="rounded-xl bg-blue-700 px-4 py-2 text-sm font-bold text-white hover:bg-blue-800">
                    Mulai Kamera
                </button>
                <button id="scan-stop" type="button" class="hidden rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Hentikan
                </button>
            </div>
            <p class="mt-2 text-[11px] text-slate-400">Arahkan QR E-Card siswa ke kamera. Di HP, izinkan akses kamera bila diminta.</p>
        </div>
        <div id="scan-hasil" aria-live="polite" class="mt-3 space-y-2"></div>
    </x-card>

    <x-card>
        <h2 class="flex items-center gap-2 font-bold text-slate-900">
            <x-icon name="pencil" class="h-5 w-5 text-blue-700" /> Ketik Manual
        </h2>
        <p class="mt-1 text-xs text-slate-500">
            Untuk kartu hilang / kamera bermasalah: ketik kode ESK yang tercetak di bawah QR E-Card.
            Sakit tetap lewat pengajuan izin (otomatis tersambung ke absensi) atau tandai SAKIT di tabel manual.
        </p>
        <form id="scan-manual" method="POST" action="{{ route('pembina.scan.store', $eskul) }}" class="mt-3 flex gap-2">
            @csrf
            <input type="hidden" name="tanggal" id="manual-tanggal" value="{{ $tanggal }}">
            <input name="kode" type="text" required placeholder="cth: ESK-000000003F"
                   class="min-w-0 flex-1 rounded-xl border-slate-300 font-mono text-sm focus:border-blue-500 focus:ring-blue-500">
            <button class="shrink-0 rounded-xl bg-blue-700 px-4 py-2 text-sm font-bold text-white hover:bg-blue-800">Catat Hadir</button>
        </form>
        @error('kode')
            <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
        @enderror
    </x-card>

    <x-card>
        <h2 class="font-bold text-slate-900">Terpindai sesi ini (<span id="scan-count">0</span>)</h2>
        <ul id="scan-daftar" class="mt-2 divide-y divide-slate-100 text-sm">
            <li id="scan-kosong" class="py-2 text-slate-400">Belum ada.</li>
        </ul>
    </x-card>
</div>

<script>
(function () {
    const video = document.getElementById('scan-video');
    const btnStart = document.getElementById('scan-start');
    const btnStop = document.getElementById('scan-stop');
    const hasil = document.getElementById('scan-hasil');
    const daftar = document.getElementById('scan-daftar');
    const kosong = document.getElementById('scan-kosong');
    const count = document.getElementById('scan-count');
    const inputTanggal = document.getElementById('scan-tanggal');
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const url = @json(route('pembina.scan.store', $eskul));
    let stream = null, raf = null, detector = null, terakhir = '', jedaSampai = 0, jumlah = 0;
    const sudahCatat = new Set();

    if (!('BarcodeDetector' in window)) {
        document.getElementById('scan-unsupported').classList.remove('hidden');
        document.getElementById('scan-area').classList.add('hidden');
    }

    document.getElementById('manual-tanggal').value = inputTanggal.value;
    inputTanggal.addEventListener('change', () => {
        document.getElementById('manual-tanggal').value = inputTanggal.value;
    });

    function pesan(ok, teks) {
        const div = document.createElement('div');
        div.className = 'rounded-xl border p-3 text-sm ' + (ok
            ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
            : 'border-rose-200 bg-rose-50 text-rose-700');
        div.textContent = teks;
        hasil.prepend(div);
        while (hasil.children.length > 4) hasil.lastChild.remove();
    }

    function tambahDaftar(nama, kelas) {
        kosong?.remove();
        jumlah++;
        count.textContent = jumlah;
        const li = document.createElement('li');
        li.className = 'py-2';
        li.innerHTML = '<span class="font-semibold"></span> <span class="text-slate-400"></span>';
        li.querySelector('span').textContent = nama;
        li.querySelectorAll('span')[1].textContent = kelas || '';
        daftar.prepend(li);
    }

    async function kirim(kode) {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({ kode: kode, tanggal: inputTanggal.value }),
        });
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.ok) {
            pesan(true, data.pesan);
            const kunci = kode + '|' + inputTanggal.value;
            if (!sudahCatat.has(kunci)) {
                sudahCatat.add(kunci);
                tambahDaftar(data.nama, data.kelas);
            }
        } else {
            pesan(false, data.pesan || 'Gagal memproses QR.');
        }
    }

    async function loop() {
        if (!stream) return;
        try {
            const codes = await detector.detect(video);
            const now = Date.now();
            if (codes.length && now > jedaSampai && codes[0].rawValue !== terakhir) {
                terakhir = codes[0].rawValue;
                jedaSampai = now + 2500;
                kirim(terakhir);
            }
        } catch (e) {}
        raf = requestAnimationFrame(() => setTimeout(loop, 400));
    }

    btnStart.addEventListener('click', async () => {
        try {
            detector = new BarcodeDetector({ formats: ['qr_code'] });
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
            video.srcObject = stream;
            await video.play();
            btnStart.classList.add('hidden');
            btnStop.classList.remove('hidden');
            loop();
        } catch (e) {
            pesan(false, 'Kamera tidak dapat dibuka. Ketik kode manual di bawah.');
        }
    });

    function henti() {
        cancelAnimationFrame(raf);
        stream?.getTracks().forEach(t => t.stop());
        stream = null;
        video.srcObject = null;
        btnStop.classList.add('hidden');
        btnStart.classList.remove('hidden');
    }
    btnStop.addEventListener('click', henti);
    window.addEventListener('beforeunload', henti);
})();
</script>
@endsection

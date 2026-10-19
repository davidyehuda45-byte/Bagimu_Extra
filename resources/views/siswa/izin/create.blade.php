@extends('layouts.siswa')

@section('title', 'Buat Pengajuan Izin')

@section('content')
<div class="rounded-xl bg-white border p-4">
    <h1 class="text-lg font-bold">Buat Pengajuan Izin</h1>
    <p class="text-sm text-gray-600">Periode: <span class="font-semibold">{{ $registration->periode }}</span></p>

    <div class="mt-3 rounded-xl border border-blue-200 bg-blue-50/70 p-3.5 text-xs text-blue-900 leading-relaxed">
        ℹ️ <b>Pemberitahuan:</b> Pencatatan kehadiran, izin, dan sakit siswa dilakukan dan didata langsung oleh Pembina pada lembar presensi eskul. Anda dapat melampirkan surat bukti di bawah jika diperlukan.
    </div>

    <form method="POST" action="{{ route('siswa.izin.store') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
        @csrf

        <div>
            <label for="eskul" class="block text-sm font-semibold mb-1">Ekstrakurikuler</label>
            <select id="eskul" name="eskul" required class="w-full rounded-xl border-slate-300 px-3.5 py-2.5 pr-10 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">— Pilih Ekstrakurikuler —</option>
                @foreach ($eskulDiikuti as $nama)
                    <option value="{{ $nama }}" @selected(old('eskul') === $nama)>{{ $nama }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="tanggal" class="block text-sm font-semibold mb-1">Tanggal</label>
            <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal') }}" required min="{{ now()->subDays(7)->toDateString() }}" max="{{ now()->addDays(30)->toDateString() }}" class="w-full rounded-xl border-slate-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('tanggal')
                <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="jenis" class="block text-sm font-semibold mb-1">Jenis Izin</label>
            <select id="jenis" name="jenis" required class="w-full rounded-xl border-slate-300 px-3.5 py-2.5 pr-10 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">— Pilih Jenis Izin —</option>
                @foreach ($jenis as $j)
                    <option value="{{ $j->value }}" @selected(old('jenis') === $j->value)>{{ $j->label() }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="alasan" class="block text-sm font-semibold mb-1">Alasan (maks. 500 karakter)</label>
            <textarea id="alasan" name="alasan" rows="4" maxlength="500" required placeholder="Tulis alasan..." class="w-full rounded-lg border-gray-300 px-3 py-2.5 text-sm">{{ old('alasan') }}</textarea>
        </div>

        <div>
            <label for="bukti" class="block text-sm font-semibold mb-1">Lampiran / Bukti Surat (Opsional)</label>
            <input id="bukti" name="bukti" type="file" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            <p class="mt-1 text-xs text-gray-400">Format: JPG, PNG, atau PDF. Maksimal 2MB (Surat dokter, surat orang tua, atau surat dispensasi).</p>
        </div>

        <button type="submit" class="w-full rounded-lg bg-emerald-700 text-white py-3 font-semibold hover:bg-emerald-800">Kirim Pengajuan</button>
        <a href="{{ route('siswa.izin.index') }}" class="block text-center text-sm text-gray-600">Batal</a>
    </form>
</div>
@endsection

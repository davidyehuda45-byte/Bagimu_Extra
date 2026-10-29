<?php

namespace App\Http\Controllers\Siswa;

use App\Enums\Gender;
use App\Enums\Jurusan;
use App\Enums\PeriodeStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Eskul;
use App\Models\PendaftaranWindow;
use App\Models\Periode;
use App\Models\Registration;
use App\Models\StatusHistory;
use App\Models\Student;
use App\Support\Kelas;
use App\Support\Kuota;
use App\Support\Notifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PendaftaranController extends Controller
{
    private function periodeAktif(): ?Periode
    {
        return Periode::where('status', PeriodeStatus::DIBUKA)
            ->orderBy('periode', 'desc')
            ->first();
    }

    /**
     * Langkah 1 alur siswa: Ketentuan dan aturan pendaftaran.
     */
    public function aturan(): View|RedirectResponse
    {
        $user = auth()->user()->load('student');
        $periode = $this->periodeAktif();

        $existing = $periode
            ? Registration::where('user_id', $user->id)->where('periode', $periode->periode)->first()
            : null;

        if ($existing) {
            return redirect()->route('siswa.sukses');
        }

        return view('siswa.aturan', [
            'user' => $user,
            'periode' => $periode,
        ]);
    }

    /**
     * Langkah 2 alur siswa: Pilih ekstrakurikuler.
     */
    public function create(): View|RedirectResponse
    {
        $user = auth()->user()->load('student');
        $periode = $this->periodeAktif();

        $existing = $periode
            ? Registration::where('user_id', $user->id)->where('periode', $periode->periode)->first()
            : null;

        if ($existing) {
            return redirect()->route('siswa.sukses');
        }

        $window = $periode ? PendaftaranWindow::find($periode->periode) : null;
        $windowOpen = $window ? $window->isOpen() : false;

        $eskuls = Eskul::with(['jadwals', 'pembina'])->where('aktif', true)->orderBy('name')->get()
            ->map(fn ($e) => [
                'name' => $e->name,
                'description' => $e->description,
                'kuota' => $e->kuota,
                'kuota_aktif' => $e->kuota_aktif,
                'pembina_nama' => $e->pembina?->name ?? 'Belum ditentukan',
                'jadwals' => $e->jadwals->map(fn ($j) => [
                    'hari' => ucfirst(strtolower($j->hari)),
                    'jam' => $j->jam_mulai.'–'.$j->jam_selesai,
                    'tempat' => $j->tempat,
                ])->values()->all(),
                'terisi' => $periode ? Kuota::terisi($e, $periode->periode, $existing?->id) : 0,
                'jadwal' => $e->jadwals->map(fn ($j) => ucfirst(strtolower($j->hari)).' '.$j->jam_mulai.'–'.$j->jam_selesai.' @ '.$j->tempat)->join(' • ') ?: 'Jadwal menyusul',
            ])
            ->map(fn ($r) => $r + ['penuh' => $r['kuota_aktif'] && $r['terisi'] >= $r['kuota']]);

        $semuaJadwal = Eskul::with('jadwals')->where('aktif', true)->orderBy('name')->get();
        $existingEskul = $existing ? Eskul::with(['jadwals', 'pembina'])->where('name', $existing->eskul_utama)->first() : null;

        // Deep-link dari katalog publik: ?eskul=Nama → preselect bila tersedia & tidak penuh.
        $diminta = trim((string) request()->query('eskul', ''));
        $pilihanAwal = null;
        if ($diminta !== '' && ! $existing) {
            $cocok = $eskuls->firstWhere('name', $diminta);
            if ($cocok && ! $cocok['penuh']) {
                $pilihanAwal = $cocok['name'];
            }
        }

        return view('siswa.daftar', [
            'user' => $user,
            'periode' => $periode,
            'existing' => $existing,
            'existingEskul' => $existingEskul,
            'eskuls' => $eskuls,
            'semuaJadwal' => $semuaJadwal,
            'pilihanAwal' => $pilihanAwal,
            'jurusans' => Jurusan::cases(),
            'genders' => Gender::cases(),
            'windowOpen' => $windowOpen,
            'windowLabel' => $window?->label() ?? 'Jendela belum diatur admin — pendaftaran ditutup.',
        ]);
    }

    /**
     * Langkah 3: Simpan pilihan eskul siswa.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user()->load('student');
        $periode = $this->periodeAktif();

        if (! $periode) {
            return redirect()->route('siswa.aturan')->with('error', 'Belum ada periode pendaftaran yang dibuka.');
        }

        $request->validate([
            'eskul_utama' => ['required', 'string', Rule::exists('eskuls', 'name'),
                function ($attr, $val, $fail) {
                    if (! Eskul::where('name', $val)->where('aktif', true)->exists()) {
                        $fail('Eskul tidak tersedia.');
                    }
                }],
        ]);

        $existing = Registration::where('user_id', $user->id)
            ->where('periode', $periode->periode)
            ->first();

        // Pilihan final: tidak dapat diubah lagi setelah terkirim.
        if ($existing) {
            return redirect()->route('siswa.sukses')
                ->with('status', "Data pendaftaran Anda sudah terkunci pada {$existing->eskul_utama}.");
        }

        // Syarat kelayakan: akun aktif + tingkat kelas + semester.
        $master = $user->student ?? Student::where('nis', $user->username)->first();
        $masterAktif = $master && $master->status === 'AKTIF';

        if (! $masterAktif) {
            return redirect()->route('siswa.aturan')
                ->with('error', 'Akun Anda tidak aktif atau data siswa belum terdaftar. Hubungi TU.');
        }

        $kelasInput = $request->input('kelas');
        $kelas = $kelasInput ? \App\Support\Kelas::normalisasi($kelasInput) : ($master->kelas ?? '');
        $tingkat = \App\Support\Kelas::tingkat($kelas);
        $boleh = array_map('trim', explode(',', \App\Models\AppSetting::get('eskul.kelas_allowed', '10,11')));
        $semester = \App\Models\AppSetting::get('eskul.semester_aktif', '1');

        if (! $tingkat || ! in_array((string) $tingkat, $boleh, true)) {
            return back()->withErrors([
                'kelas' => 'Eskul hanya untuk kelas '.implode(', ', $boleh).'.',
            ]);
        }

        if (
            $tingkat === 11
            && \App\Models\AppSetting::get('eskul.kelas11_smt1_only', '1') === '1'
            && $semester !== '1'
        ) {
            return back()->withErrors([
                'kelas' => 'Kelas 11 hanya bisa ikut eskul di semester 1 (semester 2 magang).',
            ]);
        }

        $window = PendaftaranWindow::find($periode->periode);
        if (! $window || ! $window->isOpen()) {
            return redirect()->route('siswa.aturan')->with('error', 'Pendaftaran sedang tidak dibuka.');
        }

        try {
            [$registration, $dipakai] = DB::transaction(function () use ($request, $user, $periode, $master, $kelas) {
                $dipakai = $this->resolve(
                    $request->input('eskul_utama'),
                    $periode->periode
                );

                if ($request->filled('whatsapp') && $master) {
                    $master->update(['whatsapp' => $request->input('whatsapp')]);
                }

                $wa = $request->input('whatsapp') ?? $master->whatsapp;

                $waliKelasNama = $request->input('nama_wali_kelas')
                    ?? \App\Models\SchoolClass::where('nama', $kelas)->first()?->wali_kelas_nama
                    ?? $master->wali_nama
                    ?? 'Belum ditentukan';

                $registration = Registration::create([
                    'user_id' => $user->id,
                    'periode' => $periode->periode,
                    'kelas' => $kelas,
                    'jurusan' => $request->input('jurusan') ? (\App\Enums\Jurusan::tryFrom($request->input('jurusan')) ?? $master->jurusan) : $master->jurusan,
                    'nis' => $master->nis,
                    'no_absen' => $request->input('no_absen') ?? $master->no_absen ?? 1,
                    'nama_wali_kelas' => $waliKelasNama,
                    'usia' => $request->input('usia') ?? 16,
                    'gender' => $request->input('gender') ? (\App\Enums\Gender::tryFrom($request->input('gender')) ?? $master->gender ?? \App\Enums\Gender::LAKI_LAKI) : ($master->gender ?? \App\Enums\Gender::LAKI_LAKI),
                    'whatsapp' => $wa,
                    'eskul_utama' => $dipakai,
                    'eskul_cadangan' => null,
                    'alasan' => $request->input('alasan'),
                    'alasan_penolakan' => null,
                    'status' => Status::APPROVED,
                    'verify_token' => bin2hex(random_bytes(32)),
                ]);

                StatusHistory::create([
                    'registration_id' => $registration->id,
                    'old_status' => null,
                    'new_status' => Status::APPROVED,
                    'actor_id' => $user->id,
                    'actor_email' => $user->email,
                    'reason' => 'Pendaftaran baru oleh siswa.',
                ]);

                return [$registration, $dipakai];
            });
        } catch (\Illuminate\Database\QueryException $e) {
            $sudah = Registration::where('user_id', $user->id)->where('periode', $periode->periode)->first();
            if ($sudah) {
                return redirect()->route('siswa.sukses')
                    ->with('status', "Anda sudah terdaftar {$sudah->eskul_utama} periode {$periode->periode}.");
            }
            throw $e;
        }

        // Token bersifat sekali pakai: langsung hanguskan token setelah pendaftaran berhasil
        \App\Support\LoginToken::revoke($user);

        $pesan = "Pendaftaran berhasil! Anda resmi terdaftar pada {$dipakai}. Data Anda telah tersimpan dan terkunci.";

        Notifier::statusPendaftaran($registration);

        return redirect()->route('siswa.sukses')
            ->with('status', $pesan)
            ->with('eskul', $dipakai)
            ->with('just_registered', true);
    }

    /**
     * Langkah 4: Halaman sukses pendaftaran + hitung mundur 5 detik auto-logout.
     */
    public function sukses(): View
    {
        $user = auth()->user()->load('student');
        $periode = $this->periodeAktif();

        $registration = $periode
            ? Registration::where('user_id', $user->id)->where('periode', $periode->periode)->latest()->first()
            : null;

        $schoolName = \App\Models\AppSetting::get('school.name', 'SMK Bagimu Negeriku');
        $tahunAjaran = $periode?->tahun_ajaran ?? ($periode?->periode ?? '2026/2027');

        $berlakuSampai = null;
        if ($periode && $periode->tgl_selesai) {
            $berlakuSampai = \Illuminate\Support\Carbon::parse($periode->tgl_selesai)->translatedFormat('d F Y');
        } else {
            $tahunParts = explode('/', $tahunAjaran);
            $berlakuSampai = isset($tahunParts[1]) ? '30 Juni ' . trim($tahunParts[1]) : 'Akhir Tahun Ajaran';
        }

        $qrCodeData = $user->student?->nis ?? ($registration?->nis ?? $user->username);
        $qrSvg = null;
        try {
            if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
                $qrSvg = (string) \SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->margin(0)->generate($qrCodeData);
            }
        } catch (\Throwable $e) {
            $qrSvg = null;
        }

        return view('siswa.sukses', [
            'user' => $user,
            'registration' => $registration,
            'periode' => $periode,
            'schoolName' => $schoolName,
            'tahunAjaran' => $tahunAjaran,
            'berlakuSampai' => $berlakuSampai,
            'qrSvg' => $qrSvg,
            'qrCodeData' => $qrCodeData,
        ]);
    }

    /**
     * Tentukan eskul yang dipakai (tanpa cadangan).
     */
    private function resolve(string $utama, string $periode, ?int $kecualiId = null): string
    {
        $eU = Eskul::where('name', $utama)->lockForUpdate()->first();

        if (! $eU || ! $eU->aktif) {
            throw ValidationException::withMessages(['eskul_utama' => 'Eskul tidak tersedia.']);
        }

        if (Kuota::penuh($eU, $periode, $kecualiId)) {
            throw ValidationException::withMessages([
                'eskul_utama' => "Maaf, kuota {$eU->name} sudah penuh ({$eU->kuota} siswa). Silakan pilih eskul lain.",
            ]);
        }

        return $eU->name;
    }
}

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

    public function create(): View
    {
        $user = auth()->user()->load('student');
        $periode = $this->periodeAktif();

        $existing = $periode
            ? Registration::where('user_id', $user->id)->where('periode', $periode->periode)->first()
            : null;

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
     * Daftar baru maupun ubah data: status selalu APPROVED (tanpa antrean).
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user()->load('student');
        $periode = $this->periodeAktif();

        if (! $periode) {
            return redirect()->route('siswa.status')->with('status', 'Belum ada periode pendaftaran yang dibuka.');
        }

        $validated = $this->validated($request);

        $existing = Registration::where('user_id', $user->id)
            ->where('periode', $periode->periode)
            ->first();

        // Batas pendaftaran per siswa per periode (default 1, dari setting periode).
        $maks = max(1, (int) ($periode->max_eskul_per_siswa ?? 1));
        $jumlah = Registration::where('user_id', $user->id)
            ->where('periode', $periode->periode)
            ->count();

        // Pilihan final: tidak dapat diubah lagi setelah terkirim.
        if ($jumlah >= $maks) {
            $sudah = $existing?->eskul_utama ?? 'eskul';
            return redirect()->route('siswa.status')
                ->with('status', "Anda sudah terdaftar {$sudah} periode {$periode->periode} ({$jumlah}/{$maks}). Pilihan tidak dapat diubah lagi.");
        }

        // Syarat kelayakan: akun aktif + tingkat kelas + semester.
        // Master dicari via relasi, fallback NIS == username (akun lama belum tertaut).
        $master = $user->student ?? Student::where('nis', $user->username)->first();
        $masterAktif = $master && $master->status === 'AKTIF';

        if (! $masterAktif) {
            return redirect()->route('siswa.status')
                ->with('status', 'Akun Anda tidak aktif. Hubungi TU.');
        }

        $tingkat = \App\Support\Kelas::tingkat($validated['kelas']);
        $boleh = array_map('trim', explode(',', \App\Models\AppSetting::get('eskul.kelas_allowed', '10,11')));
        $semester = \App\Models\AppSetting::get('eskul.semester_aktif', '1');

        if (! $tingkat || ! in_array((string) $tingkat, $boleh, true)) {
            return back()->withInput()->withErrors([
                'kelas' => 'Eskul hanya untuk kelas '.implode(', ', $boleh).'.',
            ]);
        }

        if (
            $tingkat === 11
            && \App\Models\AppSetting::get('eskul.kelas11_smt1_only', '1') === '1'
            && $semester !== '1'
        ) {
            return back()->withInput()->withErrors([
                'kelas' => 'Kelas 11 hanya bisa ikut eskul di semester 1 (semester 2 magang).',
            ]);
        }

        $window = PendaftaranWindow::find($periode->periode);
        if (! $window || ! $window->isOpen()) {
            return redirect()->route('siswa.status')->with('status', 'Pendaftaran sedang tidak dibuka.');
        }

        try {
            [$registration, $dipakai, $info] = DB::transaction(function () use ($validated, $user, $periode) {
                [$dipakai, $info] = $this->resolve(
                    $validated['eskul_utama'],
                    $validated['eskul_cadangan'] ?? null,
                    $periode->periode
                );

                $data = array_merge($validated, [
                    'eskul_utama' => $dipakai,
                    'status' => Status::APPROVED,
                    'alasan_penolakan' => null,
                ]);

                $registration = Registration::create(array_merge($data, [
                    'user_id' => $user->id,
                    'periode' => $periode->periode,
                    'nis' => $user->student?->nis ?? $user->username,
                    'verify_token' => bin2hex(random_bytes(32)),
                ]));

                StatusHistory::create([
                    'registration_id' => $registration->id,
                    'old_status' => null,
                    'new_status' => Status::APPROVED,
                    'actor_id' => $user->id,
                    'actor_email' => $user->email,
                    'reason' => 'Pendaftaran baru oleh siswa.',
                ]);

                return [$registration, $dipakai, $info];
            });
        } catch (\Illuminate\Database\QueryException $e) {
            $sudah = Registration::where('user_id', $user->id)->where('periode', $periode->periode)->first();
            if ($sudah) {
                return redirect()->route('siswa.status')
                    ->with('status', "Anda sudah terdaftar {$sudah->eskul_utama} periode {$periode->periode}.");
            }
            throw $e;
        }

        $pesan = "Pendaftaran berhasil. Anda resmi terdaftar {$dipakai}.";

        Notifier::statusPendaftaran($registration);

        if ($info) {
            $pesan .= ' '.$info;
        }

        return redirect()->route('siswa.status')->with('status', $pesan);
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'kelas' => ['required', 'string', 'min:3', 'max:50',
                'regex:/^(XII|XI|X|10|11|12)\s+(RPL|KULINER|DKV|TP|TKP)\s*\d{0,2}$/i',
                function ($attr, $val, $fail) use ($request) {
                    if (
                        preg_match('/^(XII|XI|X|10|11|12)\s+(RPL|KULINER|DKV|TP|TKP)/i', $val, $m)
                        && strtoupper($m[2]) !== strtoupper($request->input('jurusan', ''))
                    ) {
                        $fail('Jurusan di kelas harus sama dengan jurusan yang dipilih.');
                    }
                }],
            'jurusan' => ['required', Rule::enum(Jurusan::class)],
            'usia' => ['required', 'integer', 'min:13', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20', 'regex:/^(\+?62|08)[\d\s\-().]{7,15}$/'],
            'no_absen' => ['required', 'integer', 'min:1', 'max:99'],
            'nama_wali_kelas' => ['nullable', 'string', 'max:100'],
            'gender' => ['required', Rule::enum(Gender::class)],
            'eskul_utama' => ['required', 'string', Rule::exists('eskuls', 'name'),
                function ($attr, $val, $fail) {
                    if (! Eskul::where('name', $val)->where('aktif', true)->exists()) {
                        $fail('Eskul tidak tersedia.');
                    }
                }],
            'eskul_cadangan' => ['nullable', 'string', 'different:eskul_utama', Rule::exists('eskuls', 'name')],
            'alasan' => ['nullable', 'string', 'max:1000'],
        ], [
            'kelas.regex' => 'Format kelas contoh: 10 RPL 1 atau XI RPL 1 (kelas + jurusan + nomor).',
        ]);

        $validated['kelas'] = Kelas::normalisasi($validated['kelas']);

        return $validated;
    }

    /**
     * Tentukan eskul yang dipakai + info pengalihan.
     *
     * @return array{0: string, 1: ?string}
     */
    private function resolve(string $utama, ?string $cadangan, string $periode, ?int $kecualiId = null): array
    {
        $eU = Eskul::where('name', $utama)->lockForUpdate()->first();

        if (! $eU || ! $eU->aktif) {
            throw ValidationException::withMessages(['eskul_utama' => 'Eskul tidak tersedia.']);
        }

        if (! Kuota::penuh($eU, $periode, $kecualiId)) {
            return [$eU->name, null];
        }

        if (! $cadangan) {
            throw ValidationException::withMessages([
                'eskul_utama' => "Maaf, kuota {$eU->name} sudah penuh ({$eU->kuota} siswa). Silakan pilih eskul lain.",
            ]);
        }

        $eC = Eskul::where('name', $cadangan)->lockForUpdate()->first();

        if ($eC && $eC->aktif && ! Kuota::penuh($eC, $periode, $kecualiId)) {
            return [$eC->name, "{$utama} sudah penuh — otomatis dialihkan ke {$cadangan}."];
        }

        throw ValidationException::withMessages([
            'eskul_utama' => "Maaf, {$utama} dan {$cadangan} sama-sama penuh. Silakan pilih eskul lain.",
        ]);
    }
}

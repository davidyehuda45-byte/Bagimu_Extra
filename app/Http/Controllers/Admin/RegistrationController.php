<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Jurusan;
use App\Enums\PeriodeStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Certificate;
use App\Models\Eskul;
use App\Models\NilaiHistory;
use App\Models\Periode;
use App\Models\Registration;
use App\Models\StatusHistory;
use App\Support\CsvExport;
use App\Support\ExcelExport;
use App\Support\Kuota;
use App\Support\LoginToken;
use App\Support\Notifier;
use App\Support\Whatsapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $defaultPeriode = Periode::where('status', PeriodeStatus::DIBUKA->value)->latest('periode')->value('periode')
            ?? Periode::latest('periode')->value('periode');

        $periodeFilter = $request->query('periode', $defaultPeriode);

        $query = $this->terapkanFilter(Registration::with('user')->orderByDesc('created_at'), $request, $periodeFilter);

        $total = (clone $query)->count();

        $registrations = $query->paginate(10)->withQueryString();

        $periodes = Periode::orderByDesc('periode')->get();
        $eskuls = Eskul::orderBy('name')->get();
        $jurusanList = Jurusan::cases();

        return view('admin.registrations.index', compact(
            'registrations',
            'periodes',
            'eskuls',
            'jurusanList',
            'periodeFilter',
            'total'
        ));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function terapkanFilter($query, Request $request, ?string $periodeFilter)
    {
        if ($periodeFilter) {
            $query->where('periode', $periodeFilter);
        }

        if ($request->filled('eskul')) {
            $query->where('eskul_utama', $request->query('eskul'));
        }

        if ($request->filled('jurusan')) {
            $query->where('jurusan', $request->query('jurusan'));
        }

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function ($w) use ($q) {
                $w->where('nis', 'like', "%{$q}%")
                    ->orWhere('kelas', 'like', "%{$q}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"));
            });
        }

        return $query;
    }

    public function export(Request $request)
    {
        $defaultPeriode = Periode::where('status', PeriodeStatus::DIBUKA->value)->latest('periode')->value('periode')
            ?? Periode::latest('periode')->value('periode');

        $query = $this->terapkanFilter(Registration::with('user')->orderByDesc('created_at'), $request, $request->query('periode', $defaultPeriode));

        $headers = ['No', 'Kode Pendaftaran', 'Nama', 'Email', 'NIS', 'Kelas', 'No Absen', 'Wali Kelas', 'Jurusan', 'Jenis Kelamin', 'Usia', 'No WhatsApp', 'Periode', 'Eskul Utama', 'Eskul Cadangan', 'Alasan', 'Nilai', 'Predikat', 'Tanggal Daftar'];

        $tampilkanToken = ! auth()->user()->hasRole(\App\Enums\Role::KEPALA_SEKOLAH);

        if ($tampilkanToken) {
            $headers[] = 'Token Login';
        }

        // Token diambil langsung agar tidak bergantung pada urutan kolom.
        $rows = $query->take(10000)->get()->map(function ($r, $i) use ($tampilkanToken) {
            $baris = [
                $i + 1,
                $r->kode(),
                $r->user?->name ?? '-',
                $r->user?->email ?? '-',
                $r->nis ?? '-',
                $r->kelas,
                $r->no_absen ?? '-',
                $r->nama_wali_kelas ?? '-',
                $r->jurusan?->label() ?? '-',
                $r->gender?->label() ?? '-',
                $r->usia,
                $r->whatsapp ?? '-',
                $r->periode,
                $r->eskul_utama,
                $r->eskul_cadangan ?? '-',
                $r->alasan ?? '-',
                $r->nilai ?? '-',
                $r->predikat ?? '-',
                $r->created_at?->format('d/m/Y H:i'),
            ];

            if ($tampilkanToken) {
                $tokenAktif = ($r->user?->login_token && $r->user?->login_token_revoked_at === null) ? $r->user->login_token : '';
                $baris[] = $tokenAktif !== '' ? $tokenAktif : '-';
            }

            return $baris;
        });

        AdminLog::record('registrations.export', 'Registration', $request->query('periode', '-') ?? '-', 'Export data pendaftar.');

        $namaBerkas = 'pendaftar-eskul-'.date('Y-m-d');

        if ($request->query('format') === 'excel') {
            return ExcelExport::xls($namaBerkas.'.xls', $headers, $rows);
        }

        return CsvExport::download($namaBerkas.'.csv', $headers, $rows);
    }

    public function show(Registration $registration)
    {
        $registration->load(['user', 'statusHistory', 'kehadiran', 'nilaiHistory', 'certificate']);

        $kehadiranTotal = $registration->kehadiran->count();

        $eskulOptions = Eskul::where('aktif', true)->orderBy('name')->get()->map(fn ($e) => [
            'name' => $e->name,
            'info' => $e->kuota_aktif
                ? Kuota::terisi($e, $registration->periode, $registration->id).'/'.$e->kuota
                : 'Bebas',
        ]);

        return view('admin.registrations.show', compact('registration', 'kehadiranTotal', 'eskulOptions'));
    }

    public function update(Request $request, Registration $registration)
    {
        $validated = $request->validate([
            'kelas' => ['required', 'string', 'min:3', 'max:50', 'regex:/^(XII|XI|X|10|11|12)\s+(RPL|KULINER|DKV|TP|TKP)\s*\d{0,2}$/i'],
            'jurusan' => ['required', 'in:RPL,KULINER,DKV,TP,TKP'],
            'usia' => ['required', 'integer', 'min:13', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20', 'regex:/^(\+?62|08)[\d\s\-().]{7,15}$/'],
            'no_absen' => ['required', 'integer', 'min:1', 'max:99'],
            'nama_wali_kelas' => ['nullable', 'string', 'max:100'],
            'nis' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'gender' => ['required', 'in:LAKI_LAKI,PEREMPUAN'],
            'eskul_utama' => ['required', 'string', 'exists:eskuls,name'],
            'eskul_cadangan' => ['nullable', 'string', 'different:eskul_utama', 'exists:eskuls,name'],
            'alasan' => ['nullable', 'string', 'max:1000'],
        ]);

        // Cegah admin memindahkan ke eskul yang sudah penuh.
        if ($validated['eskul_utama'] !== $registration->eskul_utama) {
            $target = Eskul::where('name', $validated['eskul_utama'])->first();
            if ($target && \App\Support\Kuota::penuh($target, $registration->periode, $registration->id)) {
                return back()->withInput()->withErrors([
                    'eskul_utama' => "Kuota {$target->name} sudah penuh ({$target->kuota} siswa).",
                ]);
            }
        }

        $validated['kelas'] = \App\Support\Kelas::normalisasi($validated['kelas']);

        $registration->update($validated);

        AdminLog::record('registration.update', 'registration', $registration->id, "Ubah data pendaftaran #{$registration->id}.");

        return back()->with('status', 'Data pendaftaran diperbarui.');
    }

    public function setNilai(Request $request, Registration $registration)
    {
        $data = $request->validate([
            'nilai' => ['required', 'integer', 'min:0', 'max:100'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($registration->status !== Status::APPROVED) {
            return back()->withErrors(['nilai' => 'Hanya pendaftar yang disetujui yang dapat dinilai.']);
        }

        $predikat = match (true) {
            $data['nilai'] >= 85 => 'A',
            $data['nilai'] >= 70 => 'B',
            $data['nilai'] >= 55 => 'C',
            default => 'D',
        };

        DB::transaction(function () use ($data, $predikat, $registration) {
            $registration->update([
                'nilai' => $data['nilai'],
                'predikat' => $predikat,
                'catatan_nilai' => $data['catatan'] ?? null,
            ]);

            NilaiHistory::create([
                'registration_id' => $registration->id,
                'nilai' => $data['nilai'],
                'predikat' => $predikat,
                'catatan' => $data['catatan'] ?? null,
                'actor_id' => auth()->id(),
                'actor_email' => auth()->user()?->email,
            ]);
        });

        AdminLog::record('registration.nilai', 'registration', $registration->id, "Nilai {$data['nilai']} ({$predikat}) untuk #{$registration->id}.");

        return back()->with('status', "Nilai tersimpan: {$data['nilai']} ({$predikat}).");
    }

    public function cabutKartu(Registration $registration)
    {
        $registration->update([
            'verify_token' => bin2hex(random_bytes(32)),
            'card_revoked_at' => now(),
            'card_version' => $registration->card_version + 1,
        ]);

        AdminLog::record('registration.revoke_card', 'registration', $registration->id, "Cabut kartu & terbitkan QR baru #{$registration->id}.");

        return back()->with('status', 'Kartu lama dicabut, QR E-Card baru diterbitkan. Sertifikat tetap dapat diverifikasi via nomor SRT.');
    }

    public function resetToken(Registration $registration)
    {
        $user = $registration->user;

        if (! $user) {
            return back()->withErrors(['token' => 'Akun siswa tidak ditemukan.']);
        }

        $plain = LoginToken::issue($user);
        // Token baru = kredensial baru: hanguskan sesi aktif lain.
        $user->increment('session_version');

        \App\Support\Notifier::kePengguna($user->id, 'Token login diperbarui', "Admin menerbitkan token baru untuk akun Anda. Token lama hangus. Ambil token baru ke TU bila belum menerimanya.");

        AdminLog::record('user.reset_token', 'User', $user->id, "Reset token login {$user->username}.");

        return back()->with('status', "Token baru untuk {$user->name}: {$plain} — sampaikan SEKARANG, token lama hangus. Siswa juga dapat notifikasi in-app.");
    }

    public function resetPassword(Request $request, Registration $registration)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'max:100'],
        ]);

        $user = $registration->user;

        if (! $user) {
            return back()->withErrors(['password' => 'Akun siswa tidak ditemukan.']);
        }

        $user->forceFill([
            'password' => Hash::make($data['password']),
            'password_set_at' => now(),
        ])->save();
        $user->increment('session_version');

        \App\Support\Notifier::kePengguna($user->id, 'Password diperbarui admin', 'Password akun Anda direset admin. Gunakan password baru untuk login berikutnya.');

        AdminLog::record('user.reset_password', 'User', $user->id, "Reset password {$user->username}.");

        return back()->with('status', 'Password direset. Sampaikan password baru ke siswa (siswa juga dapat notifikasi in-app).');
    }

    public function terbitkanSertifikat(Registration $registration)
    {
        if ($registration->certificate) {
            return back()->with('status', "Sudah memiliki sertifikat {$registration->certificate->nomor}.");
        }

        $cert = DB::transaction(function () use ($registration) {
            if (empty($registration->verify_token)) {
                $registration->verify_token = bin2hex(random_bytes(32));
                $registration->save();
            }

            return Certificate::create([
                'registration_id' => $registration->id,
                'nomor' => CertificateController::nomorBaru(),
                'doc_version' => 1,
                'diterbitkan_oleh' => auth()->user()->email,
            ]);
        });

        AdminLog::record('certificates.store', 'Certificate', (string) $cert->id, "Terbit sertifikat {$cert->nomor} untuk registrasi #{$registration->id}.");

        Notifier::sertifikatTerbit($registration->fresh(), $cert->nomor);

        return back()->with('status', "Sertifikat {$cert->nomor} berhasil diterbitkan.");
    }

    public function destroy(Registration $registration)
    {
        $nama = $registration->user?->name ?? "#{$registration->id}";
        $registration->delete();

        AdminLog::record('registration.delete', 'registration', $registration->id, "Hapus permanen pendaftaran {$nama}.");

        return redirect()->route('admin.registrations.index')->with('status', 'Data pendaftaran dihapus permanen.');
    }
}

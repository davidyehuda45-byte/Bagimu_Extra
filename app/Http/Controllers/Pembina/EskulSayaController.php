<?php

namespace App\Http\Controllers\Pembina;

use App\Enums\IzinStatus;
use App\Enums\KehadiranStatus;
use App\Enums\PeriodeStatus;
use App\Enums\Role;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Eskul;
use App\Models\Izin;
use App\Models\Kehadiran;
use App\Models\Periode;
use App\Models\Registration;
use App\Models\StatusHistory;
use App\Models\Student;
use App\Models\User;
use App\Support\CsvExport;
use App\Support\ExcelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EskulSayaController extends Controller
{
    public function index(Request $request)
    {
        $me = $request->user();

        $milik = Eskul::where('pembina_id', $me->id)->orWhere(fn ($q) => $q->where('temp_pembina_id', $me->id)->whereDate('temp_pembina_sampai', '>=', today()));

        $eskuls = $milik->withCount('jadwals')->orderBy('name')->get();

        return view('pembina.eskul.index', compact('eskuls'));
    }

    public function show(Request $request, Eskul $eskul)
    {
        $me = $request->user();

        $isMilik = $eskul->pembina_id === $me->id
            || ($eskul->temp_pembina_id === $me->id
                && $eskul->temp_pembina_sampai
                && $eskul->temp_pembina_sampai->gte(today()));

        if (! $isMilik) {
            abort(403);
        }

        $allPeriodes = Periode::orderByDesc('periode')->get();
        $activePeriode = $allPeriodes->firstWhere('status', PeriodeStatus::DIBUKA);
        $selectedPeriode = $request->query('periode') ?: ($activePeriode?->periode ?: $allPeriodes->first()?->periode);
        $periodeModel = $allPeriodes->firstWhere('periode', $selectedPeriode);
        $isArsip = $selectedPeriode !== ($activePeriode?->periode ?? '');

        $periodeQuery = $selectedPeriode ? [$selectedPeriode] : [];

        $pendaftar = Registration::with('user')
            ->join('users', 'users.id', '=', 'registrations.user_id')
            ->select('registrations.*')
            ->whereIn('periode', $periodeQuery)
            ->where('eskul_utama', $eskul->name)
            ->orderBy('registrations.kelas')->orderBy('users.name')
            ->get();

        $jadwals = $eskul->jadwals()->orderBy('hari')->orderBy('jam_mulai')->get();

        $izinPending = Izin::with('registration.user')
            ->where('eskul_id', $eskul->id)
            ->where('status', IzinStatus::PENDING)
            ->orderBy('tanggal')
            ->get();

        $tanggal = $request->query('tanggal', today()->toDateString());

        $sortFn = function ($a, $b) {
            $tingkatA = \App\Support\Kelas::tingkat($a->kelas) ?? 99;
            $tingkatB = \App\Support\Kelas::tingkat($b->kelas) ?? 99;
            if ($tingkatA !== $tingkatB) {
                return $tingkatA <=> $tingkatB;
            }
            $cmp = strnatcasecmp($a->kelas ?? '', $b->kelas ?? '');
            if ($cmp !== 0) {
                return $cmp;
            }
            return strcasecmp($a->user?->name ?? '', $b->user?->name ?? '');
        };

        $disetujui = Registration::with('user')
            ->join('users', 'users.id', '=', 'registrations.user_id')
            ->select('registrations.*')
            ->whereIn('periode', $periodeQuery)
            ->where('eskul_utama', $eskul->name)
            ->where('status', Status::APPROVED)
            ->get()
            ->sort($sortFn)
            ->values();

        $pendaftar = Registration::with('user')
            ->join('users', 'users.id', '=', 'registrations.user_id')
            ->select('registrations.*')
            ->whereIn('periode', $periodeQuery)
            ->where('eskul_utama', $eskul->name)
            ->get()
            ->sort($sortFn)
            ->values();

        $peta = Kehadiran::where('eskul_id', $eskul->id)
            ->whereDate('tanggal', $tanggal)
            ->get()->keyBy('registration_id');

        $locked = $peta->isNotEmpty() && $peta->every(fn ($k) => $k->isLocked());

        $rombelStats = $disetujui->groupBy('kelas')->map(function ($members, $kelasName) use ($peta) {
            $total = $members->count();
            $terisi = 0;
            $hadir = 0;
            foreach ($members as $m) {
                $st = $peta[$m->id]?->status?->value ?? null;
                if ($st !== null) {
                    $terisi++;
                    if ($st === 'HADIR') {
                        $hadir++;
                    }
                }
            }
            $belum = $total - $terisi;
            return [
                'kelas' => $kelasName,
                'total' => $total,
                'hadir' => $hadir,
                'terisi' => $terisi,
                'belum' => $belum,
                'lengkap' => $belum === 0 && $total > 0,
            ];
        });

        $pertemuans = $eskul->pertemuans()->orderByDesc('nomor')->get()->map(function ($p) use ($eskul) {
            $p->hadir_count = Kehadiran::where('eskul_id', $eskul->id)
                ->whereDate('tanggal', $p->tanggal)
                ->where('status', KehadiranStatus::HADIR)
                ->count();

            return $p;
        });

        if (! $isArsip) {
            $registeredNis = Registration::whereIn('periode', $periodeQuery)->pluck('nis')->filter()->toArray();
            $calonSiswa = Student::where('status', 'AKTIF')
                ->whereNotIn('nis', $registeredNis)
                ->orderBy('kelas')
                ->orderBy('nama')
                ->get();
        } else {
            $calonSiswa = collect();
        }

        $tingkatCalon = $calonSiswa->pluck('kelas')->map(fn ($k) => \App\Support\Kelas::tingkat($k))->filter()->unique()->sort()->values();
        $daftarKelasCalon = $calonSiswa->pluck('kelas')->filter()->unique()->sort(SORT_NATURAL)->values();

        return view('pembina.eskul.show', compact(
            'eskul',
            'pendaftar',
            'jadwals',
            'izinPending',
            'tanggal',
            'disetujui',
            'peta',
            'locked',
            'pertemuans',
            'rombelStats',
            'calonSiswa',
            'tingkatCalon',
            'daftarKelasCalon',
            'allPeriodes',
            'selectedPeriode',
            'periodeModel',
            'isArsip'
        ));
    }

    public function export(Request $request, Eskul $eskul)
    {
        $me = $request->user();

        $isMilik = $eskul->pembina_id === $me->id
            || ($eskul->temp_pembina_id === $me->id
                && $eskul->temp_pembina_sampai
                && $eskul->temp_pembina_sampai->gte(today()));

        if (! $isMilik) {
            abort(403);
        }

        $allPeriodes = Periode::orderByDesc('periode')->get();
        $activePeriode = $allPeriodes->firstWhere('status', PeriodeStatus::DIBUKA);
        $selectedPeriode = $request->query('periode') ?: ($activePeriode?->periode ?: $allPeriodes->first()?->periode);
        $periodeQuery = $selectedPeriode ? [$selectedPeriode] : [];

        $disetujui = Registration::with(['user', 'kehadiran' => fn ($q) => $q->where('eskul_id', $eskul->id)])
            ->join('users', 'users.id', '=', 'registrations.user_id')
            ->select('registrations.*')
            ->whereIn('periode', $periodeQuery)
            ->where('eskul_utama', $eskul->name)
            ->where('status', Status::APPROVED)
            ->orderBy('registrations.kelas')->orderBy('users.name')
            ->get();

        $headers = [
            'No',
            'NIS',
            'Nama Siswa',
            'Kelas',
            'Jurusan',
            'Jenis Kelamin',
            'Hadir',
            'Izin',
            'Sakit',
            'Alpa',
            'Total Kehadiran',
            'Nilai Akhir',
            'Predikat',
            'Catatan Nilai',
        ];

        $rows = $disetujui->map(function ($r, $index) {
            $hadirCount = $r->kehadiran->where('status', KehadiranStatus::HADIR)->count();
            $izinCount = $r->kehadiran->where('status', KehadiranStatus::IZIN)->count();
            $sakitCount = $r->kehadiran->where('status', KehadiranStatus::SAKIT)->count();
            $alpaCount = $r->kehadiran->where('status', KehadiranStatus::ALPA)->count();
            $totalPertemuan = $r->kehadiran->count();

            return [
                $index + 1,
                $r->nis ?? $r->user?->nis ?? '-',
                $r->user?->name ?? '-',
                $r->kelas ?? '-',
                $r->jurusan instanceof \BackedEnum ? $r->jurusan->value : ($r->jurusan ?? '-'),
                $r->gender instanceof \BackedEnum ? $r->gender->value : ($r->gender ?? '-'),
                $hadirCount,
                $izinCount,
                $sakitCount,
                $alpaCount,
                $totalPertemuan,
                $r->nilai ?? '-',
                $r->predikat ?? '-',
                $r->catatan_nilai ?? '-',
            ];
        });

        $slugEskul = Str::slug($eskul->name);
        $namaBerkas = 'rekap-' . $slugEskul . '-periode-' . Str::slug($selectedPeriode ?: 'all') . '-' . date('Y-m-d');

        if ($request->query('format') === 'excel') {
            return ExcelExport::xls($namaBerkas . '.xls', $headers, $rows);
        }

        return CsvExport::download($namaBerkas . '.csv', $headers, $rows);
    }

    public function tambahAnggota(Request $request, Eskul $eskul)
    {
        $me = $request->user();
        $isMilik = $eskul->pembina_id === $me->id
            || ($eskul->temp_pembina_id === $me->id
                && $eskul->temp_pembina_sampai
                && $eskul->temp_pembina_sampai->gte(today()));

        if (! $isMilik) {
            abort(403);
        }

        $data = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['required', 'integer', 'exists:students,id'],
        ]);

        $periodeAktif = Periode::where('status', PeriodeStatus::DIBUKA)->first();
        if (! $periodeAktif) {
            return back()->with('error', 'Tidak ada periode pendaftaran aktif saat ini.');
        }

        $students = Student::whereIn('id', $data['student_ids'])->where('status', 'AKTIF')->get();
        if ($students->isEmpty()) {
            return back()->with('error', 'Pilih minimal satu data siswa aktif.');
        }

        // Cek sisa kuota jika kuota eskul dibatasi
        if ($eskul->kuota_aktif && $eskul->kuota > 0) {
            $currentCount = Registration::where('periode', $periodeAktif->periode)
                ->where('eskul_utama', $eskul->name)
                ->where('status', Status::APPROVED)
                ->count();

            if ($currentCount + $students->count() > $eskul->kuota) {
                return back()->with('error', "Kuota eskul tidak mencukupi. Sisa kuota tersedia: " . max(0, $eskul->kuota - $currentCount) . " siswa.");
            }
        }

        $count = 0;
        DB::transaction(function () use ($students, $eskul, $periodeAktif, $me, &$count) {
            foreach ($students as $student) {
                // Pastikan siswa belum terdaftar di eskul mana pun pada periode ini
                $sudah = Registration::where('periode', $periodeAktif->periode)
                    ->where(function ($q) use ($student) {
                        $q->where('nis', $student->nis);
                        if ($student->user_id) {
                            $q->orWhere('user_id', $student->user_id);
                        }
                    })
                    ->exists();

                if ($sudah) {
                    continue;
                }

                $user = $student->user;
                if (! $user) {
                    $user = User::create([
                        'name' => $student->nama,
                        'email' => $student->nis . '@siswa.local',
                        'username' => $student->nis,
                        'password' => Hash::make(Str::random(16)),
                        'role' => Role::STUDENT,
                        'student_status' => $student->status ?? 'AKTIF',
                    ]);
                    $student->update(['user_id' => $user->id]);
                }

                $wa = $student->whatsapp ?: ($student->wali_kontak ?: '');
                $reg = Registration::create([
                    'user_id' => $user->id,
                    'periode' => $periodeAktif->periode,
                    'kelas' => $student->kelas ?? '10',
                    'jurusan' => $student->jurusan,
                    'nis' => $student->nis,
                    'no_absen' => $student->no_absen ?? 1,
                    'nama_wali_kelas' => $student->wali_nama ?? 'Belum ditentukan',
                    'usia' => $student->usia ?? 16,
                    'gender' => $student->gender ?? \App\Enums\Gender::LAKI_LAKI,
                    'whatsapp' => $wa,
                    'eskul_utama' => $eskul->name,
                    'eskul_cadangan' => null,
                    'alasan' => 'Didaftarkan manual oleh Pembina Eskul ' . $me->name,
                    'status' => Status::APPROVED,
                    'verify_token' => bin2hex(random_bytes(32)),
                ]);

                StatusHistory::create([
                    'registration_id' => $reg->id,
                    'old_status' => null,
                    'new_status' => Status::APPROVED,
                    'actor_id' => (string) $me->id,
                    'actor_email' => $me->email,
                    'reason' => 'Didaftarkan manual oleh Pembina Eskul ' . $me->name,
                ]);

                $count++;
            }
        });

        AdminLog::record('pembina.add_students', 'Registration', null, "Pembina {$me->name} mendaftarkan {$count} siswa secara manual ke eskul {$eskul->name}.");

        return back()->with('status', "Berhasil menambahkan {$count} siswa ke dalam ekstrakurikuler {$eskul->name}.");
    }

    public function hapusAnggota(Request $request, Eskul $eskul, Registration $registration)
    {
        $me = $request->user();
        $isMilik = $eskul->pembina_id === $me->id
            || ($eskul->temp_pembina_id === $me->id
                && $eskul->temp_pembina_sampai
                && $eskul->temp_pembina_sampai->gte(today()));

        if (! $isMilik) {
            abort(403);
        }

        if ($registration->eskul_utama !== $eskul->name) {
            return back()->with('error', 'Siswa ini bukan anggota dari ekstrakurikuler Anda.');
        }

        // Guard keselamatan data: Cek apakah siswa sudah memiliki catatan presensi atau nilai
        $hasPresensi = Kehadiran::where('registration_id', $registration->id)->exists();
        $hasNilai = $registration->nilai !== null;

        if ($hasPresensi || $hasNilai) {
            return back()->with('error', "Siswa {$registration->user?->name} sudah memiliki riwayat presensi atau nilai dan tidak dapat dikeluarkan sembarangan. Silakan hubungi admin kesiswaan jika perlu pembatalan khusus.");
        }

        $nama = $registration->user?->name ?? $registration->nis;
        $registration->delete();

        AdminLog::record('pembina.remove_student', 'Registration', (string) $registration->id, "Pembina {$me->name} mengeluarkan siswa {$nama} dari eskul {$eskul->name}.");

        return back()->with('status', "Siswa {$nama} berhasil dikeluarkan dari eskul {$eskul->name}. Siswa kini dapat memilih eskul kembali.");
    }
}

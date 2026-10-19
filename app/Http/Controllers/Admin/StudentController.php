<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Gender;
use App\Enums\Jurusan;
use App\Enums\PeriodeStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Periode;
use App\Models\Student;
use App\Models\User;
use App\Services\FonnteService;
use App\Support\LoginToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    protected function filterStudentsQuery(Request $request)
    {
        $query = Student::query();

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where(function ($w) use ($q) {
                $w->where('nis', 'like', "%{$q}%")
                  ->orWhere('nisn', 'like', "%{$q}%")
                  ->orWhere('nama', 'like', "%{$q}%");
            });
        }

        if ($request->filled('tingkat')) {
            $tingkat = trim((string) $request->input('tingkat'));
            $romawiMap = ['10' => 'X', '11' => 'XI', '12' => 'XII'];
            $romawi = $romawiMap[$tingkat] ?? $tingkat;
            $query->where(function ($w) use ($tingkat, $romawi) {
                $w->where('kelas', 'like', "{$tingkat} %")
                  ->orWhere('kelas', 'like', "{$romawi} %")
                  ->orWhere('kelas', 'like', "{$tingkat}-%")
                  ->orWhere('kelas', 'like', "{$romawi}-%")
                  ->orWhere('kelas', '=', $tingkat)
                  ->orWhere('kelas', '=', $romawi);
            });
        }

        if ($request->filled('kelas')) {
            $kelas = trim((string) $request->input('kelas'));
            $norm = \App\Support\Kelas::normalisasi($kelas);
            $query->where(function ($w) use ($kelas, $norm) {
                $w->where('kelas', $kelas);
                if ($norm && $norm !== $kelas) {
                    $w->orWhere('kelas', $norm);
                }
            });
        }

        if ($request->filled('jurusan')) {
            $query->where('jurusan', $request->input('jurusan'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('eskul_status')) {
            $eskulStatus = $request->input('eskul_status');
            $activePeriode = Periode::where('status', PeriodeStatus::DIBUKA)->orderByDesc('periode')->first()
                ?? Periode::orderByDesc('periode')->first();

            if ($activePeriode) {
                if ($eskulStatus === 'terdaftar') {
                    $query->whereHas('user.registrations', function ($r) use ($activePeriode) {
                        $r->where('periode', $activePeriode->periode);
                    });
                } elseif ($eskulStatus === 'belum') {
                    $query->where(function ($q) use ($activePeriode) {
                        $q->whereNull('user_id')
                          ->orWhereDoesntHave('user.registrations', function ($r) use ($activePeriode) {
                              $r->where('periode', $activePeriode->periode);
                          });
                    });
                }
            }
        }

        return $query;
    }

    public function index(Request $request)
    {
        $periode = Periode::where('status', PeriodeStatus::DIBUKA)->orderByDesc('periode')->first()
            ?? Periode::orderByDesc('periode')->first();

        $query = $this->filterStudentsQuery($request)
            ->with(['user.registrations' => function ($q) use ($periode) {
                if ($periode) {
                    $q->where('periode', $periode->periode);
                }
            }])
            ->orderBy('kelas')
            ->orderBy('nama');

        $students = $query->paginate(20)->withQueryString();
        $jurusanList = Jurusan::cases();
        $statusList = ['AKTIF', 'LULUS', 'PINDAH', 'NONAKTIF', 'ALUMNI'];
        $tingkatList = ['10', '11', '12'];

        $kelasList = Student::query()
            ->whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->distinct()
            ->pluck('kelas')
            ->sort(SORT_NATURAL)
            ->values();

        $fonnte = (new FonnteService)->getStatus();
        $totalWaStudents = Student::query()->where('status', 'AKTIF')->where(function ($q) {
            $q->whereNotNull('whatsapp')->where('whatsapp', '!=', '');
        })->count();

        if ($request->ajax()) {
            return response()->json([
                'rows' => view('admin.students.partials.rows', compact('students', 'periode'))->render(),
                'pagination' => $students->links()->toHtml(),
                'total' => $students->total(),
            ]);
        }

        return view('admin.students.index', compact('students', 'jurusanList', 'statusList', 'tingkatList', 'kelasList', 'periode', 'fonnte', 'totalWaStudents'));
    }

    public function create()
    {
        $jurusanList = Jurusan::cases();
        $genderList = Gender::cases();

        return view('admin.students.create', compact('jurusanList', 'genderList'));
    }

    public function store(Request $request)
    {
        if (is_string($request->input('nisn')) && trim($request->input('nisn')) === '') {
            $request->merge(['nisn' => null]);
        }
        $data = $request->validate([
            'nis' => ['required', 'string', 'max:20', 'unique:students,nis'],
            'nisn' => ['nullable', 'string', 'max:20', 'unique:students,nisn'],
            'nama' => ['required', 'string', 'max:100'],
            'kelas' => ['required', 'string', 'min:3', 'max:50', 'regex:/^(XII|XI|X|10|11|12)\s+(RPL|KULINER|DKV|TP|TKP)\s*\d{0,2}$/i'],
            'jurusan' => ['required', Rule::enum(Jurusan::class)],
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'no_absen' => ['nullable', 'integer', 'min:1', 'max:99'],
            'wali_nama' => ['nullable', 'string', 'max:100'],
            'wali_kontak' => ['nullable', 'string', 'max:20', 'regex:/^(\+?62|08)[\d\s\-().]{7,15}$/'],
            'whatsapp' => ['nullable', 'string', 'max:20', 'regex:/^(\+?62|08)[\d\s\-().]{7,15}$/'],
            'status' => ['nullable', 'string', 'in:AKTIF,LULUS,PINDAH,NONAKTIF,ALUMNI'],
        ]);

        $data['status'] = $data['status'] ?? 'AKTIF';
        $data['kelas'] = \App\Support\Kelas::normalisasi($data['kelas']) ?? $data['kelas'];

        $student = Student::create($data);

        AdminLog::record('student.create', 'student', $student->id, "Menambah siswa NIS {$student->nis}");

        return redirect()->route('admin.students.index')->with('status', 'Data siswa berhasil ditambah.');
    }

    public function show(Student $student)
    {
        $student->load('user');

        $riwayat = $student->user
            ? $student->user->registrations()->orderByDesc('periode')->take(5)->get()
            : collect();

        return view('admin.students.show', compact('student', 'riwayat'));
    }

    public function edit(Student $student)
    {
        $jurusanList = Jurusan::cases();
        $genderList = Gender::cases();

        return view('admin.students.edit', compact('student', 'jurusanList', 'genderList'));
    }

    public function update(Request $request, Student $student)
    {
        if (is_string($request->input('nisn')) && trim($request->input('nisn')) === '') {
            $request->merge(['nisn' => null]);
        }
        $data = $request->validate([
            'nis' => ['required', 'string', 'max:20', 'unique:students,nis,'.$student->id],
            'nisn' => ['nullable', 'string', 'max:20', 'unique:students,nisn,'.$student->id],
            'nama' => ['required', 'string', 'max:100'],
            'kelas' => ['required', 'string', 'min:3', 'max:50', 'regex:/^(XII|XI|X|10|11|12)\s+(RPL|KULINER|DKV|TP|TKP)\s*\d{0,2}$/i'],
            'jurusan' => ['required', Rule::enum(Jurusan::class)],
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'no_absen' => ['nullable', 'integer', 'min:1', 'max:99'],
            'wali_nama' => ['nullable', 'string', 'max:100'],
            'wali_kontak' => ['nullable', 'string', 'max:20', 'regex:/^(\+?62|08)[\d\s\-().]{7,15}$/'],
            'whatsapp' => ['nullable', 'string', 'max:20', 'regex:/^(\+?62|08)[\d\s\-().]{7,15}$/'],
            'status' => ['nullable', 'string', 'in:AKTIF,LULUS,PINDAH,NONAKTIF,ALUMNI'],
            'alasan' => ['required', 'string', 'max:500'],
        ]);

        $nisLama = $student->nis;
        $alasan = $data['alasan'];
        unset($data['alasan']);
        $data['kelas'] = \App\Support\Kelas::normalisasi($data['kelas']) ?? $data['kelas'];

        $student->update($data);

        // Koreksi NIS merambat ke data pendaftaran.
        if ($nisLama !== $student->nis && $student->user_id) {
            \App\Models\Registration::where('user_id', $student->user_id)->update(['nis' => $student->nis]);
        }

        // Status merambat ke akun tertaut.
        if ($student->user && in_array($student->status, ['AKTIF', 'LULUS', 'PINDAH', 'NONAKTIF', 'ALUMNI'], true)) {
            $student->user->update(['student_status' => $student->status]);
        }

        AdminLog::record('student.update', 'student', $student->id, "Koreksi siswa NIS {$nisLama} → {$student->nis}. Alasan: {$alasan}");

        return redirect()->route('admin.students.index')->with('status', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $nis = $student->nis;
        $student->delete();

        AdminLog::record('student.delete', 'student', $student->id, "Menghapus siswa NIS {$nis}");

        return redirect()->route('admin.students.index')->with('status', 'Data siswa berhasil dihapus.');
    }

    public function bulkToken(Request $request)
    {
        $mode = $request->input('mode', 'untokenized'); // 'untokenized', 'all', or 'selected'
        $selectedIds = (array) $request->input('ids', []);

        if ($mode === 'selected') {
            if (empty($selectedIds)) {
                return back()->with('error', 'Pilih minimal satu data siswa.');
            }
            $students = Student::with('user')->whereIn('id', $selectedIds)->get();
        } else {
            $students = $this->filterStudentsQuery($request)->with('user')->get();
        }

        $count = 0;
        foreach ($students as $student) {
            $user = $student->user;

            // Jika mode 'untokenized', lewati jika token sudah aktif
            if ($mode === 'untokenized' && $user && $user->login_token && $user->login_token_revoked_at === null) {
                continue;
            }

            // Lewati jika siswa sudah memilih eskul (data pendaftaran terkunci)
            $sudahPilih = \App\Models\Registration::where('nis', $student->nis)
                ->orWhere(function ($q) use ($student) {
                    if ($student->user_id) {
                        $q->where('user_id', $student->user_id);
                    }
                })
                ->exists();

            if ($sudahPilih) {
                continue;
            }

            if (! $user) {
                $user = User::create([
                    'name' => $student->nama,
                    'email' => $student->nis.'@siswa.local',
                    'username' => $student->nis,
                    'password' => Hash::make(Str::random(16)),
                    'role' => Role::STUDENT,
                    'student_status' => $student->status ?? 'AKTIF',
                ]);

                $student->update(['user_id' => $user->id]);
            }

            LoginToken::issue($user);
            $count++;
        }

        AdminLog::record('student.bulk_token', 'student', null, "Generate token massal untuk {$count} siswa (Mode: {$mode})");

        $message = "Berhasil menerbitkan/memperbarui token login untuk {$count} siswa.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('status', $message);
    }

    public function bulkDelete(Request $request)
    {
        $mode = $request->input('mode', 'selected'); // 'selected' or 'filter'
        $selectedIds = (array) $request->input('ids', []);

        if ($mode === 'selected') {
            if (empty($selectedIds)) {
                return back()->with('error', 'Pilih minimal satu data siswa untuk dihapus.');
            }
            $students = Student::whereIn('id', $selectedIds)->get();
        } else {
            $students = $this->filterStudentsQuery($request)->get();
        }

        $count = $students->count();

        if ($count === 0) {
            return back()->with('warning', 'Tidak ada data siswa yang cocok untuk dihapus.');
        }

        foreach ($students as $student) {
            $student->delete();
        }

        AdminLog::record('student.bulk_delete', 'student', null, "Menghapus {$count} data siswa secara massal");

        $message = "Berhasil menghapus {$count} data siswa secara permanen.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('status', $message);
    }

    public function waBlast(Request $request, FonnteService $fonnte)
    {
        $status = $fonnte->getStatus();
        if (! $status['connected']) {
            $msg = 'WhatsApp Gateway (Fonnte) belum terhubung. Silakan hubungkan API Key Fonnte di menu Pengaturan terlebih dahulu.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return back()->with('error', $msg);
        }

        $request->validate([
            'mode' => ['required', 'string', 'in:all,selected,untokenized'],
            'pesan' => ['required', 'string', 'max:2000'],
            'ids' => ['nullable', 'array'],
        ]);

        $mode = $request->input('mode', 'all');
        $selectedIds = (array) $request->input('ids', []);
        $pesanTemplate = $request->input('pesan');

        if ($mode === 'selected') {
            if (empty($selectedIds)) {
                return back()->with('error', 'Pilih minimal satu data siswa untuk dikirim.');
            }
            $students = Student::with(['user', 'user.registrations'])->whereIn('id', $selectedIds)->get();
        } else {
            $query = $this->filterStudentsQuery($request)->with(['user', 'user.registrations']);
            if ($mode === 'untokenized') {
                $query->whereHas('user', function ($u) {
                    $u->whereNull('login_token')->orWhereNotNull('login_token_revoked_at');
                });
            }
            $students = $query->get();
        }

        // Filter hanya siswa yang memiliki nomor WhatsApp
        $targets = $students->filter(function ($s) {
            $reg = $s->user?->registrations?->first();
            $wa = $s->whatsapp ?: ($s->wali_kontak ?: ($reg?->whatsapp ?? ''));

            return ! empty(trim((string) $wa));
        });

        if ($targets->isEmpty()) {
            $msg = 'Tidak ada siswa dengan nomor WhatsApp terdaftar pada target yang dipilih.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return back()->with('warning', $msg);
        }

        @set_time_limit(300);

        $successCount = 0;
        $failCount = 0;
        $appName = config('app.name', 'Bagimu Extra');
        $loginUrl = route('siswa.login');

        foreach ($targets as $student) {
            $reg = $student->user?->registrations?->first();
            $rawWa = $student->whatsapp ?: ($student->wali_kontak ?: ($reg?->whatsapp ?? ''));
            $normWa = \App\Support\Whatsapp::normalize($rawWa);

            if (! $normWa) {
                $failCount++;
                continue;
            }

            $user = $student->user;
            if (! $user) {
                $user = User::create([
                    'name' => $student->nama,
                    'email' => $student->nis.'@siswa.local',
                    'username' => $student->nis,
                    'password' => Hash::make(Str::random(16)),
                    'role' => Role::STUDENT,
                ]);
                $student->update(['user_id' => $user->id]);
            }

            $token = $user->login_token;
            if (! $token || $user->login_token_revoked_at) {
                $token = LoginToken::issue($user);
            }

            $directLoginUrl = route('siswa.direct-login', [
                'nis' => $student->nis,
                'token' => $token,
            ]);

            $replacements = [
                '{nama}' => $student->nama,
                '{nis}' => $student->nis,
                '{nisn}' => $student->nisn ?? '-',
                '{kelas}' => $student->kelas,
                '{token}' => $token,
                '{link}' => $directLoginUrl,
                '{login_url}' => $loginUrl,
                '{aplikasi}' => $appName,
                '{eskul}' => $reg?->eskul_utama ?? 'Belum memilih',
            ];

            $renderedMessage = str_replace(array_keys($replacements), array_values($replacements), $pesanTemplate);

            $sendResult = $fonnte->sendMessage($normWa, $renderedMessage);

            if ($sendResult['success']) {
                $successCount++;
            } else {
                $failCount++;
            }

            usleep(30000); // 30ms
        }

        AdminLog::record('student.wa_blast', 'student', null, "WA Blast: {$successCount} sukses, {$failCount} gagal (Target: {$targets->count()})");

        $statusMsg = "WA Blast selesai: {$successCount} pesan berhasil dikirim".($failCount > 0 ? ", {$failCount} gagal." : '.');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'sent' => $successCount,
                'failed' => $failCount,
                'total' => $targets->count(),
                'message' => $statusMsg,
            ]);
        }

        return redirect()->back()->with('status', $statusMsg);
    }

    public function statusRombel(Request $request)
    {
        $periodes = Periode::orderByDesc('periode')->get();
        $periodeParam = $request->query('periode');
        $periode = $periodeParam
            ? Periode::where('periode', $periodeParam)->first()
            : (Periode::where('status', PeriodeStatus::DIBUKA)->orderByDesc('periode')->first() ?? $periodes->first());

        $tingkatFilter = trim((string) $request->input('tingkat'));
        $jurusanFilter = trim((string) $request->input('jurusan'));

        $studentsQuery = Student::query();

        if ($tingkatFilter) {
            $romawiMap = ['10' => 'X', '11' => 'XI', '12' => 'XII'];
            $romawi = $romawiMap[$tingkatFilter] ?? $tingkatFilter;
            $studentsQuery->where(function ($w) use ($tingkatFilter, $romawi) {
                $w->where('kelas', 'like', "{$tingkatFilter} %")
                  ->orWhere('kelas', 'like', "{$romawi} %")
                  ->orWhere('kelas', 'like', "{$tingkatFilter}-%")
                  ->orWhere('kelas', 'like', "{$romawi}-%")
                  ->orWhere('kelas', '=', $tingkatFilter)
                  ->orWhere('kelas', '=', $romawi);
            });
        }

        if ($jurusanFilter) {
            $studentsQuery->where('jurusan', $jurusanFilter);
        }

        $allStudents = $studentsQuery->with(['user.registrations' => function ($q) use ($periode) {
            if ($periode) {
                $q->where('periode', $periode->periode);
            }
        }])->get();

        $groupedByKelas = $allStudents->groupBy('kelas')->sortKeys(SORT_NATURAL);

        $rombelStats = [];
        $totalSemuaSiswa = 0;
        $totalSemuaTerdaftar = 0;

        foreach ($groupedByKelas as $kelasName => $studentsInKelas) {
            $totalInKelas = $studentsInKelas->count();
            $registeredStudents = $studentsInKelas->filter(function ($s) use ($periode) {
                return $s->user && $s->user->registrations->isNotEmpty();
            });

            $unregisteredStudents = $studentsInKelas->filter(function ($s) use ($periode) {
                return ! $s->user || $s->user->registrations->isEmpty();
            });

            $registeredCount = $registeredStudents->count();
            $unregisteredCount = $totalInKelas - $registeredCount;
            $percent = $totalInKelas > 0 ? round(($registeredCount / $totalInKelas) * 100) : 0;

            $totalSemuaSiswa += $totalInKelas;
            $totalSemuaTerdaftar += $registeredCount;

            $rombelStats[] = [
                'kelas' => $kelasName,
                'total' => $totalInKelas,
                'terdaftar' => $registeredCount,
                'belum' => $unregisteredCount,
                'persentase' => $percent,
                'unregistered_samples' => $unregisteredStudents->take(5)->pluck('nama')->all(),
            ];
        }

        $tingkatList = ['10', '11', '12'];
        $jurusanList = Jurusan::cases();

        return view('admin.students.status-rombel', compact(
            'rombelStats',
            'periode',
            'periodes',
            'totalSemuaSiswa',
            'totalSemuaTerdaftar',
            'tingkatList',
            'jurusanList',
            'tingkatFilter',
            'jurusanFilter'
        ));
    }

    public function export(Request $request)
    {
        $format = strtolower((string) $request->query('format', 'xlsx'));
        $periode = Periode::where('status', PeriodeStatus::DIBUKA)->orderByDesc('periode')->first()
            ?? Periode::orderByDesc('periode')->first();

        $students = $this->filterStudentsQuery($request)
            ->with(['user.registrations' => function ($q) use ($periode) {
                if ($periode) {
                    $q->where('periode', $periode->periode);
                }
            }])
            ->orderBy('kelas')
            ->orderBy('nama')
            ->get();

        $headers = [
            'No',
            'NIS',
            'NISN',
            'Nama Lengkap',
            'Kelas',
            'Jurusan',
            'Status Siswa',
            'No. Absen',
            'Gender',
            'No. WhatsApp',
            'Token Login',
            'Status Eskul',
            'Eskul Terpilih',
        ];

        $rows = [];
        $no = 1;
        foreach ($students as $s) {
            $reg = $s->user?->registrations->first();
            $wa = $s->whatsapp ?: ($s->wali_kontak ?: ($reg?->whatsapp ?? ''));
            $token = ($s->user?->login_token && $s->user?->login_token_revoked_at === null) ? $s->user->login_token : '';
            $statusEskul = $reg ? 'Terdaftar' : 'Belum Memilih';
            $eskulUtama = $reg ? $reg->eskul_utama : '';

            $rows[] = [
                $no++,
                (string) $s->nis,
                (string) ($s->nisn ?? ''),
                $s->nama,
                $s->kelas,
                $s->jurusan?->short() ?? (string) $s->jurusan,
                $s->status,
                $s->no_absen ?? '',
                $s->gender?->value ?? '',
                $wa,
                $token,
                $statusEskul,
                $eskulUtama,
            ];
        }

        $filename = 'data-siswa-' . date('Ymd_His');

        if ($format === 'csv') {
            return \App\Support\CsvExport::download($filename . '.csv', $headers, $rows);
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Direktori Siswa');

        $sheet->fromArray([$headers, ...$rows], null, 'A1', true);

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E40AF'],
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(26);

        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $filename . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function importForm()
    {
        $zipAktif = extension_loaded('zip');

        return view('admin.students.import', compact('zipAktif'));
    }

    public function template(Request $request)
    {
        $format = strtolower((string) $request->query('format', 'csv'));

        $headers = ['nis', 'nisn', 'nama', 'kelas', 'jurusan', 'gender', 'no_absen'];
        $contoh = [
            ['10001', '0081234567', 'Andi Pratama', '10 RPL 1', 'RPL', 'L', '1'],
            ['10002', '', 'Citra Ayu', '11 DKV 1', 'DKV', 'P', '2'],
            ['10003', '', 'Budi Santoso', '10 KULINER 2', 'KULINER', 'L', '3'],
        ];

        if ($format === 'xlsx') {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Siswa');
            $sheet->fromArray([$headers, ...$contoh], null, 'A1', true);
            foreach (range('A', 'G') as $kolom) {
                $sheet->getColumnDimension($kolom)->setAutoSize(true);
            }

            return response()->streamDownload(function () use ($spreadsheet) {
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                $spreadsheet->disconnectWorksheets();
            }, 'template-siswa.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        return \App\Support\CsvExport::download('template-siswa.csv', $headers, $contoh);
    }

    public function import(Request $request)
    {
        $request->validate(
            [
                'file' => ['required', 'file', 'max:10240', 'extensions:csv,txt,xlsx,xls,ods,pdf'],
            ],
            [
                'file.required' => 'Pilih dulu berkas yang akan diimpor.',
                'file.file' => 'Unggahan tidak valid. Silakan coba lagi.',
                'file.max' => 'Ukuran berkas maksimal 10 MB.',
                'file.extensions' => 'Format berkas harus salah satu: .csv, .txt, .xlsx, .xls, .ods, atau .pdf.',
            ]
        );

        $uploaded = $request->file('file');
        $path = $uploaded->getRealPath();

        if ($path === false) {
            return back()->with('error', 'Berkas unggahan tidak dapat dibaca server (folder temporary PHP tidak bisa ditulis). Restart Laragon lalu coba lagi.');
        }

        try {
            $hasil = \App\Support\StudentFileReader::readFileDetailed(
                $path,
                $uploaded->getClientOriginalName(),
                $uploaded->getMimeType()
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Berkas gagal diproses. Pastikan file tidak korup dan berformat .csv/.xlsx/.xls/.ods/.pdf yang valid.');
        }

        $records = $hasil['records'];

        $baru = 0;
        $diperbarui = 0;
        $gagal = 0;
        $gagalSampel = [];

        foreach ($records as $item) {
            $nis = $item['nis'];
            $exists = Student::where('nis', $nis)->exists();

            try {
                Student::updateOrCreate(
                    ['nis' => $nis],
                    [
                        'nisn' => $item['nisn'],
                        'nama' => $item['nama'],
                        'kelas' => $item['kelas'],
                        'jurusan' => $item['jurusan'],
                        'gender' => $item['gender'],
                        'no_absen' => $item['no_absen'],
                    ]
                );
                $exists ? $diperbarui++ : $baru++;
            } catch (\Illuminate\Database\QueryException $e) {
                // Umumnya NISN duplikat milik NIS lain.
                $gagal++;
                if (count($gagalSampel) < 5) {
                    $gagalSampel[] = "NIS {$nis} (kemungkinan NISN sudah dipakai siswa lain)";
                }
            } catch (\Throwable $e) {
                report($e);
                $gagal++;
                if (count($gagalSampel) < 5) {
                    $gagalSampel[] = "NIS {$nis}";
                }
            }
        }

        $orig = $uploaded->getClientOriginalName();
        AdminLog::record('student.import', 'student', null, "Impor berkas {$orig}: {$baru} baru, {$diperbarui} diperbarui, {$gagal} gagal");

        $pesan = "Impor selesai ({$orig}): {$baru} data baru, {$diperbarui} diperbarui, {$gagal} gagal.";
        if (($hasil['skipped'] ?? 0) > 0) {
            $pesan .= " {$hasil['skipped']} baris dilewati karena tidak valid.";
        }
        if (($hasil['non_siswa'] ?? 0) > 0) {
            $pesan .= " {$hasil['non_siswa']} baris guru/staf dilewati.";
        }
        if (! empty($gagalSampel)) {
            $pesan .= ' Contoh gagal: '.implode('; ', $gagalSampel).'.';
        }

        if ($baru === 0 && $diperbarui === 0) {
            return redirect()->route('admin.students.index')->with('warning', $pesan);
        }

        return redirect()->route('admin.students.index')->with('status', $pesan);
    }

    public function issueToken(Student $student)
    {
        // Tolak jika siswa sudah memilih eskul (data terkunci)
        $sudahPilih = \App\Models\Registration::where('nis', $student->nis)
            ->orWhere(function ($q) use ($student) {
                if ($student->user_id) {
                    $q->where('user_id', $student->user_id);
                }
            })
            ->exists();

        if ($sudahPilih) {
            return redirect()->back()->with('error', "Siswa {$student->nama} (NIS: {$student->nis}) sudah memilih ekstrakurikuler dan datanya terkunci. Token baru tidak dapat diterbitkan.");
        }

        $user = $student->user;

        if (! $user) {
            $user = User::create([
                'name' => $student->nama,
                'email' => $student->nis.'@siswa.local',
                'username' => $student->nis,
                'password' => Hash::make(Str::random(16)),
                'role' => Role::STUDENT,
            ]);

            $student->update(['user_id' => $user->id]);
        }

        $plain = LoginToken::issue($user);
        $user->increment('session_version');

        \App\Support\Notifier::kePengguna($user->id, 'Token login diperbarui', "Admin menerbitkan token baru untuk NIS {$student->nis}. Token lama hangus.");

        AdminLog::record('student.token', 'student', $student->id, "Menerbitkan token login NIS {$student->nis}");

        return redirect()->back()->with('status', "Token baru NIS {$student->nis}: {$plain} — catat/cetak SEKARANG, tidak ditampilkan lagi! Siswa juga dapat notifikasi in-app.");
    }

    public function revokeToken(Student $student)
    {
        $user = $student->user;

        if (! $user) {
            return redirect()->back()->with('status', 'Siswa belum memiliki akun login.');
        }

        LoginToken::revoke($user);
        $user->increment('session_version');

        AdminLog::record('student.token.revoke', 'student', $student->id, "Mencabut token login NIS {$student->nis}");

        return redirect()->back()->with('status', "Token login NIS {$student->nis} telah dicabut.");
    }
}

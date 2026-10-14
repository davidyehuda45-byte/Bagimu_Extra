<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Gender;
use App\Enums\Jurusan;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Student;
use App\Models\User;
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

        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->filterStudentsQuery($request)
            ->with('user')
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

        if ($request->ajax()) {
            return response()->json([
                'rows' => view('admin.students.partials.rows', compact('students'))->render(),
                'pagination' => $students->links()->toHtml(),
                'total' => $students->total(),
            ]);
        }

        return view('admin.students.index', compact('students', 'jurusanList', 'statusList', 'tingkatList', 'kelasList'));
    }

    public function create()
    {
        $jurusanList = Jurusan::cases();
        $genderList = Gender::cases();

        return view('admin.students.create', compact('jurusanList', 'genderList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nis' => ['required', 'string', 'max:20', 'unique:students,nis'],
            'nisn' => ['nullable', 'string', 'max:20', 'unique:students,nisn'],
            'nama' => ['required', 'string', 'max:100'],
            'kelas' => ['required', 'string', 'max:50'],
            'jurusan' => ['required', Rule::enum(Jurusan::class)],
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'no_absen' => ['nullable', 'integer', 'min:1'],
            'wali_nama' => ['nullable', 'string', 'max:100'],
            'wali_kontak' => ['nullable', 'string', 'max:20'],
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
        $data = $request->validate([
            'nis' => ['required', 'string', 'max:20', 'unique:students,nis,'.$student->id],
            'nisn' => ['nullable', 'string', 'max:20', 'unique:students,nisn,'.$student->id],
            'nama' => ['required', 'string', 'max:100'],
            'kelas' => ['required', 'string', 'max:50'],
            'jurusan' => ['required', Rule::enum(Jurusan::class)],
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'no_absen' => ['nullable', 'integer', 'min:1'],
            'wali_nama' => ['nullable', 'string', 'max:100'],
            'wali_kontak' => ['nullable', 'string', 'max:20'],
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

        AdminLog::record('student.token', 'student', $student->id, "Menerbitkan token login NIS {$student->nis}");

        return redirect()->back()->with('status', "Token baru NIS {$student->nis}: {$plain} — catat/cetak SEKARANG, tidak ditampilkan lagi!");
    }

    public function revokeToken(Student $student)
    {
        $user = $student->user;

        if (! $user) {
            return redirect()->back()->with('status', 'Siswa belum memiliki akun login.');
        }

        LoginToken::revoke($user);

        AdminLog::record('student.token.revoke', 'student', $student->id, "Mencabut token login NIS {$student->nis}");

        return redirect()->back()->with('status', "Token login NIS {$student->nis} telah dicabut.");
    }

    public function kartu(Student $student)
    {
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

        $plainToken = ($user->login_token && $user->login_token_revoked_at === null)
            ? $user->login_token
            : LoginToken::issue($user);

        AdminLog::record('student.kartu', 'student', $student->id, "Mencetak kartu login NIS {$student->nis}");

        return view('admin.students.kartu', [
            'student' => $student,
            'plainToken' => $plainToken,
        ]);
    }
}

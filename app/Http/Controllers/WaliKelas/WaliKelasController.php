<?php

namespace App\Http\Controllers\WaliKelas;

use App\Enums\PeriodeStatus;
use App\Enums\Role;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Periode;
use App\Models\Registration;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WaliKelasController extends Controller
{
    private function resolveKelas(Request $request): array
    {
        $user = $request->user();

        // 1. Admin, Operator, atau Kepala Sekolah: dapat meninjau semua kelas dan berpindah kelas
        if ($user->isAdmin() || $user->hasRole(Role::OPERATOR, Role::KEPALA_SEKOLAH)) {
            $allClasses = SchoolClass::orderBy('tingkat')->orderBy('nama')->get();
            $kelas = null;

            if ($request->filled('kelas_id')) {
                $kelas = SchoolClass::find($request->query('kelas_id'));
            } elseif ($request->filled('kelas')) {
                $kelas = SchoolClass::where('nama', $request->query('kelas'))->first();
            }

            if (! $kelas) {
                $kelas = $user->managedClass ?? $allClasses->first();
            }

            return [$kelas, $allClasses];
        }

        // 2. Akun Wali Kelas: HANYA terkunci pada rombel kelas binaannya sendiri
        $kelas = $user->managedClass;
        if (! $kelas) {
            $kelas = SchoolClass::where('wali_kelas_id', $user->id)
                ->orWhere('wali_kelas_nama', $user->name)
                ->first();
        }

        // Jangan pernah mengambil kelas acak orang lain jika wali kelas belum ditugaskan
        return [$kelas, collect($kelas ? [$kelas] : [])];
    }

    private function periodeAktif(Request $request): ?Periode
    {
        $periodes = Periode::orderByDesc('periode')->get();
        $diminta = $request->query('periode');

        if ($diminta) {
            return $periodes->firstWhere('periode', $diminta) ?? $periodes->first();
        }

        return Periode::where('status', PeriodeStatus::DIBUKA)->orderByDesc('periode')->first()
            ?? $periodes->first();
    }

    public function dashboard(Request $request): View
    {
        [$kelas, $allClasses] = $this->resolveKelas($request);
        $periode = $this->periodeAktif($request);
        $periodes = Periode::orderByDesc('periode')->get();

        $students = collect();
        $registrations = collect();

        if ($kelas) {
            $students = Student::where('kelas', $kelas->nama)->orderBy('nama')->get();

            if ($periode) {
                $registrations = Registration::with(['user', 'student'])
                    ->where('kelas', $kelas->nama)
                    ->where('periode', $periode->periode)
                    ->where('status', Status::APPROVED)
                    ->get();
            }
        }

        $totalSiswa = $students->count();
        $totalIkutEskul = $registrations->pluck('nis')->unique()->count();
        $belumIkut = max(0, $totalSiswa - $totalIkutEskul);
        $sudahDinilai = $registrations->whereNotNull('nilai')->count();

        return view('wali-kelas.dashboard', compact(
            'kelas',
            'allClasses',
            'periode',
            'periodes',
            'totalSiswa',
            'totalIkutEskul',
            'belumIkut',
            'sudahDinilai',
            'registrations'
        ));
    }

    public function siswa(Request $request): View
    {
        [$kelas, $allClasses] = $this->resolveKelas($request);
        $periode = $this->periodeAktif($request);
        $periodes = Periode::orderByDesc('periode')->get();

        $students = collect();
        $regPeta = collect();

        if ($kelas) {
            $students = Student::where('kelas', $kelas->nama)
                ->orderBy('no_absen')
                ->orderBy('nama')
                ->get();

            if ($periode) {
                $regPeta = Registration::where('kelas', $kelas->nama)
                    ->where('periode', $periode->periode)
                    ->where('status', Status::APPROVED)
                    ->get()
                    ->keyBy('nis');
            }
        }

        return view('wali-kelas.siswa', compact(
            'kelas',
            'allClasses',
            'periode',
            'periodes',
            'students',
            'regPeta'
        ));
    }

    public function nilai(Request $request): View
    {
        [$kelas, $allClasses] = $this->resolveKelas($request);
        $periode = $this->periodeAktif($request);
        $periodes = Periode::orderByDesc('periode')->get();

        $dataNilai = collect();

        if ($kelas && $periode) {
            $dataNilai = Registration::with(['user', 'student'])
                ->where('kelas', $kelas->nama)
                ->where('periode', $periode->periode)
                ->where('status', Status::APPROVED)
                ->orderBy('nis')
                ->get();
        }

        return view('wali-kelas.nilai', compact(
            'kelas',
            'allClasses',
            'periode',
            'periodes',
            'dataNilai'
        ));
    }

    public function cetakNilai(Request $request): View
    {
        [$kelas, $allClasses] = $this->resolveKelas($request);
        $periode = $this->periodeAktif($request);

        $dataNilai = collect();

        if ($kelas && $periode) {
            $dataNilai = Registration::with(['user', 'student'])
                ->where('kelas', $kelas->nama)
                ->where('periode', $periode->periode)
                ->where('status', Status::APPROVED)
                ->orderBy('nis')
                ->get();
        }

        $sekolahNama = AppSetting::get('school.name', 'SMK NEGERI BAGIMU NEGERI');
        $sekolahAlamat = AppSetting::get('school.address', 'Jl. Pendidikan No. 1');
        $kepsekNama = AppSetting::get('school.kepsek', 'Kepala Sekolah');

        return view('wali-kelas.cetak-nilai', compact(
            'kelas',
            'periode',
            'dataNilai',
            'sekolahNama',
            'sekolahAlamat',
            'kepsekNama'
        ));
    }
}


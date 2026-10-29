<?php

namespace App\Http\Controllers\Pembina;

use App\Enums\KehadiranStatus;
use App\Enums\PeriodeStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Eskul;
use App\Models\Periode;
use App\Models\Registration;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnggotaController extends Controller
{
    private function getEskuls(int $pembinaId)
    {
        return Eskul::where('pembina_id', $pembinaId)
            ->orWhere(fn ($q) => $q->where('temp_pembina_id', $pembinaId)->whereDate('temp_pembina_sampai', '>=', today()))
            ->orderBy('name')
            ->get();
    }

    public function index(Request $request): View
    {
        $me = $request->user();
        $eskuls = $this->getEskuls($me->id);

        $allPeriodes = Periode::orderByDesc('periode')->get();
        $activePeriode = $allPeriodes->firstWhere('status', PeriodeStatus::DIBUKA);
        $selectedPeriode = $request->query('periode') ?: ($activePeriode?->periode ?: $allPeriodes->first()?->periode);

        $selectedEskulId = $request->query('eskul_id') ?: $eskuls->first()?->id;
        $currentEskul = $eskuls->firstWhere('id', (int) $selectedEskulId) ?: $eskuls->first();

        $query = Registration::with([
            'user',
            'student',
            'kehadiran' => fn ($q) => $currentEskul ? $q->where('eskul_id', $currentEskul->id) : $q,
        ]);

        if ($currentEskul) {
            $query->where('eskul_utama', $currentEskul->name);
        }

        if ($selectedPeriode) {
            $query->where('periode', $selectedPeriode);
        }

        $query->where('status', Status::APPROVED);

        // Filter Pencarian Nama / NIS
        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                    ->orWhere('no_absen', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('student', fn ($sq) => $sq->where('nama', 'like', "%{$search}%"));
            });
        }

        // Filter Kelas / Rombel
        if ($kelas = $request->query('kelas')) {
            $query->where('kelas', $kelas);
        }

        $anggotaList = $query->orderBy('kelas')->orderBy('nis')->paginate(25)->withQueryString();

        // Daftar kelas yang ada di anggota eskul ini untuk dropdown filter
        $daftarKelas = Registration::where('eskul_utama', $currentEskul?->name)
            ->where('periode', $selectedPeriode)
            ->where('status', Status::APPROVED)
            ->pluck('kelas')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        // Statistik agregat anggota
        $totalAnggota = Registration::where('eskul_utama', $currentEskul?->name)
            ->where('periode', $selectedPeriode)
            ->where('status', Status::APPROVED)
            ->count();

        $sudahDinilai = Registration::where('eskul_utama', $currentEskul?->name)
            ->where('periode', $selectedPeriode)
            ->where('status', Status::APPROVED)
            ->whereNotNull('nilai')
            ->count();

        return view('pembina.anggota.index', compact(
            'eskuls',
            'currentEskul',
            'allPeriodes',
            'selectedPeriode',
            'anggotaList',
            'daftarKelas',
            'totalAnggota',
            'sudahDinilai'
        ));
    }

    public function export(Request $request)
    {
        $me = $request->user();
        $eskuls = $this->getEskuls($me->id);

        $selectedEskulId = $request->query('eskul_id') ?: $eskuls->first()?->id;
        $currentEskul = $eskuls->firstWhere('id', (int) $selectedEskulId) ?: $eskuls->first();

        if (! $currentEskul) {
            return back()->with('error', 'Eskul tidak ditemukan.');
        }

        $allPeriodes = Periode::orderByDesc('periode')->get();
        $activePeriode = $allPeriodes->firstWhere('status', PeriodeStatus::DIBUKA);
        $selectedPeriode = $request->query('periode') ?: ($activePeriode?->periode ?: $allPeriodes->first()?->periode);

        $anggota = Registration::with([
            'user',
            'student',
            'kehadiran' => fn ($q) => $q->where('eskul_id', $currentEskul->id),
        ])
            ->where('eskul_utama', $currentEskul->name)
            ->where('periode', $selectedPeriode)
            ->where('status', Status::APPROVED)
            ->orderBy('kelas')
            ->orderBy('nis')
            ->get();

        $filename = 'Direktori_Anggota_' . str_replace(' ', '_', $currentEskul->name) . '_' . str_replace(['/', '\\'], '-', $selectedPeriode) . '.xls';

        $output = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        $output .= '<head><meta charset="utf-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Direktori Anggota</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>';
        $output .= '<body>';
        $output .= '<h3>DIREKTORI ANGGOTA EKSTRAKURIKULER ' . strtoupper(e($currentEskul->name)) . '</h3>';
        $output .= '<p><strong>Tahun Ajaran:</strong> ' . e($selectedPeriode) . ' | <strong>Pembina:</strong> ' . e($me->name) . '</p>';
        $output .= '<table border="1" cellpadding="5" cellspacing="0">';
        $output .= '<tr style="background-color: #1e3a8a; color: #ffffff; font-weight: bold;">';
        $output .= '<th>No</th><th>NIS</th><th>Nama Lengkap</th><th>L/P</th><th>Kelas</th><th>WhatsApp</th><th>Hadir</th><th>Tidak Hadir (Izin/Sakit/Alpa)</th><th>Total Pertemuan</th><th>Nilai Angka</th><th>Predikat</th><th>Catatan Rapor</th>';
        $output .= '</tr>';

        $no = 1;
        foreach ($anggota as $reg) {
            $student = $reg->student;
            $hadir = $reg->kehadiran->where('status', KehadiranStatus::HADIR)->count();
            $izin = $reg->kehadiran->where('status', KehadiranStatus::IZIN)->count();
            $sakit = $reg->kehadiran->where('status', KehadiranStatus::SAKIT)->count();
            $alpa = $reg->kehadiran->where('status', KehadiranStatus::ALPA)->count();
            $tidakHadir = $izin + $sakit + $alpa;
            $total = $reg->kehadiran->count();

            $output .= '<tr>';
            $output .= '<td align="center">' . $no++ . '</td>';
            $output .= '<td style="mso-number-format:\'@\';">' . e($reg->nis) . '</td>';
            $output .= '<td>' . e($student?->nama ?: ($reg->user?->name ?: '-')) . '</td>';
            $output .= '<td align="center">' . e($reg->gender?->value ?: ($student?->gender?->value ?: '-')) . '</td>';
            $output .= '<td>' . e($reg->kelas ?: ($student?->kelas ?: '-')) . '</td>';
            $output .= '<td style="mso-number-format:\'@\';">' . e($reg->whatsapp ?: ($student?->whatsapp ?: '-')) . '</td>';
            $output .= '<td align="center">' . $hadir . '</td>';
            $output .= '<td align="center">' . $tidakHadir . ' (' . $izin . 'I/' . $sakit . 'S/' . $alpa . 'A)</td>';
            $output .= '<td align="center">' . $total . '</td>';
            $output .= '<td align="center"><strong>' . ($reg->nilai !== null ? e($reg->nilai) : '-') . '</strong></td>';
            $output .= '<td align="center"><strong>' . e($reg->predikat ?: '-') . '</strong></td>';
            $output .= '<td>' . e($reg->catatan_nilai ?: '-') . '</td>';
            $output .= '</tr>';
        }

        $output .= '</table>';
        $output .= '</body></html>';

        return response($output, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }
}

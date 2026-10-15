<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Certificate;
use App\Models\Registration;
use App\Support\Notifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $query = Registration::with(['user', 'certificate']);

        if ($request->filled('periode')) {
            $query->where('periode', $request->query('periode'));
        }

        if ($request->filled('q')) {
            $q = trim($request->query('q'));
            $query->where(function ($w) use ($q) {
                $w->where('nis', 'like', "%{$q}%")
                    ->orWhere('eskul_utama', 'like', "%{$q}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('certificate', fn ($c) => $c->where('nomor', 'like', "%{$q}%"));
            });
        }

        $pendaftar = $query->where('status', Status::APPROVED)
            ->latest()->paginate(20)->withQueryString();

        $periodes = Registration::select('periode')->distinct()->orderByDesc('periode')->pluck('periode');

        return view('admin.certificates.index', [
            'pendaftar' => $pendaftar,
            'periodes' => $periodes,
            'filterPeriode' => $request->query('periode', ''),
            'filterQ' => $request->query('q', ''),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'registration_id' => ['required', 'exists:registrations,id'],
        ]);

        $reg = Registration::with('certificate')->findOrFail($data['registration_id']);

        if ($reg->status !== Status::APPROVED) {
            return back()->withErrors(['registration_id' => 'Hanya pendaftar yang disetujui yang dapat diterbitkan sertifikat.']);
        }

        if ($reg->certificate) {
            return back()->with('status', "Sudah memiliki sertifikat {$reg->certificate->nomor}.");
        }

        try {
            $cert = DB::transaction(function () use ($reg) {
                $reg->lockForUpdate()->first();
                if ($reg->fresh()->certificate) {
                    return $reg->fresh()->certificate;
                }
                $nomor = self::nomorBaru();

                if (empty($reg->verify_token)) {
                    $reg->verify_token = bin2hex(random_bytes(32));
                    $reg->save();
                }

                return Certificate::create([
                    'registration_id' => $reg->id,
                    'nomor' => $nomor,
                    'doc_version' => 1,
                    'diterbitkan_oleh' => auth()->user()->email,
                ]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            $reg->refresh();
            if ($reg->certificate) {
                return back()->with('status', "Sudah memiliki sertifikat {$reg->certificate->nomor}.");
            }
            throw $e;
        }

        AdminLog::record('certificates.store', 'Certificate', (string) $cert->id, "Terbit sertifikat {$cert->nomor} untuk registrasi #{$reg->id}.");

        Notifier::sertifikatTerbit($reg, $cert->nomor);

        return back()->with('status', "Sertifikat {$cert->nomor} berhasil diterbitkan.");
    }

    public function cetak(Certificate $certificate)
    {
        $certificate->load('registration.user');

        return view('admin.certificates.cetak', [
            'certificate' => $certificate,
            'registration' => $certificate->registration,
        ]);
    }

    /**
     * Nomor sertifikat SRT-{tahun}-{6 alfanumerik} (tanpa I,O,0,1).
     */
    public static function nomorBaru(): string
    {
        $alfabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        for ($i = 0; $i < 8; $i++) {
            $kode = '';
            for ($j = 0; $j < 6; $j++) {
                $kode .= $alfabet[random_int(0, strlen($alfabet) - 1)];
            }

            $nomor = 'SRT-'.date('Y').'-'.$kode;

            if (! Certificate::where('nomor', $nomor)->exists()) {
                return $nomor;
            }
        }

        return 'SRT-'.date('Y').'-'.strtoupper(Str::random(6));
    }
}

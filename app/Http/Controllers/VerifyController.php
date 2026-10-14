<?php

namespace App\Http\Controllers;

use App\Models\Eskul;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerifyController extends Controller
{
    /**
     * Form verifikasi manual (ketik kode ESK-… atau tempel token).
     */
    public function form(Request $request): View
    {
        $kode = trim($request->query('kode', ''));
        $registration = null;
        $error = null;

        if ($kode !== '') {
            $registration = $this->cari($kode);

            if (! $registration) {
                $error = 'Kode tidak dikenal. Periksa kembali kode pada E-Card siswa.';
            }
        }

        return view('verify.show', [
            'registration' => $registration,
            'jadwals' => $registration ? $this->jadwals($registration) : collect(),
            'error' => $error,
            'kode' => $kode,
        ]);
    }

    /**
     * Verifikasi via QR (token opaque 64 hex).
     */
    public function show(string $token): View
    {
        $registration = $this->cari($token);

        if (! $registration) {
            abort(404);
        }

        return view('verify.show', [
            'registration' => $registration,
            'jadwals' => $this->jadwals($registration),
            'error' => null,
            'kode' => '',
        ]);
    }

    private function cari(string $kode): ?Registration
    {
        $kode = trim($kode);

        if (preg_match('/^[0-9a-fA-F]{32,64}$/', $kode)) {
            return Registration::with(['user', 'certificate'])
                ->where('verify_token', strtolower($kode))
                ->first();
        }

        return Registration::dariKode($kode)?->load(['user', 'certificate']);
    }

    private function jadwals(Registration $registration)
    {
        return Eskul::where('name', $registration->eskul_utama)->first()?->jadwals
            ?? collect();
    }
}

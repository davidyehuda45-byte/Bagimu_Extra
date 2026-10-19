<?php

namespace App\Http\Controllers\Siswa;

use App\Enums\PeriodeStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Mail\BuktiPendaftaranMail;
use App\Models\AppSetting;
use App\Models\Eskul;
use App\Models\Periode;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class StatusController extends Controller
{
    public function index(): View
    {
        $user = auth()->user()->load('student');

        $periodeAktif = Periode::where('status', PeriodeStatus::DIBUKA)
            ->orderBy('periode', 'desc')
            ->first();

        $registrations = $user->registrations()->with(['certificate'])->latest()->get();

        $aktif = $periodeAktif
            ? $registrations->firstWhere('periode', $periodeAktif->periode)
            : $registrations->first();

        $jadwals = collect();
        if ($aktif) {
            if (empty($aktif->verify_token)) {
                $aktif->verify_token = bin2hex(random_bytes(32));
                $aktif->save();
                $aktif->refresh();
            }

            $jadwals = Eskul::where('name', $aktif->eskul_utama)->first()?->jadwals ?? collect();
        }

        $kontakPembina = AppSetting::get('school.contact', '');

        return view('siswa.status', compact(
            'user',
            'periodeAktif',
            'registrations',
            'aktif',
            'jadwals',
            'kontakPembina'
        ));
    }

    public function kirimEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $user = auth()->user()->load('student');

        $periodeAktif = Periode::where('status', PeriodeStatus::DIBUKA)
            ->orderBy('periode', 'desc')
            ->first();

        $registrations = $user->registrations()->latest()->get();

        $aktif = $periodeAktif
            ? $registrations->firstWhere('periode', $periodeAktif->periode)
            : $registrations->first();

        if (! $aktif) {
            return back()->with('error', 'Belum ada data pendaftaran yang aktif.');
        }

        if (empty($aktif->verify_token)) {
            $aktif->verify_token = bin2hex(random_bytes(32));
            $aktif->save();
            $aktif->refresh();
        }

        try {
            Mail::to($request->email)->send(new BuktiPendaftaranMail($aktif));

            return back()->with('status', 'Bukti pendaftaran berhasil dikirim ke '.$request->email);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal mengirim email: '.$e->getMessage());
        }
    }
}

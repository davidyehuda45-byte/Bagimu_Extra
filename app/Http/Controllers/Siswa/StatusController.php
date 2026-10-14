<?php

namespace App\Http\Controllers\Siswa;

use App\Enums\PeriodeStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Eskul;
use App\Models\Periode;
use App\Models\Registration;
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

        $kontakPembina = AppSetting::get('school.contact', '6281234567890');

        return view('siswa.status', compact(
            'user',
            'periodeAktif',
            'registrations',
            'aktif',
            'jadwals',
            'kontakPembina'
        ));
    }
}

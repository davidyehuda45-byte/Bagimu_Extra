<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PeriodeStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Periode;
use App\Models\Registration;
use Illuminate\Http\Request;

class CetakController extends Controller
{
    public function index(Request $request)
    {
        $periodes = Registration::where('status', Status::APPROVED)
            ->select('periode')->distinct()->orderByDesc('periode')->pluck('periode');

        $aktif = Periode::where('status', PeriodeStatus::DIBUKA)->orderByDesc('periode')->first();
        $periode = $request->query('periode', $aktif?->periode ?? $periodes->first());

        $peserta = Registration::with('user')
            ->where('status', Status::APPROVED)
            ->when($periode, fn ($q) => $q->where('periode', $periode))
            ->join('users', 'users.id', '=', 'registrations.user_id')
            ->orderBy('eskul_utama')->orderBy('users.name')
            ->select('registrations.*')
            ->get();

        return view('admin.cetak.index', compact('periodes', 'periode', 'peserta'));
    }
}

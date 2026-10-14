<?php

namespace App\Http\Controllers\Siswa;

use App\Enums\IzinJenis;
use App\Enums\IzinStatus;
use App\Enums\PeriodeStatus;
use App\Http\Controllers\Controller;
use App\Models\Eskul;
use App\Models\Izin;
use App\Models\Periode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class IzinController extends Controller
{
    public function index(): View
    {
        $regIds = auth()->user()->registrations()->pluck('id');

        $pengajuan = Izin::whereIn('registration_id', $regIds)
            ->with(['eskul', 'registration'])
            ->latest()
            ->paginate(15);

        return view('siswa.izin.index', compact('pengajuan'));
    }

    public function create(): View|RedirectResponse
    {
        $user = auth()->user();

        $periode = Periode::where('status', PeriodeStatus::DIBUKA)
            ->orderBy('periode', 'desc')
            ->first();

        $registration = $periode
            ? $user->registrations()->where('periode', $periode->periode)->first()
            : null;

        if (! $registration) {
            return redirect()->route('siswa.daftar')
                ->with('status', 'Silakan daftar ekstrakurikuler terlebih dahulu sebelum mengajukan izin.');
        }

        $eskulDiikuti = collect([$registration->eskul_utama])
            ->filter()
            ->values();

        return view('siswa.izin.create', [
            'registration' => $registration,
            'eskulDiikuti' => $eskulDiikuti,
            'jenis' => IzinJenis::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $periode = Periode::where('status', PeriodeStatus::DIBUKA)
            ->orderBy('periode', 'desc')
            ->first();

        $registration = $periode
            ? $user->registrations()->where('periode', $periode->periode)->first()
            : null;

        if (! $registration) {
            return redirect()->route('siswa.daftar')
                ->with('status', 'Silakan daftar ekstrakurikuler terlebih dahulu sebelum mengajukan izin.');
        }

        $validated = $request->validate([
            'eskul' => ['required', 'string', Rule::in([$registration->eskul_utama])],
            'tanggal' => ['required', 'date'],
            'jenis' => ['required', Rule::enum(IzinJenis::class)],
            'alasan' => ['required', 'string', 'min:3', 'max:500'],
            'bukti' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'eskul.in' => 'Eskul yang dipilih harus eskul yang sedang Anda ikuti.',
        ]);

        $eskul = Eskul::where('name', $validated['eskul'])->firstOrFail();

        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('bukti_izin', 'public');
        }

        Izin::create([
            'registration_id' => $registration->id,
            'eskul_id' => $eskul->id,
            'tanggal' => $validated['tanggal'],
            'jenis' => $validated['jenis'],
            'alasan' => $validated['alasan'],
            'bukti' => $buktiPath,
            'status' => IzinStatus::PENDING,
        ]);

        return redirect()->route('siswa.izin.index')
            ->with('status', 'Pengajuan izin berhasil dikirim dan menunggu persetujuan.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PeriodeStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Eskul;
use App\Models\Periode;
use App\Models\Registration;

class RekapController extends Controller
{
    private function periodeBerjalan(): ?string
    {
        return Periode::where('status', PeriodeStatus::DIBUKA)->orderByDesc('periode')->value('periode');
    }

    public function index()
    {
        $periode = $this->periodeBerjalan();

        $rekap = Eskul::orderBy('name')->get()->map(function ($e) use ($periode) {
            $base = Registration::where('eskul_utama', $e->name)
                ->when($periode, fn ($q) => $q->where('periode', $periode));

            return [
                'eskul' => $e,
                'count' => (clone $base)->count(),
                'pending' => (clone $base)->where('status', Status::PENDING)->count(),
                'approved' => (clone $base)->where('status', Status::APPROVED)->count(),
                'rejected' => (clone $base)->where('status', Status::REJECTED)->count(),
            ];
        })->sortByDesc('count')->values();

        return view('admin.rekap.index', compact('rekap', 'periode'));
    }

    public function show(string $eskul)
    {
        $model = Eskul::where('name', urldecode($eskul))->firstOrFail();
        $periode = $this->periodeBerjalan();

        $siswa = Registration::with('user')
            ->join('users', 'users.id', '=', 'registrations.user_id')
            ->select('registrations.*')
            ->where('eskul_utama', $model->name)
            ->when($periode, fn ($q) => $q->where('periode', $periode))
            ->orderBy('registrations.kelas')->orderBy('users.name')
            ->paginate(20);

        return view('admin.rekap.show', compact('model', 'siswa', 'periode'));
    }
}

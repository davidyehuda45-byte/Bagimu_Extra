<?php

namespace App\Http\Controllers\Admin;

use App\Enums\NotifStatus;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\NotificationLog;
use App\Models\User;
use App\Support\Notifier;
use Illuminate\Http\Request;

class NotifController extends Controller
{
    private const TEMPLATES = [
        'PENDAFTARAN_MASUK',
        'SERTIFIKAT_TERBIT',
        'PENGUMUMAN_BARU',
        'JADWAL_BERUBAH',
        'KEGIATAN_MENDATANG',
    ];

    public function index(Request $request)
    {
        $status = $request->query('status', '');

        $logs = NotificationLog::latest()
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->paginate(20)
            ->withQueryString();

        $counts = NotificationLog::selectRaw('status, COUNT(*) as c')
            ->groupBy('status')->pluck('c', 'status')->all();

        return view('admin.notifikasi.index', [
            'templates' => self::TEMPLATES,
            'logs' => $logs,
            'status' => $status,
            'counts' => $counts,
            'statusList' => NotifStatus::cases(),
        ]);
    }

    public function retry(NotificationLog $log)
    {
        if ($log->status === NotifStatus::TERKIRIM) {
            return back()->with('status', 'Notifikasi ini sudah terkirim.');
        }

        if (preg_match('/^user:(\d+)$/', $log->to, $m) && ($user = User::find($m[1]))) {
            Notifier::kePengguna($user->id, 'Pemberitahuan', $log->template);
        }

        $log->update([
            'status' => NotifStatus::TERKIRIM,
            'attempts' => $log->attempts + 1,
            'sent_at' => now(),
        ]);

        AdminLog::record('notif.retry', 'NotificationLog', $log->id, "Kirim ulang notifikasi {$log->template}.");

        return back()->with('status', 'Notifikasi dikirim ulang.');
    }
}

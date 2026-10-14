<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $aksi = $request->query('aksi', '');

        if ($aksi !== '' && ! preg_match('/^[A-Za-z_.]+$/', $aksi)) {
            $aksi = '';
        }

        $q = trim((string) $request->query('q', ''));

        $logs = AdminLog::latest()
            ->when($aksi !== '', fn ($query) => $query->where('action', $aksi))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('detail', 'like', "%{$q}%")
                        ->orWhere('admin_email', 'like', "%{$q}%")
                        ->orWhere('target_id', 'like', "%{$q}%");
                });
            })
            ->paginate(20)
            ->withQueryString();

        $chipActions = AdminLog::select('action')->distinct()->orderBy('action')->pluck('action');

        return view('admin.logs.index', [
            'logs' => $logs,
            'aksi' => $aksi,
            'q' => $q,
            'chipActions' => $chipActions,
        ]);
    }
}

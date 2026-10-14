<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\InAppNotification;
use Illuminate\Http\RedirectResponse;

class NotifikasiController extends Controller
{
    public function read(InAppNotification $notifikasi): RedirectResponse
    {
        abort_unless($notifikasi->user_id === auth()->id(), 403);

        $notifikasi->markAsRead();

        return back();
    }
}

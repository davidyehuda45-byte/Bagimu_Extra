<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Student;
use App\Models\User;
use App\Services\FonnteService;
use App\Support\LoginToken;
use App\Support\Whatsapp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WaBlastController extends Controller
{
    public function index(Request $request, FonnteService $fonnte): View
    {
        $fonnteStatus = $fonnte->getStatus();

        $totalSiswa = Student::where('status', 'AKTIF')->count();
        $totalDenganWa = Student::where('status', 'AKTIF')
            ->where(function ($q) {
                $q->whereNotNull('whatsapp')->where('whatsapp', '!=', '');
            })->count();
        $totalTanpaWa = $totalSiswa - $totalDenganWa;

        $totalBelumToken = Student::where('status', 'AKTIF')
            ->where(function ($q) {
                $q->whereDoesntHave('user')
                  ->orWhereHas('user', function ($u) {
                      $u->whereNull('login_token')->orWhereNotNull('login_token_revoked_at');
                  });
            })->count();

        $kelasList = Student::whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->distinct()
            ->pluck('kelas')
            ->sort(SORT_NATURAL)
            ->values();

        // Sample target siswa untuk preview
        $query = Student::with(['user', 'user.registrations'])
            ->where('status', 'AKTIF');

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->input('kelas'));
        }
        if ($request->filled('tingkat')) {
            $t = $request->input('tingkat');
            $query->where(function ($w) use ($t) {
                $w->where('kelas', 'like', "{$t} %")
                  ->orWhere('kelas', 'like', "{$t}-%")
                  ->orWhere('kelas', '=', $t);
            });
        }
        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                  ->orWhere('nis', 'like', "%{$q}%")
                  ->orWhere('whatsapp', 'like', "%{$q}%");
            });
        }

        $students = $query->orderBy('kelas')->orderBy('nama')->paginate(15)->withQueryString();

        return view('admin.wa-blast.index', compact(
            'fonnteStatus',
            'totalSiswa',
            'totalDenganWa',
            'totalTanpaWa',
            'totalBelumToken',
            'kelasList',
            'students'
        ));
    }

    public function checkToken(Request $request, FonnteService $fonnte): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $result = $fonnte->validateToken($request->token);

        return response()->json($result);
    }

    public function saveToken(Request $request, FonnteService $fonnte): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $result = $fonnte->validateToken($request->token);

        if (! ($result['valid'] ?? false)) {
            return back()->with('error', $result['message'] ?? 'Token Fonnte tidak valid.');
        }

        $fonnte->saveToken($request->token, $result);

        AdminLog::record('settings.fonnte_connect', 'AppSetting', null, "Menghubungkan WhatsApp Gateway Fonnte (Device: {$result['device']})");

        return back()->with('status', 'WhatsApp Gateway (Fonnte) berhasil dihubungkan.');
    }

    public function disconnectToken(FonnteService $fonnte): RedirectResponse
    {
        $fonnte->disconnect();

        AdminLog::record('settings.fonnte_disconnect', 'AppSetting', null, 'Memutus WhatsApp Gateway Fonnte');

        return back()->with('status', 'Koneksi WhatsApp Gateway Fonnte berhasil diputus.');
    }

    public function send(Request $request, FonnteService $fonnte)
    {
        $status = $fonnte->getStatus();
        if (! $status['connected']) {
            $msg = 'WhatsApp Gateway (Fonnte) belum terhubung. Hubungkan API Key Fonnte terlebih dahulu.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return back()->with('error', $msg);
        }

        $request->validate([
            'mode' => ['required', 'string', 'in:all,kelas,untokenized,selected'],
            'pesan' => ['required', 'string', 'max:2000'],
            'kelas' => ['nullable', 'string'],
            'ids' => ['nullable', 'array'],
        ]);

        $mode = $request->input('mode', 'all');
        $pesanTemplate = $request->input('pesan');

        $query = Student::with(['user', 'user.registrations'])->where('status', 'AKTIF');

        if ($mode === 'selected') {
            $selectedIds = (array) $request->input('ids', []);
            if (empty($selectedIds)) {
                return back()->with('error', 'Pilih minimal satu data siswa.');
            }
            $query->whereIn('id', $selectedIds);
        } elseif ($mode === 'kelas' && $request->filled('kelas')) {
            $query->where('kelas', $request->input('kelas'));
        } elseif ($mode === 'untokenized') {
            $query->where(function ($q) {
                $q->whereDoesntHave('user')
                  ->orWhereHas('user', function ($u) {
                      $u->whereNull('login_token')->orWhereNotNull('login_token_revoked_at');
                  });
            });
        }

        $students = $query->get();

        // Hanya siswa dengan nomor WA
        $targets = $students->filter(function ($s) {
            $reg = $s->user?->registrations?->first();
            $wa = $s->whatsapp ?: ($s->wali_kontak ?: ($reg?->whatsapp ?? ''));

            return ! empty(trim((string) $wa));
        });

        if ($targets->isEmpty()) {
            return back()->with('warning', 'Tidak ada siswa dengan nomor WhatsApp valid pada target yang dipilih.');
        }

        @set_time_limit(300);

        $successCount = 0;
        $failCount = 0;
        $appName = config('app.name', 'Bagimu Extra');
        $loginUrl = route('siswa.login');

        foreach ($targets as $student) {
            $reg = $student->user?->registrations?->first();
            $rawWa = $student->whatsapp ?: ($student->wali_kontak ?: ($reg?->whatsapp ?? ''));
            $normWa = Whatsapp::normalize($rawWa);

            if (! $normWa) {
                $failCount++;
                continue;
            }

            $user = $student->user;
            if (! $user) {
                $user = User::create([
                    'name' => $student->nama,
                    'email' => $student->nis.'@siswa.local',
                    'username' => $student->nis,
                    'password' => bcrypt(Str::random(16)),
                    'role' => Role::STUDENT,
                ]);
                $student->update(['user_id' => $user->id]);
            }

            $token = $user->login_token;
            if (! $token || $user->login_token_revoked_at) {
                $token = LoginToken::issue($user);
            }

            $directLoginUrl = route('siswa.direct-login', [
                'nis' => $student->nis,
                'token' => $token,
            ]);

            $replacements = [
                '{nama}' => $student->nama,
                '{nis}' => $student->nis,
                '{nisn}' => $student->nisn ?? '-',
                '{kelas}' => $student->kelas,
                '{token}' => $token,
                '{link}' => $directLoginUrl,
                '{login_url}' => $loginUrl,
                '{aplikasi}' => $appName,
                '{eskul}' => $reg?->eskul_utama ?? 'Belum memilih',
            ];

            $renderedMessage = str_replace(array_keys($replacements), array_values($replacements), $pesanTemplate);

            $sendResult = $fonnte->sendMessage($normWa, $renderedMessage);

            if ($sendResult['success']) {
                $successCount++;
            } else {
                $failCount++;
            }

            usleep(30000); // 30ms
        }

        AdminLog::record('wa_blast.send', 'Student', null, "WA Blast: {$successCount} sukses, {$failCount} gagal (Total target: {$targets->count()})");

        $statusMsg = "WA Blast selesai: {$successCount} pesan berhasil dikirim".($failCount > 0 ? ", {$failCount} gagal." : '.');

        return redirect()->route('admin.wa-blast.index')->with('status', $statusMsg);
    }
}


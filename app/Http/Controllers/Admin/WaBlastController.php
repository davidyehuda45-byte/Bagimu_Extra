<?php

namespace App\Http\Controllers\Admin;

use App\Enums\NotifChannel;
use App\Enums\NotifStatus;
use App\Enums\PeriodeStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\NotificationLog;
use App\Models\Periode;
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

        $currentPeriode = Periode::where('status', PeriodeStatus::DIBUKA->value)->latest('periode')->value('periode')
            ?? Periode::latest('periode')->value('periode') ?? '2026/2027';

        // Siswa yang sudah dikirimi WA blast pada periode berjalan
        $blastedNisList = NotificationLog::where('channel', NotifChannel::WA)
            ->where('dedupe_key', 'like', "wa_blast:{$currentPeriode}:%")
            ->where('status', NotifStatus::TERKIRIM)
            ->pluck('dedupe_key')
            ->map(fn ($k) => str_replace("wa_blast:{$currentPeriode}:", '', $k))
            ->flip()
            ->all();

        $totalSudahBlasted = count($blastedNisList);

        $totalBelumBlasted = Student::where('status', 'AKTIF')
            ->where(function ($q) {
                $q->whereNotNull('whatsapp')->where('whatsapp', '!=', '');
            })
            ->whereNotIn('nis', array_keys($blastedNisList))
            ->count();

        $totalBelumDaftar = Student::where('status', 'AKTIF')
            ->whereDoesntHave('registrations', function ($q) use ($currentPeriode) {
                $q->where('periode', $currentPeriode);
            })
            ->where(function ($q) {
                $q->whereNotNull('whatsapp')->where('whatsapp', '!=', '');
            })
            ->whereNotIn('nis', array_keys($blastedNisList))
            ->count();

        // Sample target siswa untuk preview
        $query = Student::with(['user', 'registrations'])
            ->where('status', 'AKTIF');

        if ($request->filled('filter_blast')) {
            $f = $request->input('filter_blast');
            if ($f === 'belum_kirim') {
                $query->whereNotIn('nis', array_keys($blastedNisList));
            } elseif ($f === 'sudah_kirim') {
                $query->whereIn('nis', array_keys($blastedNisList));
            }
        }

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
            'students',
            'currentPeriode',
            'totalSudahBlasted',
            'totalBelumBlasted',
            'totalBelumDaftar',
            'blastedNisList'
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
            'mode' => ['required', 'string', 'in:all,kelas,untokenized,unregistered,unblasted,selected'],
            'pesan' => ['required', 'string', 'max:2000'],
            'kelas' => ['nullable', 'string'],
            'ids' => ['nullable', 'array'],
            'batch_limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'skip_already_blasted' => ['nullable', 'boolean'],
        ]);

        $currentPeriode = Periode::where('status', PeriodeStatus::DIBUKA->value)->latest('periode')->value('periode')
            ?? Periode::latest('periode')->value('periode') ?? '2026/2027';

        $mode = $request->input('mode', 'unblasted');
        $pesanTemplate = $request->input('pesan');
        $skipAlreadyBlasted = $request->boolean('skip_already_blasted', true);
        $batchLimit = $request->filled('batch_limit') ? (int) $request->input('batch_limit') : 25;

        // Ambil daftar NIS yang sudah pernah dikirim blast di periode ini
        $blastedNis = NotificationLog::where('channel', NotifChannel::WA)
            ->where('dedupe_key', 'like', "wa_blast:{$currentPeriode}:%")
            ->where('status', NotifStatus::TERKIRIM)
            ->pluck('dedupe_key')
            ->map(fn ($k) => str_replace("wa_blast:{$currentPeriode}:", '', $k))
            ->all();

        $query = Student::with(['user', 'registrations'])->where('status', 'AKTIF');

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
        } elseif ($mode === 'unregistered') {
            // Siswa belum memilih eskul di periode ini
            $query->whereDoesntHave('registrations', function ($q) use ($currentPeriode) {
                $q->where('periode', $currentPeriode);
            });
        }

        // Terapkan perlindungan 1x kirim per periode jika skip_already_blasted aktif atau mode unblasted
        if ($skipAlreadyBlasted || $mode === 'unblasted') {
            if (! empty($blastedNis)) {
                $query->whereNotIn('nis', $blastedNis);
            }
        }

        $allMatching = $query->get();

        // Hanya siswa dengan nomor WA valid
        $targets = $allMatching->filter(function ($s) {
            $reg = $s->registrations?->first();
            $wa = $s->whatsapp ?: ($s->wali_kontak ?: ($reg?->whatsapp ?? ''));

            return ! empty(trim((string) $wa));
        });

        $totalEligible = $targets->count();

        if ($totalEligible === 0) {
            return back()->with('warning', 'Semua siswa pada target yang dipilih sudah pernah menerima WhatsApp Blast di periode ' . $currentPeriode . ' atau tidak memiliki nomor WhatsApp.');
        }

        // Potong sesuai batch limit gelombang aman
        if ($batchLimit > 0 && $targets->count() > $batchLimit) {
            $targets = $targets->take($batchLimit);
        }

        @set_time_limit(300);

        $successCount = 0;
        $failCount = 0;
        $appName = config('app.name', 'Bagimu Extra');
        $loginUrl = route('siswa.login');

        foreach ($targets as $student) {
            $reg = $student->registrations?->first();
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

            $replacements = [
                '{nama}' => $student->nama,
                '{nis}' => $student->nis,
                '{kelas}' => $student->kelas,
                '{token}' => $token,
                '{link}' => $loginUrl,
                '{login_url}' => $loginUrl,
                '{aplikasi}' => $appName,
                '{sekolah}' => \App\Models\AppSetting::get('school.name', $appName),
                '{eskul}' => $reg?->eskul_utama ?? 'Belum memilih',
            ];

            $renderedMessage = str_replace(array_keys($replacements), array_values($replacements), $pesanTemplate);

            $sendResult = $fonnte->sendMessage($normWa, $renderedMessage);

            $dedupeKey = "wa_blast:{$currentPeriode}:{$student->nis}";

            NotificationLog::create([
                'channel' => NotifChannel::WA,
                'to' => $normWa,
                'template' => 'wa_blast',
                'payload' => [
                    'student_id' => $student->id,
                    'nis' => $student->nis,
                    'nama' => $student->nama,
                    'periode' => $currentPeriode,
                    'mode' => $mode,
                ],
                'status' => $sendResult['success'] ? NotifStatus::TERKIRIM : NotifStatus::GAGAL,
                'attempts' => 1,
                'provider_resp' => $sendResult['raw'] ?? ($sendResult['message'] ?? null),
                'dedupe_key' => $dedupeKey,
                'sent_at' => now(),
            ]);

            if ($sendResult['success']) {
                $successCount++;
            } else {
                $failCount++;
            }

            // Jeda aman anti-banned: 1.5 detik per pesan
            usleep(1500000);
        }

        AdminLog::record('wa_blast.send', 'Student', null, "WA Blast gelombang: {$successCount} sukses, {$failCount} gagal (Gelombang ini: {$targets->count()}, Periode: {$currentPeriode})");

        $sisaAntrean = $totalEligible - $targets->count();
        $statusMsg = "Gelombang pengiriman selesai: {$successCount} pesan berhasil dikirim (dengan jeda proteksi 1,5s)";
        if ($failCount > 0) {
            $statusMsg .= ", {$failCount} gagal";
        }
        if ($sisaAntrean > 0) {
            $statusMsg .= ". Masih ada {$sisaAntrean} siswa yang belum dikirimi di target ini (silakan klik kirim gelombang berikutnya).";
        } else {
            $statusMsg .= ". Semua siswa pada target ini telah berhasil dikirimi.";
        }

        return redirect()->route('admin.wa-blast.index')->with('status', $statusMsg);
    }
}


<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use PragmaRX\Google2FA\Google2FA;

class SettingController extends Controller
{
    private const IDENTITAS = [
        'school.name' => 'Nama Sekolah',
        'school.contact' => 'Kontak',
        'school.address' => 'Alamat',
        'school.logo' => 'Path Logo',
    ];

    public function index()
    {
        $settings = [];
        foreach (self::IDENTITAS as $key => $label) {
            $settings[$key] = AppSetting::get($key, '');
        }

        $me = auth()->user();
        $qrUrl = null;
        if ($me->totp_secret_enc && ! $me->totp_enabled) {
            $qrUrl = (new Google2FA)->getQRCodeUrl(
                config('app.name', 'Bagimu Extra'),
                $me->email,
                Crypt::decryptString($me->totp_secret_enc)
            );
        }

        $users = \App\Models\User::whereIn('role', [
            \App\Enums\Role::ADMIN,
            \App\Enums\Role::OPERATOR,
            \App\Enums\Role::PEMBINA,
            \App\Enums\Role::KEPALA_SEKOLAH,
        ])->orderBy('name')->paginate(20);

        return view('admin.settings.index', [
            'settings' => $settings,
            'labels' => self::IDENTITAS,
            'maintenance' => AppSetting::get('app.maintenance', '0') === '1',
            'maintenanceMsg' => AppSetting::get('app.maintenance_msg', 'Sistem sedang dalam pemeliharaan.'),
            'maintenanceEta' => AppSetting::get('app.maintenance_eta', ''),
            'me' => $me,
            'qrUrl' => $qrUrl,
            'users' => $users,
            'elig' => [
                'kelas' => AppSetting::get('eskul.kelas_allowed', '10,11'),
                'semester' => AppSetting::get('eskul.semester_aktif', '1'),
                'kelas11_smt1' => AppSetting::get('eskul.kelas11_smt1_only', '1') === '1',
            ],
        ]);
    }

    public function update(Request $request)
    {
        // Catatan: nama field memakai underscore karena PHP mengubah
        // titik pada name input menjadi underscore.
        $data = $request->validate([
            'school_name' => ['nullable', 'string', 'max:255'],
            'school_contact' => ['nullable', 'string', 'max:255'],
            'school_address' => ['nullable', 'string', 'max:1000'],
            'school_logo' => ['nullable', 'string', 'max:255'],
        ]);

        $peta = [
            'school_name' => 'school.name',
            'school_contact' => 'school.contact',
            'school_address' => 'school.address',
            'school_logo' => 'school.logo',
        ];

        foreach ($peta as $input => $key) {
            AppSetting::set($key, (string) ($data[$input] ?? ''));
        }

        AdminLog::record('settings.update', 'AppSetting', null, 'Perbarui identitas sekolah.');

        return back()->with('status', 'Identitas sekolah disimpan.');
    }

    /**
     * Syarat ikut eskul (kategori yang boleh daftar): tingkat kelas,
     * semester aktif, dan aturan magang kelas 11.
     */
    public function eligibility(Request $request)
    {
        $data = $request->validate([
            'kelas_allowed' => ['required', 'string', 'max:50', 'regex:/^[0-9,\s]+$/'],
            'semester_aktif' => ['required', 'in:1,2'],
            'kelas11_smt1_only' => ['nullable', 'boolean'],
        ]);

        AppSetting::set('eskul.kelas_allowed', $data['kelas_allowed']);
        AppSetting::set('eskul.semester_aktif', $data['semester_aktif']);
        AppSetting::set('eskul.kelas11_smt1_only', $request->boolean('kelas11_smt1_only') ? '1' : '0');

        AdminLog::record('settings.eligibility', 'AppSetting', null, 'Ubah syarat eskul.');

        return back()->with('status', 'Syarat eskul disimpan.');
    }

    public function maintenance(Request $request)
    {
        $data = $request->validate([
            'aktif' => ['nullable', 'boolean'],
            'pesan' => ['nullable', 'string', 'max:500'],
            'eta' => ['nullable', 'string', 'max:100'],
        ]);

        $aktif = $request->boolean('aktif');
        AppSetting::set('app.maintenance', $aktif ? '1' : '0');
        AppSetting::set('app.maintenance_msg', $data['pesan'] ?? 'Sistem sedang dalam proses pemeliharaan dan optimalisasi data.');
        AppSetting::set('app.maintenance_eta', $data['eta'] ?? '');

        AdminLog::record(
            'settings.maintenance',
            'AppSetting',
            null,
            $aktif ? 'Mode pemeliharaan DIAKTIFKAN.' : 'Mode pemeliharaan dimatikan.'
        );

        return back()->with('status', $aktif
            ? 'Mode pemeliharaan aktif — siswa melihat halaman informasi pemeliharaan.'
            : 'Mode pemeliharaan dimatikan — sistem kembali normal.');
    }

    public function mfaGenerate()
    {
        $me = auth()->user();
        $secret = (new Google2FA)->generateSecretKey();

        $me->forceFill([
            'totp_secret_enc' => Crypt::encryptString($secret),
            'totp_enabled' => false,
        ])->save();

        AdminLog::record('settings.mfa_generate', 'User', $me->id, 'Mulai aktivasi MFA.');

        return back()->with('status', 'Rahasia MFA dibuat. Pindai QR lalu masukkan 6 digit kode.');
    }

    public function mfaConfirm(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $me = auth()->user();

        if (! $me->totp_secret_enc) {
            return back()->withErrors(['code' => 'Belum ada rahasia MFA. Klik Aktifkan dulu.']);
        }

        $ok = (new Google2FA)->verifyKey(
            Crypt::decryptString($me->totp_secret_enc),
            $data['code'],
            1
        );

        if (! $ok) {
            return back()->withErrors(['code' => 'Kode salah/kedaluwarsa. Coba lagi.']);
        }

        $me->forceFill(['totp_enabled' => true])->save();

        AdminLog::record('settings.mfa_enable', 'User', $me->id, 'MFA diaktifkan.');

        return back()->with('status', 'MFA aktif. Login berikutnya meminta kode authenticator.');
    }

    public function mfaDisable(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $me = auth()->user();
        $me->forceFill(['totp_enabled' => false, 'totp_secret_enc' => null])->save();

        AdminLog::record('settings.mfa_disable', 'User', $me->id, 'MFA dinonaktifkan.');

        return back()->with('status', 'MFA dinonaktifkan.');
    }
}

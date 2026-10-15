<?php

namespace App\Http\Controllers\Auth;

use App\Enums\LoginMethod;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use App\Models\Student;
use App\Models\User;
use App\Support\LoginToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SiswaLoginController extends Controller
{
    public function create(): View
    {
        return view('auth.siswa-login');
    }

    /**
     * Login siswa: NIS + token 8 digit, atau NIS + password
     * (bila siswa sudah mengatur password di Profil).
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nis' => ['required', 'string', 'max:20'],
            'token' => ['required', 'string', 'max:100'],
        ]);

        // Normalisasi khusus token: buang spasi/strip (password dipakai mentah).
        $normal = preg_replace('/[\s-]+/', '', $data['token']);

        $throttleKey = 'siswa-login:'.$request->ip().':'.$data['nis'];

        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'token' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        $master = Student::where('nis', $data['nis'])->first();

        if (! $master || $master->status !== 'AKTIF') {
            // Pesan generik sama seperti kredensial salah agar NIS valid tidak bisa dienumerasi.
            $this->fail($request, $throttleKey, null, null, 'NIS tidak dikenal', 'Token atau password salah. Periksa kembali kartu login Anda, atau hubungi TU bila belum punya token.', 'token');
        }

        $user = $master->user ?? User::where('username', $master->nis)->first();

        if (! $user) {
            $user = User::create([
                'name' => $master->nama,
                'email' => strtolower($master->nis).'@siswa.local',
                'role' => Role::STUDENT,
                'username' => $master->nis,
                // Akun siswa tidak memakai password (login via token).
                'password' => Hash::make(Str::random(32)),
            ]);
        }

        if ($master->user_id !== $user->getAuthIdentifier()) {
            $master->update(['user_id' => $user->getAuthIdentifier()]);
        }

        $tokenOk = $user->login_token_hash
            && $user->login_token_revoked_at === null
            && LoginToken::verify($normal, $user->login_token_hash);

        $passOk = $user->password_set_at !== null
            && Hash::check($data['token'], $user->password);

        if (! $tokenOk && ! $passOk) {
            $this->fail($request, $throttleKey, $user->getAuthIdentifier(), null, 'Kredensial salah', 'Token atau password salah. Periksa kembali kartu login Anda, atau hubungi TU bila belum punya token.', 'token');
        }

        RateLimiter::clear($throttleKey);

        Auth::login($user);
        $request->session()->regenerate();
        $tabToken = Str::random(32);
        session([
            'session_version' => $user->getAttribute('session_version'),
            'auth_session_id' => $tabToken,
            'fresh_login' => true,
        ]);

        LoginHistory::create([
            'user_id' => $user->getAuthIdentifier(),
            'email' => $user->email,
            'ip_hash' => hash('sha256', $request->ip().' '.config('app.key')),
            'method' => $tokenOk ? LoginMethod::TOKEN : LoginMethod::PASSWORD,
            'success' => true,
            'reason' => null,
        ]);

        return redirect()->intended('/siswa');
    }

    private function fail(
        Request $request,
        string $throttleKey,
        string|int|null $userId,
        ?string $email,
        string $reason,
        string $message,
        string $field = 'nis',
    ): never {
        RateLimiter::hit($throttleKey);

        LoginHistory::create([
            'user_id' => $userId,
            'email' => $email,
            'ip_hash' => hash('sha256', $request->ip().' '.config('app.key')),
            'method' => LoginMethod::TOKEN,
            'success' => false,
            'reason' => $reason,
        ]);

        throw ValidationException::withMessages([$field => $message]);
    }
}

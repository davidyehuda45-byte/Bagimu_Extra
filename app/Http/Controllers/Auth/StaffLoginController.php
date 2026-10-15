<?php

namespace App\Http\Controllers\Auth;

use App\Enums\LoginMethod;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StaffLoginController extends Controller
{
    /** Role yang boleh masuk via pintu staf (admin panel + pembina). */
    private const ADMIN_PANEL_ROLES = [Role::ADMIN, Role::OPERATOR, Role::KEPALA_SEKOLAH, Role::PEMBINA];

    public function create(): View
    {
        return view('auth.admin-login');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = 'staff-login:'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5, 300)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'login' => "Terlalu banyak percobaan login gagal. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        $login = $data['login'];
        $user = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? User::where('email', $login)->first()
            : User::where('username', $login)->first();

        if (! $user || ! $user->password || ! Hash::check($data['password'], $user->password)) {
            $this->hitAndLog($request, $throttleKey, $user?->getAuthIdentifier(), $login, 'Kredensial salah');

            throw ValidationException::withMessages([
                'login' => 'Username/email atau password salah.',
            ]);
        }

        if (! in_array($user->role, self::ADMIN_PANEL_ROLES, true)) {
            $reason = $user->role === Role::STUDENT ? 'Akun siswa mencoba masuk panel staf' : 'Bukan akun staf';
            $this->hitAndLog($request, $throttleKey, $user->getAuthIdentifier(), $user->email, $reason);

            $pesan = $user->role === Role::STUDENT
                ? 'Ini halaman login staf/pembina. Akun siswa silakan login lewat halaman login siswa.'
                : 'Hanya staf (admin/operator/kepala sekolah/pembina) yang memiliki akses masuk ke panel ini.';

            throw ValidationException::withMessages([
                'login' => $pesan,
            ]);
        }

        RateLimiter::clear($throttleKey);

        if ($user->totp_enabled) {
            session(['mfa_user_id' => $user->getAuthIdentifier()]);
            $request->session()->regenerate();

            return redirect()->route('admin.mfa');
        }

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
            'method' => LoginMethod::USERNAME,
            'success' => true,
            'reason' => null,
        ]);

        return redirect()->intended('/dashboard');
    }

    private function hitAndLog(
        Request $request,
        string $throttleKey,
        string|int|null $userId,
        ?string $email,
        string $reason,
    ): void {
        RateLimiter::hit($throttleKey);

        LoginHistory::create([
            'user_id' => $userId,
            'email' => $email,
            'ip_hash' => hash('sha256', $request->ip().' '.config('app.key')),
            'method' => LoginMethod::USERNAME,
            'success' => false,
            'reason' => $reason,
        ]);
    }
}

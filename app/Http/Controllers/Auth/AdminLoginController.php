<?php

namespace App\Http\Controllers\Auth;

use App\Enums\LoginMethod;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    /** Role yang diizinkan masuk ke portal Administrator */
    private const ALLOWED_ROLES = [Role::ADMIN, Role::OPERATOR, Role::KEPALA_SEKOLAH];

    public function create(): View
    {
        return view('auth.admin-login');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = 'admin-login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5, 300)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            AdminLog::record(
                'auth.throttle',
                'User',
                null,
                "Percobaan login admin diblokir rate limiter ({$seconds} detik).",
                'FAILED',
                $data['login']
            );

            throw ValidationException::withMessages([
                'login' => "Terlalu banyak percobaan login gagal. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        $login = $data['login'];
        $user = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? User::where('email', $login)->first()
            : User::where('username', $login)->first();

        if (! $user || ! $user->password || ! Hash::check($data['password'], $user->password)) {
            $this->logFailure($request, $throttleKey, $user?->id, $login, 'Kredensial login admin salah');

            throw ValidationException::withMessages([
                'login' => 'Username/email atau password admin salah.',
            ]);
        }

        // Pengecekan ketat: Pembina DILARANG login di portal admin
        if ($user->role === Role::PEMBINA) {
            $this->logFailure($request, $throttleKey, $user->id, $user->email, 'Pembina mencoba login ke portal Admin');

            throw ValidationException::withMessages([
                'login' => 'Akun Pembina tidak diizinkan masuk melalui portal Administrator. Silakan login melalui halaman Pembina.',
            ]);
        }

        if ($user->role === Role::STUDENT) {
            $this->logFailure($request, $throttleKey, $user->id, $user->email, 'Siswa mencoba login ke portal Admin');

            throw ValidationException::withMessages([
                'login' => 'Portal ini khusus Administrator. Siswa silakan login melalui halaman login siswa.',
            ]);
        }

        if (! in_array($user->role, self::ALLOWED_ROLES, true)) {
            $this->logFailure($request, $throttleKey, $user->id, $user->email, 'Role tanpa izin mencoba login ke portal Admin');

            throw ValidationException::withMessages([
                'login' => 'Akun Anda tidak memiliki hak akses ke panel Administrator.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        if ($user->totp_enabled) {
            session(['mfa_user_id' => $user->getAuthIdentifier()]);
            $request->session()->regenerate();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('admin.mfa'),
                ]);
            }

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
            'ip_hash' => hash('sha256', $request->ip() . ' ' . config('app.key')),
            'method' => LoginMethod::USERNAME,
            'success' => true,
            'reason' => null,
        ]);

        AdminLog::record(
            'auth.admin_login',
            'User',
            $user->id,
            "Administrator {$user->name} ({$user->email}) berhasil login.",
            'SUCCESS'
        );

        $tujuan = session()->pull('url.intended', route('admin.dashboard'));

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => $tujuan,
            ]);
        }

        return redirect()->to($tujuan);
    }

    private function logFailure(
        Request $request,
        string $throttleKey,
        string|int|null $userId,
        ?string $email,
        string $reason
    ): void {
        RateLimiter::hit($throttleKey);

        LoginHistory::create([
            'user_id' => $userId,
            'email' => $email,
            'ip_hash' => hash('sha256', $request->ip() . ' ' . config('app.key')),
            'method' => LoginMethod::USERNAME,
            'success' => false,
            'reason' => $reason,
        ]);

        AdminLog::record(
            'auth.admin_login_failed',
            'User',
            $userId,
            "Gagal login admin ({$email}): {$reason}",
            'FAILED',
            $email,
            $userId,
            'GUEST'
        );
    }
}

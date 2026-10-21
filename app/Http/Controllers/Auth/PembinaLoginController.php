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

class PembinaLoginController extends Controller
{
    /** Role staf lapangan yang boleh masuk via pintu Pembina */
    private const ALLOWED_ROLES = [Role::PEMBINA, Role::WALI_KELAS];

    public function create(): View
    {
        // Jika view khusus auth.pembina-login sudah ada, pakai itu, jika belum fallback ke admin-login
        if (view()->exists('auth.pembina-login')) {
            return view('auth.pembina-login');
        }

        return view('auth.admin-login');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = 'pembina-login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5, 300)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            AdminLog::record(
                'auth.throttle',
                'User',
                null,
                "Percobaan login pembina diblokir rate limiter ({$seconds} detik).",
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
            $this->logFailure($request, $throttleKey, $user?->id, $login, 'Kredensial login pembina salah');

            throw ValidationException::withMessages([
                'login' => 'Username/email atau password salah.',
            ]);
        }

        // Pengecekan ketat: Admin DILARANG login di portal pembina
        if (in_array($user->role, [Role::ADMIN, Role::OPERATOR, Role::KEPALA_SEKOLAH], true)) {
            $this->logFailure($request, $throttleKey, $user->id, $user->email, 'Admin mencoba login ke portal Pembina');

            throw ValidationException::withMessages([
                'login' => 'Akun Administrator tidak dapat login melalui portal Pembina. Silakan masuk melalui halaman login Admin.',
            ]);
        }

        if ($user->role === Role::STUDENT) {
            $this->logFailure($request, $throttleKey, $user->id, $user->email, 'Siswa mencoba login ke portal Pembina');

            throw ValidationException::withMessages([
                'login' => 'Halaman ini khusus Pembina eskul. Siswa silakan login melalui halaman login siswa.',
            ]);
        }

        if (! in_array($user->role, self::ALLOWED_ROLES, true)) {
            $this->logFailure($request, $throttleKey, $user->id, $user->email, 'Role tanpa izin mencoba login ke portal Pembina');

            throw ValidationException::withMessages([
                'login' => 'Akun Anda tidak memiliki hak akses ke panel Pembina.',
            ]);
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
            'ip_hash' => hash('sha256', $request->ip() . ' ' . config('app.key')),
            'method' => LoginMethod::USERNAME,
            'success' => true,
            'reason' => null,
        ]);

        AdminLog::record(
            'auth.pembina_login',
            'User',
            $user->id,
            "Pembina {$user->name} ({$user->email}) berhasil login.",
            'SUCCESS'
        );

        $tujuan = $user->role === Role::WALI_KELAS
            ? session()->pull('url.intended', route('wali-kelas.dashboard'))
            : session()->pull('url.intended', route('pembina.dashboard'));

        $path = parse_url((string) $tujuan, PHP_URL_PATH) ?? '';
        if (str_starts_with($path, '/admin')) {
            $tujuan = $user->role === Role::WALI_KELAS ? route('wali-kelas.dashboard') : route('pembina.dashboard');
        }

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
            'auth.pembina_login_failed',
            'User',
            $userId,
            "Gagal login pembina ({$email}): {$reason}",
            'FAILED',
            $email,
            $userId,
            'GUEST'
        );
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Enums\LoginMethod;
use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class MfaController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (! session()->has('mfa_user_id')) {
            return redirect()->route('admin.login');
        }

        return view('auth.mfa');
    }

    public function store(Request $request): RedirectResponse
    {
        $userId = session('mfa_user_id');

        if (! $userId) {
            return redirect()->route('admin.login');
        }

        $data = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = User::find($userId);

        $ok = $user && $user->totp_enabled && $user->totp_secret_enc
            && (new Google2FA)->verifyKey(
                Crypt::decryptString($user->totp_secret_enc),
                $data['code'],
                1
            );

        if (! $ok) {
            LoginHistory::create([
                'user_id' => $userId,
                'email' => $user?->email,
                'ip_hash' => hash('sha256', $request->ip().' '.config('app.key')),
                'method' => LoginMethod::USERNAME,
                'success' => false,
                'reason' => 'Kode MFA salah',
            ]);

            throw ValidationException::withMessages([
                'code' => 'Kode MFA salah/kedaluwarsa.',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();
        $tabToken = \Illuminate\Support\Str::random(32);
        session([
            'session_version' => $user->getAttribute('session_version'),
            'auth_session_id' => $tabToken,
            'fresh_login' => true,
        ]);
        session()->forget(['mfa_user_id', 'mfa_remember']);

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
}

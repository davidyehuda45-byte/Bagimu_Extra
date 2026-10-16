<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();
        $tabToken = \Illuminate\Support\Str::random(32);
        session([
            'session_version' => $user?->getAttribute('session_version'),
            'auth_session_id' => $tabToken,
            'fresh_login' => true,
        ]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Hanguskan sesi saat pengguna keluar dari situs / tab demi keamanan.
     */
    public function sessionExit(Request $request): RedirectResponse
    {
        $user = $request->user();
        $isStaff = $user && in_array($user->role, [
            \App\Enums\Role::ADMIN,
            \App\Enums\Role::OPERATOR,
            \App\Enums\Role::PEMBINA,
            \App\Enums\Role::KEPALA_SEKOLAH,
        ], true);

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($isStaff) {
            return redirect()->route('admin.login')->with('status', 'Sesi Anda telah berakhir demi keamanan. Silakan login kembali.');
        }

        $sudahDaftar = $user && \App\Models\Registration::where('user_id', $user->id)->exists();
        if ($sudahDaftar) {
            return redirect()->route('siswa.login')->with('status', 'Pendaftaran Anda telah berhasil disimpan dan data telah terkunci. Token Anda bersifat sekali pakai.');
        }

        return redirect()->route('siswa.login')->with('status', 'Sesi Anda telah berakhir demi keamanan. Silakan login kembali.');
    }
}

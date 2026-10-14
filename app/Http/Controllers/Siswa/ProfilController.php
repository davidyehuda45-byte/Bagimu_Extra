<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function show(): View
    {
        $user = auth()->user()->load('student');
        $terakhir = $user->registrations()->latest()->first();
        $notifikasi = $user->inappNotifications()->latest()->take(10)->get();
        $logins = $user->loginHistory()->latest()->take(10)->get();

        return view('siswa.profil', compact('user', 'terakhir', 'notifikasi', 'logins'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'wali_nama' => ['nullable', 'string', 'max:100'],
            'wali_kontak' => ['nullable', 'string', 'max:20'],
            'notify_wa' => ['nullable', 'boolean'],
            'notify_email' => ['nullable', 'boolean'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'wali_nama' => $validated['wali_nama'] ?? null,
            'wali_kontak' => $validated['wali_kontak'] ?? null,
            'notify_wa' => $request->boolean('notify_wa'),
            'notify_email' => $request->boolean('notify_email'),
        ]);

        if ($user->student) {
            $user->student->update([
                'wali_nama' => $validated['wali_nama'] ?? null,
                'wali_kontak' => $validated['wali_kontak'] ?? null,
            ]);
        }

        $terakhir = $user->registrations()->latest()->first();
        if ($terakhir) {
            $terakhir->update(['whatsapp' => $validated['whatsapp'] ?? null]);
        }

        return redirect()->route('siswa.profil')
            ->with('status', 'Profil berhasil diperbarui.');
    }

    /**
     * Atur/ganti password untuk login alternatif NIS + password.
     * Bila belum pernah mengatur, password lama tidak diminta.
     * Sesi lain otomatis keluar setelah ganti.
     */
    public function password(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $rules = [
            'password' => ['required', 'string', 'min:8', 'max:100', 'confirmed'],
        ];

        if ($user->password_set_at) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $validated = $request->validate($rules);

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'password_set_at' => now(),
        ])->save();

        $user->increment('session_version');
        session(['session_version' => $user->getAttribute('session_version')]);

        return back()->with('status', 'Password berhasil disimpan. Sesi lain otomatis keluar.');
    }
}

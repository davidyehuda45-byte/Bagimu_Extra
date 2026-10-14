<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', [Role::ADMIN, Role::OPERATOR, Role::PEMBINA, Role::KEPALA_SEKOLAH])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:191', 'unique:users,email'],
            'username' => ['nullable', 'string', 'max:30', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
            'role' => ['required', Rule::in([Role::ADMIN->value, Role::OPERATOR->value, Role::PEMBINA->value, Role::KEPALA_SEKOLAH->value])],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'username' => $data['username'] ? strtolower($data['username']) : null,
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        AdminLog::record('user.create', 'User', $user->id, "Membuat akun {$user->email} ({$user->role->value}).");

        return back()->with('status', "Akun {$user->email} dibuat. Tugaskan eskul via Master Eskul bila pembina.");
    }

    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', Rule::in([Role::ADMIN->value, Role::OPERATOR->value, Role::PEMBINA->value, Role::KEPALA_SEKOLAH->value])],
        ]);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['role' => 'Tidak dapat mengubah peran akun sendiri.']);
        }

        if (
            $user->role === Role::ADMIN && $data['role'] !== Role::ADMIN->value
            && User::where('role', Role::ADMIN)->count() <= 1
        ) {
            return back()->withErrors(['role' => 'Tidak dapat menurunkan admin terakhir.']);
        }

        $user->update(['role' => $data['role']]);

        AdminLog::record('user.role', 'User', $user->id, "Ubah peran {$user->email} ke {$data['role']}.");

        return back()->with('status', 'Peran akun diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'Tidak dapat menghapus akun sendiri.']);
        }

        if ($user->role === Role::ADMIN && User::where('role', Role::ADMIN)->count() <= 1) {
            return back()->withErrors(['user' => 'Tidak dapat menghapus admin terakhir.']);
        }

        $email = $user->email;
        $user->delete();

        AdminLog::record('user.delete', 'User', $user->id, "Menghapus akun {$email}.");

        return back()->with('status', "Akun {$email} dihapus.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'max:100'],
        ]);

        $user->forceFill([
            'password' => Hash::make($data['password']),
            'password_set_at' => now(),
        ])->save();
        $user->increment('session_version');

        AdminLog::record('user.reset_password', 'User', $user->id, "Reset password staff {$user->email}.");

        return back()->with('status', "Password untuk akun {$user->email} berhasil direset.");
    }
}

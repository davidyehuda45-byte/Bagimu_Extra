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
            ->with('eskulDiampu')
            ->orderBy('name')
            ->paginate(20);

        $eskuls = \App\Models\Eskul::orderBy('name')->get(['id', 'name', 'pembina_id']);

        return view('admin.users.index', compact('users', 'eskuls'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:191', 'unique:users,email'],
            'username' => ['nullable', 'string', 'max:30', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
            'role' => ['required', Rule::in([Role::ADMIN->value, Role::OPERATOR->value, Role::PEMBINA->value, Role::KEPALA_SEKOLAH->value])],
            'eskul_ids' => ['nullable', 'array'],
            'eskul_ids.*' => ['integer', 'exists:eskuls,id'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'username' => $data['username'] ? strtolower($data['username']) : null,
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        $ditugaskan = [];
        if ($data['role'] === Role::PEMBINA->value && ! empty($data['eskul_ids'])) {
            $ditugaskan = $this->tugaskanEskul($user, $data['eskul_ids']);
        }

        AdminLog::record('user.create', 'User', $user->id, "Membuat akun {$user->email} ({$user->role->value}).");

        $pesan = "Akun {$user->email} dibuat.";
        if ($ditugaskan !== []) {
            $pesan .= ' Ditugaskan mengampu: '.implode(', ', $ditugaskan).'.';
        }

        return back()->with('status', $pesan);
    }

    /**
     * Tetapkan eskul yang diampu pembina. Kembalikan nama eskul yang berhasil ditugaskan.
     */
    public static function tugaskanEskul(User $user, array $eskulIds): array
    {
        $names = [];
        foreach (array_unique($eskulIds) as $id) {
            $eskul = \App\Models\Eskul::find($id);
            if ($eskul) {
                $eskul->update(['pembina_id' => $user->id]);
                $names[] = $eskul->name;
            }
        }

        return $names;
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

        $peranLama = $user->role->value;
        $user->update(['role' => $data['role']]);

        // Bila bukan pembina lagi, lepaskan eskul yang diampunya agar tidak yatim.
        if ($peranLama === Role::PEMBINA->value && $data['role'] !== Role::PEMBINA->value) {
            \App\Models\Eskul::where('pembina_id', $user->id)->update(['pembina_id' => null]);
        }

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

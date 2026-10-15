<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Mail\UndanganAdmin;
use App\Models\AdminLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InvitationController extends Controller
{
    public function index()
    {
        $eskuls = \App\Models\Eskul::orderBy('name')->get(['id', 'name', 'pembina_id']);

        return view('admin.invitations.index', compact('eskuls'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:191', 'unique:users,email'],
            'role' => ['required', Rule::in([
                Role::ADMIN->value, Role::OPERATOR->value,
                Role::PEMBINA->value, Role::KEPALA_SEKOLAH->value,
            ])],
            'eskul_ids' => ['nullable', 'array'],
            'eskul_ids.*' => ['integer', 'exists:eskuls,id'],
        ]);

        $dasar = Str::slug(Str::before(strtolower($data['email']), '@'), '');
        $dasar = $dasar !== '' ? $dasar : 'staf';
        $username = $dasar;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $dasar.(++$i);
        }

        $password = Str::random(10);

        $user = User::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'username' => $username,
            'password' => Hash::make($password),
            'role' => $data['role'],
        ]);

        $loginUrl = route('admin.login');

        $ditugaskan = [];
        if ($data['role'] === Role::PEMBINA->value && ! empty($data['eskul_ids'])) {
            $ditugaskan = UserController::tugaskanEskul($user, $data['eskul_ids']);
        }

        try {
            Mail::to($user->email)->send(new UndanganAdmin(
                $user->name, $username, $password,
                Role::from($data['role'])->label(), $loginUrl
            ));
            $mailStatus = 'terkirim ke '.$user->email;
        } catch (\Throwable $e) {
            $mailStatus = 'tidak terkirim otomatis — teruskan manual ke calon admin.';
        }

        AdminLog::record('invitation.create', 'User', $user->id, "Undang {$user->email} sebagai {$data['role']} (email: {$mailStatus}).");

        return view('admin.invitations.result', compact('user', 'username', 'password', 'loginUrl', 'mailStatus', 'ditugaskan'));
    }
}

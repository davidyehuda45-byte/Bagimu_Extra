<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WaliKelasAdminController extends Controller
{
    /**
     * Tampilkan daftar akun Wali Kelas beserta kelas binaannya.
     */
    public function index(): View
    {
        $waliKelases = User::where('role', Role::WALI_KELAS)
            ->with(['managedClass.students'])
            ->orderBy('name')
            ->paginate(20);

        // Seluruh daftar rombel kelas aktif untuk pilihan dropdown
        $classes = SchoolClass::orderBy('tingkat')
            ->orderBy('nama')
            ->get();

        return view('admin.wali-kelas.index', compact('waliKelases', 'classes'));
    }

    /**
     * Tambah akun Wali Kelas baru dan langsung tautkan ke rombel kelas binaan.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:191', 'unique:users,email'],
            'username' => ['nullable', 'string', 'max:30', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'username' => $data['username'] ? strtolower($data['username']) : null,
            'password' => Hash::make($data['password']),
            'role' => Role::WALI_KELAS,
        ]);

        $namaKelas = null;
        if (! empty($data['school_class_id'])) {
            $class = SchoolClass::find($data['school_class_id']);
            if ($class) {
                $class->update([
                    'wali_kelas_id' => $user->id,
                    'wali_kelas_nama' => $user->name,
                ]);
                $namaKelas = $class->nama;
            }
        }

        AdminLog::record(
            'wali_kelas.create',
            'User',
            $user->id,
            "Membuat akun Wali Kelas {$user->name} ({$user->email})" . ($namaKelas ? " untuk rombel {$namaKelas}." : ".")
        );

        $pesan = "Akun Wali Kelas {$user->name} berhasil ditambahkan.";
        if ($namaKelas) {
            $pesan .= " Ditugaskan mengampu rombel {$namaKelas}.";
        }

        return back()->with('status', $pesan);
    }

    /**
     * Perbarui data Wali Kelas atau ganti rombel kelas binaan.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== Role::WALI_KELAS) {
            return back()->withErrors(['error' => 'Pengguna ini bukan akun Wali Kelas.']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:191', Rule::unique('users', 'email')->ignore($user->id)],
            'username' => ['nullable', 'string', 'max:30', Rule::unique('users', 'username')->ignore($user->id)],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'username' => $data['username'] ? strtolower($data['username']) : null,
        ]);

        // Lepaskan penugasan rombel lama jika ada
        SchoolClass::where('wali_kelas_id', $user->id)->update([
            'wali_kelas_id' => null,
            'wali_kelas_nama' => null,
        ]);

        // Tautkan ke rombel baru jika dipilih
        $namaKelas = null;
        if (! empty($data['school_class_id'])) {
            $class = SchoolClass::find($data['school_class_id']);
            if ($class) {
                $class->update([
                    'wali_kelas_id' => $user->id,
                    'wali_kelas_nama' => $user->name,
                ]);
                $namaKelas = $class->nama;
            }
        }

        AdminLog::record(
            'wali_kelas.update',
            'User',
            $user->id,
            "Memperbarui data Wali Kelas {$user->name}" . ($namaKelas ? " (Rombel: {$namaKelas})." : ".")
        );

        return back()->with('status', "Data Wali Kelas {$user->name} berhasil diperbarui.");
    }

    /**
     * Reset kata sandi akun Wali Kelas.
     */
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'max:100'],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        AdminLog::record(
            'wali_kelas.password_reset',
            'User',
            $user->id,
            "Mereset kata sandi akun Wali Kelas {$user->name} ({$user->email})."
        );

        return back()->with('status', "Kata sandi untuk {$user->name} berhasil diubah.");
    }

    /**
     * Hapus akun Wali Kelas dan lepaskan rombel kelas binaan.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->role !== Role::WALI_KELAS) {
            return back()->withErrors(['error' => 'Pengguna ini bukan akun Wali Kelas.']);
        }

        // Lepas penugasan kelas binaan sebelum akun dihapus
        SchoolClass::where('wali_kelas_id', $user->id)->update([
            'wali_kelas_id' => null,
            'wali_kelas_nama' => null,
        ]);

        $name = $user->name;
        $email = $user->email;
        $userId = $user->id;

        $user->delete();

        AdminLog::record(
            'wali_kelas.delete',
            'User',
            $userId,
            "Menghapus akun Wali Kelas {$name} ({$email})."
        );

        return back()->with('status', "Akun Wali Kelas {$name} berhasil dihapus.");
    }
}

<?php

namespace Tests\Feature;

use App\Enums\Gender;
use App\Enums\Jurusan;
use App\Enums\Role;
use App\Models\Eskul;
use App\Models\Registration;
use App\Models\Student;
use App\Models\User;
use App\Support\LoginToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaDirectLoginTest extends TestCase
{
    use RefreshDatabase;

    private function createStudentWithToken(string $nis = '20001', string $name = 'Budi Test'): array
    {
        $student = Student::create([
            'nis' => $nis,
            'nama' => $name,
            'kelas' => '10 RPL 1',
            'jurusan' => Jurusan::RPL,
            'gender' => Gender::LAKI_LAKI,
            'whatsapp' => '081234567890',
            'status' => 'AKTIF',
        ]);

        $user = User::create([
            'name' => $student->nama,
            'email' => $student->nis.'@siswa.local',
            'username' => $student->nis,
            'password' => bcrypt('secret123'),
            'role' => Role::STUDENT,
        ]);
        $student->update(['user_id' => $user->id]);

        $rawToken = LoginToken::issue($user);

        return [$student, $user, $rawToken];
    }

    public function test_student_can_direct_login_via_magic_link(): void
    {
        [$student, $user, $rawToken] = $this->createStudentWithToken('20001', 'Ahmad Dani');

        $response = $this->get(route('siswa.direct-login', [
            'nis' => $student->nis,
            'token' => $rawToken,
        ]));

        $response->assertRedirect(route('siswa.daftar'));
        $response->assertSessionHas('status');
        $this->assertAuthenticatedAs($user);
    }

    public function test_direct_login_fails_if_params_missing(): void
    {
        $response = $this->get(route('siswa.direct-login'));

        $response->assertRedirect(route('siswa.login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_direct_login_fails_if_token_invalid(): void
    {
        [$student] = $this->createStudentWithToken('20002', 'Budi Salah');

        $response = $this->get(route('siswa.direct-login', [
            'nis' => $student->nis,
            'token' => '99999999',
        ]));

        $response->assertRedirect(route('siswa.login'));
        $response->assertSessionHas('error');
        $response->assertSessionHas('_old_input.nis', $student->nis);
        $this->assertGuest();
    }

    public function test_direct_login_fails_if_student_already_chose_eskul(): void
    {
        [$student, $user, $rawToken] = $this->createStudentWithToken('20003', 'Citra Kunci');

        $eskul = Eskul::create([
            'name' => 'Pramuka',
            'kuota' => 50,
            'aktif' => true,
        ]);

        Registration::create([
            'user_id' => $user->id,
            'student_id' => $student->id,
            'nis' => $student->nis,
            'nama_lengkap' => $student->nama,
            'kelas' => $student->kelas,
            'jurusan' => $student->jurusan,
            'eskul_id' => $eskul->id,
            'eskul_utama' => $eskul->name,
            'periode' => '2026/2027',
            'usia' => 16,
            'gender' => 'LAKI_LAKI',
            'status' => \App\Enums\Status::APPROVED,
        ]);

        $response = $this->get(route('siswa.direct-login', [
            'nis' => $student->nis,
            'token' => $rawToken,
        ]));

        $response->assertRedirect(route('siswa.login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_standard_form_login_still_works(): void
    {
        [$student, $user, $rawToken] = $this->createStudentWithToken('20004', 'Deni Form');

        $response = $this->post(route('siswa.login'), [
            'nis' => $student->nis,
            'token' => $rawToken,
        ]);

        $response->assertRedirect('/siswa/daftar');
        $this->assertAuthenticatedAs($user);
    }
}

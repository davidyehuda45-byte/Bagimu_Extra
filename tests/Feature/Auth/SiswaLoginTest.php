<?php

namespace Tests\Feature\Auth;

use App\Enums\Role;
use App\Models\Student;
use App\Models\User;
use App\Support\LoginToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_login_siswa_bisa_ditampilkan(): void
    {
        $response = $this->get('/siswa/login');

        $response->assertOk();
    }

    private function master(array $overrides = []): Student
    {
        return Student::create(array_merge([
            'nis' => '10001',
            'nama' => 'Siswa Tes',
            'kelas' => 'X RPL 1',
            'jurusan' => 'RPL',
            'status' => 'AKTIF',
        ], $overrides));
    }

    public function test_siswa_bisa_login_dengan_nis_dan_token(): void
    {
        $master = $this->master();
        $user = User::factory()->create(['username' => '10001', 'role' => Role::STUDENT]);
        $token = LoginToken::issue($user);

        $response = $this->post('/siswa/login', ['nis' => '10001', 'token' => $token]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/siswa/daftar');
        $this->assertSame($user->id, $master->fresh()->user_id);
    }

    public function test_akun_dibuat_otomatis_dari_data_induk(): void
    {
        $this->master();
        $user = User::factory()->create(['username' => '10001', 'role' => Role::STUDENT]);
        $token = LoginToken::issue($user);
        $user->delete();

        $this->post('/siswa/login', ['nis' => '10001', 'token' => $token]);

        // Token milik akun lama tidak berlaku untuk akun baru.
        $this->assertGuest();
        $this->assertDatabaseHas('users', ['username' => '10001']);
    }

    public function test_nis_tidak_dikenal_ditolak(): void
    {
        $response = $this->post('/siswa/login', ['nis' => '99999', 'token' => '12345678']);

        $this->assertGuest();
        $response->assertSessionHasErrors('token');
    }

    public function test_nis_nonaktif_ditolak(): void
    {
        $this->master(['status' => 'NONAKTIF']);

        $response = $this->post('/siswa/login', ['nis' => '10001', 'token' => '12345678']);

        $this->assertGuest();
        $response->assertSessionHasErrors('token');
    }

    public function test_token_salah_ditolak(): void
    {
        $this->master();
        $user = User::factory()->create(['username' => '10001', 'role' => Role::STUDENT]);
        LoginToken::issue($user);

        $response = $this->post('/siswa/login', ['nis' => '10001', 'token' => '00000000']);

        $this->assertGuest();
        $response->assertSessionHasErrors('token');
    }

    public function test_belum_punya_token_ditolak(): void
    {
        $this->master();
        User::factory()->create(['username' => '10001', 'role' => Role::STUDENT]);

        $response = $this->post('/siswa/login', ['nis' => '10001', 'token' => '12345678']);

        $this->assertGuest();
        $response->assertSessionHasErrors('token');
    }
}

<?php

namespace Tests\Feature\Auth;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_login_staf_bisa_ditampilkan(): void
    {
        $response = $this->get('/admin/login');

        $response->assertOk();
    }

    public function test_staf_bisa_login_pakai_email(): void
    {
        $user = User::factory()->create(['role' => Role::ADMIN]);

        $response = $this->post('/admin/login', [
            'login' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_pembina_login_diarahkan_ke_dashboard_pembina(): void
    {
        $user = User::factory()->create(['role' => Role::PEMBINA]);

        $response = $this->withSession(['url.intended' => 'http://localhost/admin/dashboard'])
            ->post('/admin/login', [
                'login' => $user->email,
                'password' => 'password',
            ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('pembina.dashboard'));
    }

    public function test_staf_bisa_login_pakai_username(): void
    {
        $user = User::factory()->create(['role' => Role::OPERATOR, 'username' => 'operator1']);

        $response = $this->post('/admin/login', [
            'login' => 'operator1',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_login_ditolak_bila_password_salah(): void
    {
        $user = User::factory()->create(['role' => Role::ADMIN]);

        $this->post('/admin/login', [
            'login' => $user->email,
            'password' => 'salah-password',
        ]);

        $this->assertGuest();
    }

    public function test_akun_siswa_ditolak_di_pintu_staf(): void
    {
        $user = User::factory()->create(['role' => Role::STUDENT]);

        $response = $this->post('/admin/login', [
            'login' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('login');
    }

    public function test_staf_bisa_logout(): void
    {
        $user = User::factory()->create(['role' => Role::ADMIN]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_rute_login_guru_tersembunyi_dari_halaman_siswa_dan_landing(): void
    {
        $siswaRes = $this->get('/siswa/login');
        $siswaRes->assertOk();
        $siswaRes->assertDontSee('Login Guru & Staf');
        $siswaRes->assertDontSee('/admin/login');

        $landingRes = $this->get('/');
        $landingRes->assertOk();
        $landingRes->assertDontSee('Login Guru & Staf');
        $landingRes->assertDontSee('/admin/login');
    }

    public function test_halaman_login_guru_menggunakan_judul_login_guru_khusus(): void
    {
        $response = $this->get('/admin/login');
        $response->assertOk();
        $response->assertSee('Login Staf / Pembina');
        $response->assertDontSee('Login Guru & Staf');
    }

    public function test_login_staf_menyimpan_auth_session_id_dan_fresh_login(): void
    {
        $user = User::factory()->create(['role' => Role::ADMIN]);

        $response = $this->post('/admin/login', [
            'login' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHas('auth_session_id');
        $response->assertSessionHas('fresh_login', true);
    }

    public function test_auth_session_exit_menghanguskan_sesi_staf_dan_mengarahkan_ke_login_guru(): void
    {
        $user = User::factory()->create(['role' => Role::ADMIN]);

        $response = $this->actingAs($user)->get('/auth/session-exit');

        $this->assertGuest();
        $response->assertRedirect('/admin/login');
        $response->assertSessionHas('status');
    }

    public function test_auth_session_exit_menghanguskan_sesi_siswa_dan_mengarahkan_ke_login_siswa(): void
    {
        $user = User::factory()->create(['role' => Role::STUDENT]);

        $response = $this->actingAs($user)->get('/auth/session-exit');

        $this->assertGuest();
        $response->assertRedirect('/siswa/login');
        $response->assertSessionHas('status');
    }

    public function test_halaman_terotentikasi_memiliki_header_anti_cache(): void
    {
        $user = User::factory()->create(['role' => Role::ADMIN]);

        $response = $this->actingAs($user)->get('/admin');

        $cacheControl = $response->headers->get('Cache-Control');
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('no-cache', $cacheControl);
        $this->assertStringContainsString('must-revalidate', $cacheControl);
        $response->assertHeader('Pragma', 'no-cache');
    }
}

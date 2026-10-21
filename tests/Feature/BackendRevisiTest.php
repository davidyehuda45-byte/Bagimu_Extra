<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\AdminLog;
use App\Models\Eskul;
use App\Models\Registration;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Support\LoginToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BackendRevisiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Uji pemisahan portal login: Pembina tidak boleh masuk via /admin/login.
     */
    public function test_pembina_cannot_login_at_admin_portal(): void
    {
        $pembina = User::create([
            'name' => 'Test Pembina Auth',
            'username' => 'test_pem_auth',
            'email' => 'test_pem_auth@test.local',
            'password' => Hash::make('password123'),
            'role' => Role::PEMBINA,
        ]);

        $response = $this->post(route('admin.login'), [
            'login' => 'test_pem_auth',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();

        // Pastikan kegagalan dicatat di AdminLog
        $this->assertDatabaseHas('admin_logs', [
            'action' => 'auth.admin_login_failed',
            'status' => 'FAILED',
        ]);
    }

    /**
     * Uji pemisahan portal login: Admin tidak boleh masuk via /pembina/login.
     */
    public function test_admin_cannot_login_at_pembina_portal(): void
    {
        $admin = User::create([
            'name' => 'Test Admin Auth',
            'username' => 'test_adm_auth',
            'email' => 'test_adm_auth@test.local',
            'password' => Hash::make('password123'),
            'role' => Role::ADMIN,
        ]);

        $response = $this->post(route('pembina.login'), [
            'login' => 'test_adm_auth',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();

        $this->assertDatabaseHas('admin_logs', [
            'action' => 'auth.pembina_login_failed',
            'status' => 'FAILED',
        ]);
    }

    /**
     * Uji login Admin berhasil via /admin/login.
     */
    public function test_admin_can_login_at_admin_portal(): void
    {
        $admin = User::create([
            'name' => 'Test Admin Ok',
            'username' => 'test_adm_ok',
            'email' => 'test_adm_ok@test.local',
            'password' => Hash::make('password123'),
            'role' => Role::ADMIN,
        ]);

        $response = $this->post(route('admin.login'), [
            'login' => 'test_adm_ok',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);

        $this->assertDatabaseHas('admin_logs', [
            'action' => 'auth.admin_login',
            'status' => 'SUCCESS',
            'actor_role' => 'ADMIN',
        ]);
    }

    /**
     * Uji login Pembina berhasil via /pembina/login.
     */
    public function test_pembina_can_login_at_pembina_portal(): void
    {
        $pembina = User::create([
            'name' => 'Test Pembina Ok',
            'username' => 'test_pem_ok',
            'email' => 'test_pem_ok@test.local',
            'password' => Hash::make('password123'),
            'role' => Role::PEMBINA,
        ]);

        $response = $this->post(route('pembina.login'), [
            'login' => 'test_pem_ok',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('pembina.dashboard'));
        $this->assertAuthenticatedAs($pembina);

        $this->assertDatabaseHas('admin_logs', [
            'action' => 'auth.pembina_login',
            'status' => 'SUCCESS',
            'actor_role' => 'PEMBINA',
        ]);
    }

    /**
     * Uji alur siswa: Login sukses langsung mengarah ke /siswa/aturan.
     */
    public function test_student_login_redirects_to_aturan(): void
    {
        $user = User::create([
            'name' => 'Siswa Test Alur',
            'username' => '999901',
            'email' => '999901@siswa.local',
            'role' => Role::STUDENT,
            'password' => Hash::make('secret'),
        ]);
        $token = LoginToken::issue($user);

        Student::create([
            'nis' => '999901',
            'nama' => 'Siswa Test Alur',
            'kelas' => 'X RPL 1',
            'jurusan' => \App\Enums\Jurusan::RPL,
            'gender' => \App\Enums\Gender::LAKI_LAKI,
            'status' => 'AKTIF',
            'user_id' => $user->id,
        ]);

        $response = $this->post(route('siswa.login'), [
            'nis' => '999901',
            'token' => $token,
        ]);

        $response->assertRedirect(route('siswa.aturan'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Uji alur pendaftaran siswa: Siswa dapat mendaftar dan token langsung di-revoke.
     */
    public function test_student_registration_revokes_token_and_redirects_to_sukses(): void
    {
        \App\Models\Periode::create([
            'periode' => '2026/2027',
            'status' => \App\Enums\PeriodeStatus::DIBUKA,
        ]);
        \App\Models\PendaftaranWindow::create([
            'periode' => '2026/2027',
            'buka' => true,
        ]);
        $eskul = Eskul::create([
            'name' => 'Futsal',
            'aktif' => true,
            'kuota' => 20,
            'kuota_aktif' => true,
        ]);

        $user = User::create([
            'name' => 'Siswa Test Reg',
            'username' => '999902',
            'email' => '999902@siswa.local',
            'role' => Role::STUDENT,
            'password' => Hash::make('secret'),
        ]);
        LoginToken::issue($user);

        Student::create([
            'nis' => '999902',
            'nama' => 'Siswa Test Reg',
            'kelas' => 'X RPL 1',
            'jurusan' => \App\Enums\Jurusan::RPL,
            'gender' => \App\Enums\Gender::LAKI_LAKI,
            'status' => 'AKTIF',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('siswa.daftar.store'), [
            'eskul_utama' => 'Futsal',
        ]);

        $response->assertRedirect(route('siswa.sukses'));

        $this->assertDatabaseHas('registrations', [
            'user_id' => $user->id,
            'eskul_utama' => 'Futsal',
            'status' => 'APPROVED',
        ]);

        $user->refresh();
        $this->assertNotNull($user->login_token_revoked_at);
    }

    /**
     * Uji Halaman Utama: Menampilkan akses login terpisah & tombol Detail Eskul.
     */
    public function test_home_page_displays_separated_logins_and_detail_button(): void
    {
        Eskul::create([
            'name' => 'Voli Uji',
            'aktif' => true,
            'kuota' => 15,
            'kuota_aktif' => true,
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Masuk Siswa');
        $response->assertSee('Masuk Pembina');
        $response->assertSee('Detail Eskul');
        $response->assertDontSee('Jadwal lengkap');
    }

    /**
     * Uji pembina dapat membuat kegiatan dengan jam mulai & jam selesai.
     */
    public function test_pembina_can_create_activity_with_start_and_end_time(): void
    {
        $eskul = Eskul::create([
            'name' => 'Basket Uji',
            'aktif' => true,
        ]);

        $pembina = User::create([
            'name' => 'Pembina Basket',
            'username' => 'pem_basket',
            'email' => 'pem_basket@test.local',
            'password' => Hash::make('password123'),
            'role' => Role::PEMBINA,
        ]);

        $eskul->update(['pembina_id' => $pembina->id]);

        $this->actingAs($pembina);

        $response = $this->post(route('pembina.kegiatan.store'), [
            'eskul_id' => $eskul->id,
            'tanggal' => '2026-10-25',
            'jenis' => 'LATIHAN',
            'jam_mulai' => '15:30',
            'jam_selesai' => '17:30',
            'agenda' => 'Latihan Dribbling dan Layup',
            'lokasi' => 'Lapangan Olahraga',
        ]);

        $response->assertRedirect(route('pembina.kegiatan.index'));

        $this->assertDatabaseHas('activity_plans', [
            'eskul_id' => $eskul->id,
            'agenda' => 'Latihan Dribbling dan Layup',
            'jam_mulai' => '15:30',
            'jam_selesai' => '17:30',
        ]);
    }

    /**
     * Uji pembina dapat mencatat presensi hadir melalui scan NIS Kartu Pelajar siswa.
     */
    public function test_pembina_can_scan_attendance_using_student_nis(): void
    {
        $eskul = Eskul::create([
            'name' => 'Futsal Uji Scan',
            'aktif' => true,
        ]);

        $pembina = User::create([
            'name' => 'Pembina Futsal Scan',
            'username' => 'pem_futsal_scan',
            'email' => 'pem_futsal_scan@test.local',
            'password' => Hash::make('password123'),
            'role' => Role::PEMBINA,
        ]);
        $eskul->update(['pembina_id' => $pembina->id]);

        $siswaUser = User::create([
            'name' => 'Budi Kartu Pelajar',
            'username' => '10888',
            'email' => '10888@siswa.local',
            'role' => Role::STUDENT,
        ]);

        $reg = Registration::create([
            'user_id' => $siswaUser->id,
            'periode' => '2026/2027',
            'kelas' => 'X RPL 1',
            'nis' => '10888',
            'jurusan' => \App\Enums\Jurusan::RPL,
            'gender' => \App\Enums\Gender::LAKI_LAKI,
            'usia' => 16,
            'eskul_utama' => $eskul->name,
            'status' => \App\Enums\Status::APPROVED,
        ]);

        $this->actingAs($pembina);

        $response = $this->postJson(route('pembina.scan.store', $eskul), [
            'kode' => '10888',
            'tanggal' => today()->toDateString(),
        ]);

        $response->assertOk();
        $response->assertJson([
            'ok' => true,
            'nama' => 'Budi Kartu Pelajar',
            'kelas' => 'X RPL 1',
        ]);

        $this->assertDatabaseHas('kehadiran', [
            'eskul_id' => $eskul->id,
            'registration_id' => $reg->id,
            'status' => \App\Enums\KehadiranStatus::HADIR,
        ]);
    }
}

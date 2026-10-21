<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminWaliKelasManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => Role::ADMIN]);
    }

    public function test_admin_bisa_mengakses_halaman_kelola_wali_kelas(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->get(route('admin.wali-kelas.index'));
        $response->assertOk();
        $response->assertSee('Kelola Wali Kelas');
    }

    public function test_admin_bisa_menambah_wali_kelas_dan_sinkron_ke_rombel(): void
    {
        $admin = $this->admin();
        $kelas = SchoolClass::create([
            'nama' => '10 RPL 1',
            'tingkat' => 10,
            'jurusan' => 'RPL',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.wali-kelas.store'), [
            'name' => 'Pak Joko Widodo, S.Pd.',
            'email' => 'joko@sekolah.sch.id',
            'username' => 'walijoko',
            'password' => 'password123',
            'school_class_id' => $kelas->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'email' => 'joko@sekolah.sch.id',
            'username' => 'walijoko',
            'role' => Role::WALI_KELAS->value,
        ]);

        $waliUser = User::where('email', 'joko@sekolah.sch.id')->first();
        $this->assertNotNull($waliUser);

        $kelas->refresh();
        $this->assertEquals($waliUser->id, $kelas->wali_kelas_id);
        $this->assertEquals('Pak Joko Widodo, S.Pd.', $kelas->wali_kelas_nama);
    }

    public function test_admin_bisa_mengubah_data_dan_pindah_rombel(): void
    {
        $admin = $this->admin();
        $kelasA = SchoolClass::create(['nama' => '10 RPL 1', 'tingkat' => 10, 'jurusan' => 'RPL']);
        $kelasB = SchoolClass::create(['nama' => '10 RPL 2', 'tingkat' => 10, 'jurusan' => 'RPL']);

        $wali = User::factory()->create([
            'name' => 'Wali Lama',
            'email' => 'walilama@sekolah.sch.id',
            'role' => Role::WALI_KELAS,
        ]);
        $kelasA->update(['wali_kelas_id' => $wali->id, 'wali_kelas_nama' => $wali->name]);

        $response = $this->actingAs($admin)->put(route('admin.wali-kelas.update', $wali), [
            'name' => 'Wali Baru',
            'email' => 'walibaru@sekolah.sch.id',
            'username' => 'walibaru',
            'school_class_id' => $kelasB->id,
        ]);

        $response->assertRedirect();

        $kelasA->refresh();
        $kelasB->refresh();

        $this->assertNull($kelasA->wali_kelas_id);
        $this->assertEquals($wali->id, $kelasB->wali_kelas_id);
        $this->assertEquals('Wali Baru', $kelasB->wali_kelas_nama);
    }

    public function test_admin_bisa_reset_sandi_wali_kelas(): void
    {
        $admin = $this->admin();
        $wali = User::factory()->create(['role' => Role::WALI_KELAS]);

        $response = $this->actingAs($admin)->put(route('admin.wali-kelas.password', $wali), [
            'password' => 'passwordBaru123',
        ]);

        $response->assertRedirect();
        $wali->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('passwordBaru123', $wali->password));
    }

    public function test_admin_bisa_hapus_wali_kelas_dan_rombel_dilepas(): void
    {
        $admin = $this->admin();
        $kelas = SchoolClass::create(['nama' => '10 RPL 1', 'tingkat' => 10, 'jurusan' => 'RPL']);
        $wali = User::factory()->create(['role' => Role::WALI_KELAS]);
        $kelas->update(['wali_kelas_id' => $wali->id, 'wali_kelas_nama' => $wali->name]);

        $response = $this->actingAs($admin)->delete(route('admin.wali-kelas.destroy', $wali));
        $response->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $wali->id]);
        $kelas->refresh();
        $this->assertNull($kelas->wali_kelas_id);
        $this->assertNull($kelas->wali_kelas_nama);
    }

    public function test_wali_kelas_bisa_login_dan_ke_dashboard_wali_kelas(): void
    {
        $wali = User::factory()->create([
            'email' => 'walilogin@sekolah.sch.id',
            'username' => 'walilogin',
            'password' => bcrypt('password123'),
            'role' => Role::WALI_KELAS,
        ]);

        $response = $this->post('/pembina/login', [
            'login' => 'walilogin',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('wali-kelas.dashboard'));
        $this->assertAuthenticatedAs($wali);
    }
}

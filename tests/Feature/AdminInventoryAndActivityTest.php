<?php

namespace Tests\Feature;

use App\Enums\ActivityJenis;
use App\Enums\Role;
use App\Models\ActivityPlan;
use App\Models\AdminLog;
use App\Models\Eskul;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInventoryAndActivityTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => Role::ADMIN]);
    }

    private function pembina(): User
    {
        return User::factory()->create(['role' => Role::PEMBINA]);
    }

    public function test_admin_dapat_mencari_dan_memfilter_riwayat_aktivitas(): void
    {
        $admin = $this->admin();

        AdminLog::create([
            'admin_id' => (string) $admin->id,
            'admin_email' => 'admin@sekolah.sch.id',
            'action' => 'eskuls.store',
            'target_type' => 'Eskul',
            'target_id' => '1',
            'detail' => 'Tambah eskul Bola Basket Molten.',
        ]);

        AdminLog::create([
            'admin_id' => (string) $admin->id,
            'admin_email' => 'operator@sekolah.sch.id',
            'action' => 'registrations.export',
            'target_type' => 'Registration',
            'target_id' => '2026/2027',
            'detail' => 'Export data pendaftar periode 2026/2027.',
        ]);

        // Search by keyword
        $resKeyword = $this->actingAs($admin)->get('/admin/logs?q=Molten');
        $resKeyword->assertOk();
        $resKeyword->assertSee('Bola Basket Molten');
        $resKeyword->assertDontSee('Export data pendaftar');

        // Filter by action
        $resAction = $this->actingAs($admin)->get('/admin/logs?aksi=registrations.export');
        $resAction->assertOk();
        $resAction->assertSee('Export data pendaftar');
        $resAction->assertDontSee('Bola Basket Molten');
    }

    public function test_admin_dapat_menghapus_eskul_tanpa_pendaftar(): void
    {
        $admin = $this->admin();
        $eskul = Eskul::create(['name' => 'Klub Robotik', 'aktif' => true]);

        // Pastikan tombol hapus terlihat di halaman index dan edit
        $resIndex = $this->actingAs($admin)->get('/admin/eskuls');
        $resIndex->assertOk();
        $resIndex->assertSee('Hapus');

        $resEdit = $this->actingAs($admin)->get("/admin/eskuls/{$eskul->id}/edit");
        $resEdit->assertOk();
        $resEdit->assertSee('Hapus Ekstrakurikuler');

        // Lakukan penghapusan
        $resDelete = $this->actingAs($admin)->delete("/admin/eskuls/{$eskul->id}");
        $resDelete->assertRedirect('/admin/eskuls');
        $this->assertDatabaseMissing('eskuls', ['id' => $eskul->id]);
    }

    public function test_admin_ditolak_menghapus_eskul_dengan_pendaftar(): void
    {
        $admin = $this->admin();
        $eskul = Eskul::create(['name' => 'Futsal', 'aktif' => true]);
        $studentUser = User::factory()->create(['role' => Role::STUDENT]);

        Registration::create([
            'user_id' => $studentUser->id,
            'nis' => '10001',
            'kelas' => 'X RPL 1',
            'jurusan' => 'RPL',
            'usia' => 16,
            'gender' => 'LAKI_LAKI',
            'eskul_utama' => 'Futsal',
            'status' => 'APPROVED',
            'periode' => '2026/2027',
            'jalur' => 'REGULER',
        ]);

        $resDelete = $this->actingAs($admin)->delete("/admin/eskuls/{$eskul->id}");
        $resDelete->assertSessionHasErrors('eskul');
        $this->assertDatabaseHas('eskuls', ['id' => $eskul->id]);
    }

    public function test_pembina_dapat_mengelola_kegiatan_eskulnya_saja(): void
    {
        $pembina = $this->pembina();
        $otherPembina = $this->pembina();

        $eskul = Eskul::create(['name' => 'Pramuka', 'pembina_id' => $pembina->id, 'aktif' => true]);
        $otherEskul = Eskul::create(['name' => 'PMR', 'pembina_id' => $otherPembina->id, 'aktif' => true]);

        // Pembina creates activity
        $responseCreate = $this->actingAs($pembina)->post('/pembina/kegiatan', [
            'eskul_id' => $eskul->id,
            'tanggal' => now()->toDateString(),
            'jenis' => ActivityJenis::LATIHAN->value,
            'agenda' => 'Latihan Tali Temali dan Semaphore',
            'tujuan' => 'Meningkatkan keterampilan kepramukaan',
            'lokasi' => 'Lapangan Utama',
        ]);
        $responseCreate->assertRedirect('/pembina/kegiatan');
        $this->assertDatabaseHas('activity_plans', [
            'eskul_id' => $eskul->id,
            'agenda' => 'Latihan Tali Temali dan Semaphore',
        ]);

        $kegiatan = ActivityPlan::where('agenda', 'Latihan Tali Temali dan Semaphore')->firstOrFail();

        // Other pembina cannot access this kegiatan
        $this->actingAs($otherPembina)->get("/pembina/kegiatan/{$kegiatan->id}")->assertForbidden();
        $this->actingAs($otherPembina)->delete("/pembina/kegiatan/{$kegiatan->id}")->assertForbidden();

        // Owner pembina can access
        $this->actingAs($pembina)->get("/pembina/kegiatan/{$kegiatan->id}")->assertOk()->assertSee('Tali Temali');

        // Owner pembina can delete
        $this->actingAs($pembina)->delete("/pembina/kegiatan/{$kegiatan->id}")->assertRedirect('/pembina/kegiatan');
        $this->assertDatabaseMissing('activity_plans', ['id' => $kegiatan->id]);
    }

    public function test_admin_dapat_mengelola_akun_staf_dan_reset_password(): void
    {
        $admin = $this->admin();
        $pembina = $this->pembina();

        // 1. Non-admin pembina cannot access admin/users
        $this->actingAs($pembina)->get('/admin/users')->assertForbidden();

        // 2. Admin can access admin/users index
        $responseIndex = $this->actingAs($admin)->get('/admin/users');
        $responseIndex->assertOk();
        $responseIndex->assertSee('Kelola Pembina Ekstrakurikuler');

        // 3. Admin creates a new staff account
        $responseStore = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Guru Olahraga',
            'email' => 'guru.olahraga@sekolah.sch.id',
            'username' => 'guru_olahraga',
            'password' => 'password1234',
            'role' => 'PEMBINA',
        ]);
        $responseStore->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'guru.olahraga@sekolah.sch.id',
            'username' => 'guru_olahraga',
            'role' => 'PEMBINA',
        ]);

        $newStaff = User::where('email', 'guru.olahraga@sekolah.sch.id')->firstOrFail();

        // 4. Admin updates staff role to operator
        $responseRole = $this->actingAs($admin)->put("/admin/users/{$newStaff->id}/role", [
            'role' => 'OPERATOR',
        ]);
        $responseRole->assertRedirect();
        $this->assertEquals(Role::OPERATOR, $newStaff->fresh()->role);

        // 5. Admin resets staff password
        $responsePassword = $this->actingAs($admin)->put("/admin/users/{$newStaff->id}/password", [
            'password' => 'newsecretpassword123',
        ]);
        $responsePassword->assertRedirect();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newsecretpassword123', $newStaff->fresh()->password));

        // 6. Admin deletes staff account
        $responseDelete = $this->actingAs($admin)->delete("/admin/users/{$newStaff->id}");
        $responseDelete->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $newStaff->id]);

        // 7. Security: Admin cannot delete or demote themselves
        $responseSelfRole = $this->actingAs($admin)->put("/admin/users/{$admin->id}/role", [
            'role' => 'OPERATOR',
        ]);
        $responseSelfRole->assertSessionHasErrors('role');

        $responseSelfDelete = $this->actingAs($admin)->delete("/admin/users/{$admin->id}");
        $responseSelfDelete->assertSessionHasErrors('user');
    }
}

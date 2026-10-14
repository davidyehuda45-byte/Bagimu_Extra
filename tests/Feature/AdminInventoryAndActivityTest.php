<?php

namespace Tests\Feature;

use App\Enums\ActivityJenis;
use App\Enums\ItemKondisi;
use App\Enums\Role;
use App\Models\ActivityPlan;
use App\Models\AdminLog;
use App\Models\Certificate;
use App\Models\Eskul;
use App\Models\InventoryItem;
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
            'action' => 'inventory.store',
            'target_type' => 'InventoryItem',
            'target_id' => '1',
            'detail' => 'Tambah barang Bola Basket Molten (10).',
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

    public function test_admin_dapat_mengelola_inventaris(): void
    {
        $admin = $this->admin();
        $eskul = Eskul::create(['name' => 'Basket', 'aktif' => true]);

        // Create
        $responseCreate = $this->actingAs($admin)->post('/admin/inventory', [
            'eskul_id' => $eskul->id,
            'nama' => 'Bola Basket Spalding',
            'jumlah' => 5,
            'kondisi' => 'BAIK',
            'keterangan' => 'Kondisi baru pengadaan 2026',
        ]);
        $responseCreate->assertRedirect('/admin/inventory');
        $this->assertDatabaseHas('inventory_items', [
            'nama' => 'Bola Basket Spalding',
            'jumlah' => 5,
            'kondisi' => 'BAIK',
        ]);

        $item = InventoryItem::where('nama', 'Bola Basket Spalding')->firstOrFail();

        // Update
        $responseUpdate = $this->actingAs($admin)->put("/admin/inventory/{$item->id}", [
            'eskul_id' => $eskul->id,
            'nama' => 'Bola Basket Spalding Pro',
            'jumlah' => 4,
            'kondisi' => 'RUSAK_RINGAN',
            'keterangan' => '1 bola bocor',
        ]);
        $responseUpdate->assertRedirect('/admin/inventory');
        $this->assertDatabaseHas('inventory_items', [
            'id' => $item->id,
            'nama' => 'Bola Basket Spalding Pro',
            'jumlah' => 4,
            'kondisi' => 'RUSAK_RINGAN',
        ]);

        // Delete
        $responseDelete = $this->actingAs($admin)->delete("/admin/inventory/{$item->id}");
        $responseDelete->assertRedirect('/admin/inventory');
        $this->assertDatabaseMissing('inventory_items', ['id' => $item->id]);
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

    public function test_admin_dapat_melihat_halaman_cetak_sertifikat(): void
    {
        $admin = $this->admin();
        $siswa = User::factory()->create(['name' => 'Budi Santoso', 'role' => Role::STUDENT]);

        $reg = Registration::create([
            'user_id' => $siswa->id,
            'periode' => '2026/2027',
            'kelas' => 'XI RPL 1',
            'nis' => '10099',
            'jurusan' => 'RPL',
            'usia' => 17,
            'gender' => 'LAKI_LAKI',
            'eskul_utama' => 'Robotik',
            'status' => 'APPROVED',
            'nilai' => 92,
            'predikat' => 'A',
            'verify_token' => 'token-sertifikat-12345',
        ]);

        $cert = Certificate::create([
            'registration_id' => $reg->id,
            'nomor' => 'SRT-2026-ABCDEF',
            'doc_version' => 1,
            'diterbitkan_oleh' => $admin->email,
        ]);

        $response = $this->actingAs($admin)->get("/admin/certificates/{$cert->id}/cetak");
        $response->assertOk();
        $response->assertSee('Budi Santoso');
        $response->assertSee('SRT-2026-ABCDEF');
        $response->assertSee('Robotik');
        $response->assertSee('Predikat A');
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
        $responseIndex->assertSee('Kelola Akun Staf');
        $responseIndex->assertSee($admin->email);

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

    public function test_admin_dapat_mencatat_peminjaman_dan_pengembalian_barang(): void
    {
        $admin = $this->admin();
        $eskul = Eskul::create(['name' => 'Badminton', 'aktif' => true]);

        $item = InventoryItem::create([
            'eskul_id' => $eskul->id,
            'nama' => 'Raket Yonex',
            'jumlah' => 3,
            'kondisi' => 'BAIK',
        ]);

        // 1. Borrow 2 items
        $responseBorrow = $this->actingAs($admin)->post("/admin/inventory/{$item->id}/loans", [
            'peminjam' => 'Ahmad Siswa',
            'jumlah' => 2,
            'tgl_pinjam' => now()->toDateString(),
        ]);
        $responseBorrow->assertRedirect();
        $this->assertDatabaseHas('inventory_loans', [
            'item_id' => $item->id,
            'peminjam' => 'Ahmad Siswa',
            'jumlah' => 2,
            'status' => 'DIPINJAM',
        ]);

        // 2. Cannot borrow more than available stock (only 1 left)
        $responseExcess = $this->actingAs($admin)->post("/admin/inventory/{$item->id}/loans", [
            'peminjam' => 'Budi Siswa',
            'jumlah' => 2,
            'tgl_pinjam' => now()->toDateString(),
        ]);
        $responseExcess->assertSessionHasErrors('jumlah');

        // 3. Return the loan
        $loan = $item->loans()->first();
        $responseReturn = $this->actingAs($admin)->put(route('admin.loans.kembali', $loan));
        $responseReturn->assertRedirect();
        $this->assertEquals('KEMBALI', $loan->fresh()->status);
    }
}

<?php

namespace Tests\Feature;

use App\Enums\Gender;
use App\Enums\Jurusan;
use App\Enums\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WaBlastTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => Role::ADMIN,
        ]);
    }

    public function test_guest_cannot_access_wa_blast(): void
    {
        $response = $this->post(route('admin.students.wa-blast'), [
            'mode' => 'all',
            'pesan' => 'Halo {nama}',
        ]);

        $response->assertRedirect(route('pembina.login'));
    }

    public function test_wa_blast_requires_fonnte_connected(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('admin.students.wa-blast'), [
            'mode' => 'all',
            'pesan' => 'Halo {nama}',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_admin_can_send_wa_blast_to_all_students_with_phone(): void
    {
        // 1. Simpan token Fonnte agar terhubung
        (new FonnteService)->saveToken('dummy-fonnte-token', [
            'device' => '081234567890',
            'name' => 'SMK Official',
            'device_status' => 'connect',
        ]);

        // 2. Buat siswa: 2 punya WA, 1 tidak punya WA
        $s1 = Student::create([
            'nis' => '10001',
            'nama' => 'Andi Pratama',
            'kelas' => '10 RPL 1',
            'jurusan' => Jurusan::RPL,
            'gender' => Gender::LAKI_LAKI,
            'whatsapp' => '081234567801',
            'status' => 'AKTIF',
        ]);

        $s2 = Student::create([
            'nis' => '10002',
            'nama' => 'Siti Aminah',
            'kelas' => '10 RPL 1',
            'jurusan' => Jurusan::RPL,
            'gender' => Gender::PEREMPUAN,
            'whatsapp' => '081234567802',
            'status' => 'AKTIF',
        ]);

        $s3 = Student::create([
            'nis' => '10003',
            'nama' => 'Budi Tanpa WA',
            'kelas' => '10 RPL 1',
            'jurusan' => Jurusan::RPL,
            'gender' => Gender::LAKI_LAKI,
            'whatsapp' => null,
            'status' => 'AKTIF',
        ]);

        // 3. Mock Fonnte API call
        Http::fake([
            'https://api.fonnte.com/send' => Http::response([
                'status' => true,
                'target' => ['6281234567801'],
            ], 200),
        ]);

        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('admin.students.wa-blast'), [
            'mode' => 'all',
            'pesan' => 'Halo {nama}, token login kamu adalah: {token}',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        // Pastikan Fonnte API dipanggil sebanyak 2 kali (hanya untuk siswa dengan nomor WA)
        Http::assertSentCount(2);

        Http::assertSent(function ($request) {
            return $request['target'] === '6281234567801'
                && str_contains($request['message'], 'Halo Andi Pratama');
        });

        Http::assertSent(function ($request) {
            return $request['target'] === '6281234567802'
                && str_contains($request['message'], 'Halo Siti Aminah');
        });

        // Pastikan token login otomatis diterbitkan untuk siswa yang belum punya token
        $s1->refresh();
        $this->assertNotNull($s1->user?->login_token);
    }

    public function test_admin_can_send_wa_blast_selected_students_only(): void
    {
        (new FonnteService)->saveToken('dummy-fonnte-token', [
            'device' => '081234567890',
            'name' => 'SMK Official',
        ]);

        $s1 = Student::create([
            'nis' => '10004',
            'nama' => 'Dewi Lestari',
            'kelas' => '11 DKV 1',
            'jurusan' => Jurusan::DKV,
            'gender' => Gender::PEREMPUAN,
            'whatsapp' => '081299990001',
            'status' => 'AKTIF',
        ]);

        $s2 = Student::create([
            'nis' => '10005',
            'nama' => 'Eko Prasetyo',
            'kelas' => '11 DKV 1',
            'jurusan' => Jurusan::DKV,
            'gender' => Gender::LAKI_LAKI,
            'whatsapp' => '081299990002',
            'status' => 'AKTIF',
        ]);

        Http::fake([
            'https://api.fonnte.com/send' => Http::response(['status' => true], 200),
        ]);

        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('admin.students.wa-blast'), [
            'mode' => 'selected',
            'ids' => [$s1->id], // Hanya Dewi
            'pesan' => 'Pengumuman khusus untuk {nama}',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        // Hanya dikirim ke Dewi
        Http::assertSentCount(1);
        Http::assertSent(function ($request) {
            return $request['target'] === '6281299990001'
                && str_contains($request['message'], 'Pengumuman khusus untuk Dewi Lestari');
        });
    }

    public function test_admin_can_access_dedicated_wa_blast_page(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->get(route('admin.wa-blast.index'));

        $response->assertOk();
        $response->assertSee('WhatsApp Blast');
        $response->assertSee('Koneksi Fonnte API Gateway');
    }

    public function test_admin_can_send_blast_via_dedicated_wa_blast_controller(): void
    {
        (new FonnteService)->saveToken('dummy-token', ['device' => '08123', 'device_status' => 'connect']);

        $s = Student::create([
            'nis' => '10006',
            'nama' => 'Rina Nose',
            'kelas' => '12 RPL 1',
            'jurusan' => Jurusan::RPL,
            'gender' => Gender::PEREMPUAN,
            'whatsapp' => '085200001111',
            'status' => 'AKTIF',
        ]);

        Http::fake([
            'https://api.fonnte.com/send' => Http::response(['status' => true], 200),
        ]);

        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('admin.wa-blast.send'), [
            'mode' => 'all',
            'pesan' => 'Halo {nama}, token Anda: {token}',
        ]);

        $response->assertRedirect(route('admin.wa-blast.index'));
        $response->assertSessionHas('status');

        Http::assertSent(function ($req) {
            return $req['target'] === '6285200001111' && str_contains($req['message'], 'Halo Rina Nose');
        });
    }
}


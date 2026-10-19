<?php

namespace Tests\Feature;

use App\Enums\Gender;
use App\Enums\Jurusan;
use App\Enums\PeriodeStatus;
use App\Enums\Role;
use App\Enums\Status;
use App\Mail\BuktiPendaftaranMail;
use App\Models\Periode;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class KirimBuktiPendaftaranMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_kirim_email(): void
    {
        $response = $this->post(route('siswa.status.kirim-email'), [
            'email' => 'tujuan@gmail.com',
        ]);

        $response->assertRedirect(route('siswa.login'));
    }

    public function test_student_can_send_bukti_pendaftaran_email(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'role' => Role::STUDENT,
            'email' => '10001@siswa.local',
        ]);

        $periode = Periode::create([
            'periode' => '2026/2027 Ganjil',
            'status' => PeriodeStatus::DIBUKA,
        ]);

        $registration = Registration::create([
            'user_id' => $user->id,
            'nis' => '10001',
            'periode' => $periode->periode,
            'kelas' => '10 RPL 1',
            'jurusan' => Jurusan::RPL,
            'gender' => Gender::LAKI_LAKI,
            'usia' => 16,
            'eskul_utama' => 'Futsal',
            'status' => Status::APPROVED,
        ]);

        $response = $this->actingAs($user)->post(route('siswa.status.kirim-email'), [
            'email' => 'orangtua@gmail.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        Mail::assertSent(BuktiPendaftaranMail::class, function ($mail) use ($registration) {
            return $mail->hasTo('orangtua@gmail.com')
                && $mail->registration->id === $registration->id;
        });
    }

    public function test_kirim_email_validation(): void
    {
        $user = User::factory()->create([
            'role' => Role::STUDENT,
        ]);

        $response = $this->actingAs($user)->post(route('siswa.status.kirim-email'), [
            'email' => 'invalid-email',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_email_mailable_renders_view_properly(): void
    {
        $user = User::factory()->create([
            'role' => Role::STUDENT,
            'name' => 'Budi Santoso',
        ]);

        $periode = Periode::create([
            'periode' => '2026/2027 Ganjil',
            'status' => PeriodeStatus::DIBUKA,
        ]);

        $registration = Registration::create([
            'user_id' => $user->id,
            'nis' => '10002',
            'periode' => $periode->periode,
            'kelas' => '10 RPL 1',
            'jurusan' => Jurusan::RPL,
            'gender' => Gender::LAKI_LAKI,
            'usia' => 16,
            'eskul_utama' => 'Futsal',
            'status' => Status::APPROVED,
        ]);

        $mailable = new BuktiPendaftaranMail($registration);
        $html = $mailable->render();

        $this->assertStringContainsString('Bukti Pendaftaran Ekstrakurikuler', $html);
        $this->assertStringContainsString('Budi Santoso', $html);
        $this->assertStringContainsString('Futsal', $html);
        $this->assertStringContainsString($registration->kode(), $html);
    }
}

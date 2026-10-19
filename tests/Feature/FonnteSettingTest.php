<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\AppSetting;
use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FonnteSettingTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => Role::ADMIN,
        ]);
    }

    public function test_guest_cannot_access_fonnte_settings(): void
    {
        $response = $this->postJson(route('admin.settings.fonnte.check'), [
            'token' => 'dummy-token',
        ]);

        $response->assertUnauthorized();
    }

    public function test_admin_can_check_valid_fonnte_token(): void
    {
        Http::fake([
            'https://api.fonnte.com/device' => Http::response([
                'status' => true,
                'device' => '081234567890',
                'name' => 'SMK Bagimu Negeriku',
                'device_status' => 'connect',
                'package' => 'Regular',
                'quota' => 1000,
                'expired' => '2027-01-01',
            ], 200),
        ]);

        $admin = $this->admin();

        $response = $this->actingAs($admin)->postJson(route('admin.settings.fonnte.check'), [
            'token' => 'valid-fonnte-token-12345',
        ]);

        $response->assertOk();
        $response->assertJson([
            'valid' => true,
            'device' => '081234567890',
            'name' => 'SMK Bagimu Negeriku',
            'device_status' => 'connect',
        ]);
    }

    public function test_admin_check_invalid_fonnte_token(): void
    {
        Http::fake([
            'https://api.fonnte.com/device' => Http::response([
                'status' => false,
                'reason' => 'invalid token',
            ], 200),
        ]);

        $admin = $this->admin();

        $response = $this->actingAs($admin)->postJson(route('admin.settings.fonnte.check'), [
            'token' => 'wrong-token',
        ]);

        $response->assertOk();
        $response->assertJson([
            'valid' => false,
        ]);
    }

    public function test_admin_can_save_fonnte_token_encrypted(): void
    {
        Http::fake([
            'https://api.fonnte.com/device' => Http::response([
                'status' => true,
                'device' => '081234567890',
                'name' => 'WA Official',
                'device_status' => 'connect',
            ], 200),
        ]);

        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('admin.settings.fonnte.save'), [
            'token' => 'secret-fonnte-key-999',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        // Pastikan tidak disimpan dalam plain text di database
        $rawInDb = AppSetting::get(FonnteService::SETTING_TOKEN);
        $this->assertNotEmpty($rawInDb);
        $this->assertNotEquals('secret-fonnte-key-999', $rawInDb);

        // Dekripsi harus menghasilkan key asli
        $this->assertEquals('secret-fonnte-key-999', Crypt::decryptString($rawInDb));

        // Pastikan halaman index WA Blast menyamarkan (mask) token
        $pageResponse = $this->actingAs($admin)->get(route('admin.wa-blast.index'));
        $pageResponse->assertOk();
        $pageResponse->assertDontSee('secret-fonnte-key-999');
        $pageResponse->assertSee('secr••••••••-999');
    }

    public function test_admin_can_disconnect_fonnte(): void
    {
        $service = new FonnteService();
        $service->saveToken('sample-token', ['device' => '08123']);

        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('admin.settings.fonnte.disconnect'));

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertEmpty(AppSetting::get(FonnteService::SETTING_TOKEN));
        $this->assertNull($service->getToken());
    }
}

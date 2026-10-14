<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DeployReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_tidak_ada_route_closure_agar_route_cache_bisa_jalan(): void
    {
        $routesDir = realpath(base_path('routes')) ?: base_path('routes');
        $closures = [];
        foreach (Route::getRoutes() as $route) {
            $uses = $route->getAction()['uses'] ?? null;
            if (! $uses instanceof Closure) {
                continue;
            }
            // Abaikan route internal framework (mis. /up, storage/*) yang
            // definisinya di vendor — route:cache menanganinya sendiri.
            $file = (string) (new \ReflectionFunction($uses))->getFileName();
            if (str_starts_with($file, $routesDir)) {
                $closures[] = implode('|', $route->methods()).' '.$route->uri();
            }
        }

        $this->assertSame([], $closures, 'Route closure di routes/* memblokir php artisan route:cache di production');
    }

    public function test_security_headers_terpasang(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_login_admin_dibatasi_rate_limit(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->post('/admin/login', ['login' => 'x', 'password' => 'salah']);
        }

        $this->post('/admin/login', ['login' => 'x', 'password' => 'salah'])
            ->assertStatus(429);
    }

    public function test_command_create_admin_membuat_akun(): void
    {
        $exit = Artisan::call('app:create-admin', [
            '--name' => 'Admin Prod',
            '--email' => 'prod@sekolah.sch.id',
            '--username' => 'adminprod',
            '--password' => 'rahasia123',
        ]);

        $this->assertSame(0, $exit);
        $this->assertDatabaseHas('users', ['email' => 'prod@sekolah.sch.id', 'role' => Role::ADMIN]);
    }

    public function test_command_create_admin_menolak_password_pendek(): void
    {
        $exit = Artisan::call('app:create-admin', [
            '--name' => 'X',
            '--email' => 'x@sekolah.sch.id',
            '--username' => 'xuser',
            '--password' => 'pendek',
        ]);

        $this->assertSame(1, $exit);
        $this->assertDatabaseMissing('users', ['email' => 'x@sekolah.sch.id']);
    }

    public function test_deploy_check_lolos_tanpa_kegagalan_fatal(): void
    {
        $exit = Artisan::call('deploy:check');

        // Warning (debug/url lokal) masih exit 0; yang gagal hanya bila ada [XX].
        $this->assertSame(0, $exit);
    }

    public function test_endpoint_kesehatan_up_merespons_200(): void
    {
        $this->get('/up')->assertOk();
    }
}

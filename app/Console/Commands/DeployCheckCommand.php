<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PDO;

/**
 * Pemeriksaan kesiapan production. Exit code 0 = siap (warning masih OK),
 * exit code 1 = ada yang FAIL dan harus diperbaiki sebelum deploy.
 *
 * Dipakai di server:  php artisan deploy:check
 */
class DeployCheckCommand extends Command
{
    protected $signature = 'deploy:check';

    protected $description = 'Periksa kesiapan aplikasi untuk deploy ke production';

    private int $failures = 0;

    private int $warnings = 0;

    public function handle(): int
    {
        $this->info('== Pemeriksaan kesiapan deploy ==');
        $this->newLine();

        $this->checkPhp();
        $this->checkEnv();
        $this->checkDatabase();
        $this->checkStorage();
        $this->checkAssets();
        $this->checkMail();
        $this->checkDrivers();

        $this->newLine();
        if ($this->failures > 0) {
            $this->error("HASIL: {$this->failures} GAGAL, {$this->warnings} peringatan. Perbaiki yang gagal sebelum deploy.");

            return self::FAILURE;
        }

        if ($this->warnings > 0) {
            $this->warn("HASIL: siap dengan {$this->warnings} peringatan (lihat di atas).");
        } else {
            $this->info('HASIL: siap deploy. Semua pemeriksaan lolos.');
        }

        return self::SUCCESS;
    }

    private function markOk(string $label): void
    {
        $this->line("  [OK] {$label}");
    }

    private function markWarn(string $label, string $saran = ''): void
    {
        $this->warnings++;
        $this->line("  [!!] {$label}".($saran !== '' ? " — {$saran}" : ''));
    }

    private function markFail(string $label, string $saran = ''): void
    {
        $this->failures++;
        $this->line("  [XX] {$label}".($saran !== '' ? " — {$saran}" : ''));
    }

    private function checkPhp(): void
    {
        $this->comment('PHP & ekstensi:');

        version_compare(PHP_VERSION, '8.2.0', '>=')
            ? $this->markOk('PHP '.PHP_VERSION.' (>= 8.2)')
            : $this->markFail('PHP '.PHP_VERSION.' terlalu lama', 'butuh PHP >= 8.2');

        $wajib = ['mbstring', 'fileinfo', 'openssl', 'pdo', 'tokenizer', 'xml', 'ctype', 'json', 'curl'];
        $hilang = array_values(array_filter($wajib, fn ($e) => ! extension_loaded($e)));
        empty($hilang)
            ? $this->markOk('Ekstensi wajib lengkap')
            : $this->markFail('Ekstensi hilang: '.implode(', ', $hilang), 'aktifkan di php.ini hosting');

        extension_loaded('zip')
            ? $this->markOk('Ekstensi zip aktif (impor .xlsx)')
            : $this->markFail('Ekstensi zip MATI', 'impor Excel .xlsx akan gagal — aktifkan extension=zip');

        extension_loaded('gd')
            ? $this->markOk('Ekstensi gd aktif')
            : $this->markWarn('Ekstensi gd mati', 'sebagian fitur gambar/QR bisa terganggu');

        $drivers = PDO::getAvailableDrivers();
        in_array('sqlite', $drivers, true) || in_array('mysql', $drivers, true)
            ? $this->markOk('Driver DB tersedia: '.implode(',', $drivers))
            : $this->markFail('Tidak ada driver pdo_sqlite/pdo_mysql', 'aktifkan di php.ini hosting');
    }

    private function checkEnv(): void
    {
        $this->comment('Konfigurasi environment:');

        $appKey = (string) config('app.key');
        $appKey !== '' && str_starts_with($appKey, 'base64:') && strlen($appKey) > 20
            ? $this->markOk('APP_KEY terisi')
            : $this->markFail('APP_KEY kosong/tidak valid', 'jalankan: php artisan key:generate');

        $isProd = app()->environment('production');

        if ($isProd && config('app.debug')) {
            $this->markFail('APP_DEBUG=true di production', 'set APP_DEBUG=false (detail error bocor ke pengunjung)');
        } elseif (config('app.debug')) {
            $this->markWarn('APP_DEBUG=true', 'wajib false saat deploy (APP_ENV=production)');
        } else {
            $this->markOk('APP_DEBUG=false');
        }

        $url = (string) config('app.url');
        if ($isProd && preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?/?$#', $url)) {
            $this->markFail("APP_URL masih {$url}", 'isi domain production, mis. https://eskul.sekolah.sch.id');
        } elseif (preg_match('#^https?://(localhost|127\.0\.0\.1)#', $url)) {
            $this->markWarn("APP_URL={$url}", 'ganti ke domain production saat deploy');
        } else {
            $this->markOk("APP_URL={$url}");
        }

        if ($isProd && ! str_starts_with($url, 'https://')) {
            $this->markWarn('APP_URL bukan https', 'pasang SSL lalu set APP_FORCE_HTTPS=true');
        }

        if ($isProd && ! config('app.force_https')) {
            $this->markWarn('APP_FORCE_HTTPS=false', 'set true setelah SSL aktif');
        }

        config('app.timezone') === 'Asia/Jakarta'
            ? $this->markOk('Timezone Asia/Jakarta')
            : $this->markWarn('Timezone '.config('app.timezone'), 'disarankan Asia/Jakarta (APP_TIMEZONE)');
    }

    private function checkDatabase(): void
    {
        $this->comment('Database:');

        try {
            DB::connection()->getPdo();
            $this->markOk('Koneksi DB ['.config('database.default').'] tersambung');
        } catch (\Throwable $e) {
            $this->markFail('Koneksi DB gagal: '.$e->getMessage(), 'cek DB_CONNECTION/host/nama/user/password');

            return;
        }

        if (config('database.default') === 'sqlite') {
            $path = (string) config('database.connections.sqlite.database');
            if ($path !== ':memory:' && ! is_file($path)) {
                $this->markFail("File SQLite tidak ada: {$path}", 'buat file kosong lalu migrate');

                return;
            }
        }

        try {
            $ran = DB::table('migrations')->count();
            $this->markOk("Tabel migrations terbaca ({$ran} migrasi tercatat)");
        } catch (\Throwable $e) {
            $this->markFail('Tabel migrations tidak ada', 'jalankan: php artisan migrate --force');

            return;
        }

        $wajib = ['users', 'students', 'sessions', 'cache', 'jobs', 'periodes', 'eskuls', 'registrations'];
        $hilang = array_values(array_filter($wajib, fn ($t) => ! Schema::hasTable($t)));
        empty($hilang)
            ? $this->markOk('Tabel inti lengkap')
            : $this->markFail('Tabel hilang: '.implode(', ', $hilang), 'jalankan: php artisan migrate --force');

        $admin = DB::table('users')->where('role', 'ADMIN')->count();
        $admin > 0
            ? $this->markOk("Akun admin tersedia ({$admin})")
            : $this->markWarn('Belum ada akun admin', 'buat via: php artisan app:create-admin (JANGAN db:seed di production)');
    }

    private function checkStorage(): void
    {
        $this->comment('Storage & direktori:');

        clearstatcache(true);

        foreach (['storage', 'storage/app', 'storage/logs', 'storage/framework', 'bootstrap/cache'] as $dir) {
            is_writable(base_path($dir))
                ? $this->markOk("{$dir} writable")
                : $this->markFail("{$dir} tidak writable", 'chmod 775 atau sesuaikan owner ke user web server');
        }

        $link = public_path('storage');
        file_exists($link) || is_dir($link) || is_link($link)
            ? $this->markOk('Link public/storage ada')
            : $this->markFail('Link public/storage hilang', 'jalankan: php artisan storage:link (wajib untuk bukti izin)');
    }

    private function checkAssets(): void
    {
        $this->comment('Aset frontend:');

        is_file(public_path('build/manifest.json'))
            ? $this->markOk('Aset Vite ter-build (public/build)')
            : $this->markFail('Aset Vite belum di-build', 'jalankan npm install && npm run build, lalu upload public/build');
    }

    private function checkMail(): void
    {
        $this->comment('Email:');

        $mailer = (string) config('mail.default');
        if (app()->environment('production') && in_array($mailer, ['log', 'array'], true)) {
            $this->markWarn("MAIL_MAILER={$mailer}", 'reset password & undangan tidak terkirim — set SMTP production');
        } else {
            $this->markOk("MAIL_MAILER={$mailer}");
        }
    }

    private function checkDrivers(): void
    {
        $this->comment('Session/Cache/Queue:');

        $this->markOk('SESSION_DRIVER='.config('session.driver'));
        $this->markOk('CACHE_STORE='.config('cache.default'));
        $this->markOk('QUEUE_CONNECTION='.config('queue.default'));

        if (config('queue.default') !== 'sync') {
            $this->markWarn('Queue '.config('queue.default').' butuh worker jalan', 'pastikan worker berjalan atau pakai sync');
        }
    }
}

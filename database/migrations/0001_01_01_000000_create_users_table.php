<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('role', 20)->default('STUDENT');
            $table->string('username', 30)->nullable()->unique();
            $table->string('password')->nullable();
            // Token login 8 digit (ala e-voting): kredensial cetak untuk siswa.
            $table->string('login_token', 10)->nullable()->unique();
            $table->string('login_token_hash', 64)->nullable();
            $table->dateTime('login_token_created_at')->nullable();
            $table->dateTime('login_token_revoked_at')->nullable();
            // Versi sesi: dinaikkan setiap reset password agar sesi lama hangus.
            $table->unsignedInteger('session_version')->default(0);
            // MFA TOTP untuk admin/operator.
            $table->text('totp_secret_enc')->nullable();
            $table->boolean('totp_enabled')->default(false);
            // Preferensi notifikasi.
            $table->boolean('notify_wa')->default(true);
            $table->boolean('notify_email')->default(true);
            // Status siswa.
            $table->string('student_status', 20)->default('AKTIF');
            // Kontak wali (opsional, untuk persetujuan kegiatan).
            $table->string('wali_nama', 100)->nullable();
            $table->string('wali_kontak', 20)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Kode reset kata sandi 6 digit: berlaku singkat, maks percobaan
        // tebak terbatas, sekali pakai.
        Schema::create('password_resets', function (Blueprint $table) {
            $table->id();
            $table->string('email', 191);
            $table->string('code_hash', 64);
            $table->dateTime('expires_at');
            $table->unsignedInteger('attempts')->default(0);
            $table->dateTime('used_at')->nullable();
            $table->timestamps();

            $table->index('email');
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('sessions');
    }
};

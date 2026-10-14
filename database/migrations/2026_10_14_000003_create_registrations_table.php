<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('periode', 9);
            $table->string('kelas', 50);
            $table->unsignedInteger('no_absen')->nullable();
            $table->string('nama_wali_kelas', 100)->nullable();
            $table->string('nis', 20)->nullable();
            $table->string('jurusan', 10);
            $table->unsignedInteger('usia');
            $table->string('gender', 15);
            $table->string('whatsapp', 20)->nullable();
            $table->string('eskul_utama', 100);
            $table->string('eskul_cadangan', 100)->nullable();
            $table->text('alasan')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->string('status', 20)->default('PENDING');
            $table->unsignedInteger('nilai')->nullable();
            $table->string('predikat', 2)->nullable();
            $table->text('catatan_nilai')->nullable();
            $table->string('verify_token', 64)->nullable()->unique();
            $table->dateTime('card_revoked_at')->nullable();
            $table->unsignedInteger('card_version')->default(1);
            $table->timestamps();

            $table->unique(['user_id', 'periode']);
            $table->index('periode');
            $table->index(['periode', 'nis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};

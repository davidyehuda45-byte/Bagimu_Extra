<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periodes', function (Blueprint $table) {
            $table->string('periode', 9)->primary();
            $table->string('nama', 100)->nullable();
            $table->string('tahun_ajaran', 9)->nullable();
            $table->string('semester', 20)->nullable();
            $table->dateTime('tgl_mulai')->nullable();
            $table->dateTime('tgl_selesai')->nullable();
            $table->string('status', 20)->default('DIBUKA');
            $table->unsignedInteger('max_eskul_per_siswa')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periodes');
    }
};

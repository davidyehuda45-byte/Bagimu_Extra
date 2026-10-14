<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Data induk siswa (master): acuan NIS untuk login & pendaftaran.
     * Diisi admin via CRUD / import CSV (sumber: Dapodik).
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 20)->unique();
            $table->string('nama', 100);
            $table->string('kelas', 50);
            $table->string('jurusan', 10);
            $table->string('gender', 15)->nullable();
            $table->unsignedInteger('no_absen')->nullable();
            $table->string('status', 20)->default('AKTIF');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('kelas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

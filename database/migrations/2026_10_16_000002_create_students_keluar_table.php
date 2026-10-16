<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students_keluar', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable()->index();
            $table->string('nis', 20)->index();
            $table->string('nisn', 20)->nullable();
            $table->string('nama', 100);
            $table->string('kelas_terakhir', 50);
            $table->string('jurusan', 20);
            $table->string('whatsapp', 20)->nullable();
            $table->string('status_keluar', 30)->default('DIKELUARKAN'); // DIKELUARKAN, PINDAH_SEKOLAH, DROPOUT, LAINNYA
            $table->text('alasan')->nullable();
            $table->date('tanggal_keluar');
            $table->string('diproses_oleh', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students_keluar');
    }
};
